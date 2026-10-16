<?php

class Router
{
    private static $routes = [];
    private static $groupStack = [];
    private static $namedRoutes = [];
    private static $csrfExcept = [];

    private static $middlewareHandlers = [
        'csrf' => 'handleCsrf',
        'api'  => 'handleApiToken'
    ];

    /* ----------------------
     * JSON Response Helper
     * ---------------------*/
    public static function json($statusCode = 200, $message = '', $data = null, $extra = [])
    {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
        }

        $payload = [
            'status'  => ($statusCode >= 200 && $statusCode < 300) ? 1 : 0,
            'code'    => $statusCode,
            'message' => $message
        ];

        if ($data !== null) {
            $payload['data'] = $data;
        }

        if (!empty($extra)) {
            foreach ($extra as $k => $v) {
                $payload[$k] = $v;
            }
        }

        echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /* ----------------------
     * Client IP Helper
     * ---------------------*/
    public static function getClientIp()
    {
        $keys = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR'
        ];

        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ipList = explode(',', $_SERVER[$key]);
                return trim($ipList[0]);
            }
        }

        return '0.0.0.0';
    }

    /* ----------------------------------
     * Router Logger (PHP 7 Compatible)
     * ---------------------------------- */
    public static function log($channel, array $data = [])
    {
        try {
            $basePath = BASE_DIR . '/logs/router';

            if (!is_dir($basePath)) {
                @mkdir($basePath, 0777, true);
            }

            $channel = preg_replace('/[^A-Za-z0-9_\-]/', '_', $channel);
            $date = date('Y-m-d');
            $file = rtrim($basePath, '/') . '/' . $channel . '_' . $date . '.log';

            $log = [
                'timestamp'  => date('Y-m-d H:i:s'),
                'code'       => $data['code'] ?? null,
                'message'    => $data['message'] ?? null,
                'route'      => $data['route'] ?? null,
                'method'     => $_SERVER['REQUEST_METHOD'] ?? '',
                'ip'         => self::getClientIp(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'file'       => $data['file'] ?? null,
                'line'       => $data['line'] ?? null,
                'get'        => $_GET ?? [],
                'post'       => $_POST ?? [],
                'raw_input'  => file_get_contents('php://input') ?: ''
            ];

            file_put_contents(
                $file,
                json_encode(
                    $log,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                ) . PHP_EOL . PHP_EOL,
                FILE_APPEND | LOCK_EX
            );

        } catch (\Throwable $e) {
            // silent fail
        }
    }

    public static function getApiToken()
    {
        // 0. getallheaders() fallback (PALING PENTING)
        if (function_exists('getallheaders')) {
            $headers = getallheaders();

            if (!empty($headers['Authorization'])) {
                if (stripos($headers['Authorization'], 'Bearer ') === 0) {
                    return trim(substr($headers['Authorization'], 7));
                }
            }

            if (!empty($headers['X-API-TOKEN'])) {
                return trim($headers['X-API-TOKEN']);
            }
        }

        // 1. Authorization via $_SERVER
        $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if ($auth && stripos($auth, 'Bearer ') === 0) {
            return trim(substr($auth, 7));
        }

        // 2. X-API-TOKEN header via $_SERVER
        if (!empty($_SERVER['HTTP_X_API_TOKEN'])) {
            return trim($_SERVER['HTTP_X_API_TOKEN']);
        }

        // 3. Legacy POST token
        if (!empty($_POST['token'])) {
            Router::log('API_Warning', [
                'reason' => 'Legacy POST token used',
                'route'  => Router::fullRoute()
            ]);

            return trim($_POST['token']);
        }

        return '';
    }

    public static function fullRoute()
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            ? 'https' : 'http';

        return $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    private static function handleApiToken()
    {
        header('Content-Type: application/json; charset=utf-8');

        $token = self::getApiToken();

        if ($token === '') {
            self::apiError(401, 'Token Not Found !', [
                'reason' => 'Missing API token'
            ]);

            self::log('API_Denied', [
                'reason' => 'Token Not Found'
            ]);
        }

        /** @var PDO $db */
        $db = $_SERVER['PDO'];

        $stmt = $db->prepare("
            SELECT *
            FROM access_token
            WHERE token = :token
            LIMIT 1
        ");
        $stmt->execute([':token' => $token]);

        if ($stmt->rowCount() !== 1) {
            self::apiError(401, 'Invalid Token !', [
                'token' => $token
            ]);

            self::log('API_Denied', [
                'reason' => 'Invalid Token',
                'token'  => $token
            ]);
        }

        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ((int)$tokenData['token_revoke'] !== 0) {
            self::apiError(401, 'Token has been revoked', [
                'token' => $token
            ]);

            self::log('API_Denied', [
                'reason' => 'Revoked Token',
                'token'  => $token
            ]);
        }

        // IP restriction
        $allowedRaw = strtolower(trim($tokenData['allowed_address']));
        $clientIp  = self::getClientIp();

        if ($allowedRaw !== 'any') {
            $allowed = array_map('trim', explode(';', $tokenData['allowed_address']));
            $trusted = ['127.0.0.1', '::1'];

            if (!in_array($clientIp, $trusted, true) && !in_array($clientIp, $allowed, true)) {
                self::apiError(401, 'Access from this address is not allowed', [
                    'ip' => $clientIp
                ]);

                self::log('API_Denied', [
                    'reason' => 'Unauthorized IP',
                    'ip'     => $clientIp,
                    'token'  => $token
                ]);
            }
        }

        // update usage
        $stmt = $db->prepare("
            UPDATE access_token
            SET connection_count = connection_count + 1,
                last_connection  = :last
             WHERE token = :token
        ");
        $stmt->execute([
            ':last' => getBrowser(), // Pastikan fungsi getBrowser() tersedia di helper lain
            ':token'   => $tokenData['token']
        ]);

        $_SESSION['API_TOKEN'] = $tokenData;
    }

    /* ----------------------
     * Cek Request is API helpers
     * ---------------------*/
    private static function isApiRequest(): bool
    {
        return
            str_starts_with(self::normalize($_SERVER['REQUEST_URI']), 'api/') ||
            isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');
    }

    /* ----------------------
     * CSRF helpers
     * ---------------------*/
    public static function csrfToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }

        $_SESSION['token'] = $_SESSION['_token'];
        return $_SESSION['_token'];
    }

    public static function csrfExcept($routes)
    {
        foreach ($routes as $r) {
            self::$csrfExcept[] = self::normalize($r);
        }
    }

    private static function handleCsrf()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // only for POST requests
        if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $current = self::normalize(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        // skip exceptions
        if (in_array($current, self::$csrfExcept, true)) {
            return;
        }

        // skip API prefixes
        if (preg_match('#^api/#', $current)) {
            return;
        }

        // 1. Ambil Token dari Session
        $sessionToken = $_SESSION['_token'] ?? null;

        // 2. Ambil Token dari Client
        $clientToken = null;
        
        if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $clientToken = $_SERVER['HTTP_X_CSRF_TOKEN'];
        } 
        elseif (isset($_SERVER['HTTP_X_XSRF_TOKEN'])) {
            $clientToken = $_SERVER['HTTP_X_XSRF_TOKEN'];
        }
        elseif (isset($_POST['_token'])) {
            $clientToken = $_POST['_token'];
        } 
        elseif (isset($_POST['token'])) {
            $clientToken = $_POST['token'];
        }

        // 3. Validasi
        if (!$sessionToken || !$clientToken || !hash_equals($sessionToken, $clientToken)) {
            self::handleError(419, "CSRF token mismatch", $current); 
            exit;
        }
    }

    /* ----------------------
     * Route registration
     * ---------------------*/
    public static function get($pattern, $callback)
    {
        return self::addRoute('GET', $pattern, $callback);
    }

    public static function post($pattern, $callback)
    {
        return self::addRoute('POST', $pattern, $callback);
    }

    public static function put($pattern, $callback)
    {
        return self::addRoute('PUT', $pattern, $callback);
    }

    public static function delete($pattern, $callback)
    {
        return self::addRoute('DELETE', $pattern, $callback);
    }

    private static function addRoute($method, $pattern, $callback)
    {
        $route = [
            'pattern' => $pattern,
            'callback' => $callback,
            'middleware' => [],
            'additionalMiddleware' => [],
            'exceptMiddleware'  => [],
            'name' => '',
            'meta' => [],
        ];

        $fullPrefix = '';

        foreach (self::$groupStack as $g) {
            if (!empty($g['prefix'])) {
                $fullPrefix .= rtrim($g['prefix'], '/') . '/';
            }

            if (!empty($g['middleware'])) {
                $route['middleware'] = array_merge($route['middleware'], (array)$g['middleware']);
            }

            if (!empty($g['exceptMiddleware'])) {
                $route['exceptMiddleware'] = array_merge($route['exceptMiddleware'], (array)$g['exceptMiddleware']);
            }

            if (!empty($g['additionalMiddleware'])) {
                $route['additionalMiddleware'] = array_merge(
                    $route['additionalMiddleware'],
                    (array)$g['additionalMiddleware']
                );
            }

            if (!empty($g['name'])) {
                $route['name'] .= $g['name'];
            }

            if (!empty($g['meta']) && is_array($g['meta'])) {
                $route['meta'] = array_merge($route['meta'], $g['meta']);
            }
        }

        $route['pattern'] = $fullPrefix . ltrim($route['pattern'], '/');
        $route['pattern'] = self::normalize($route['pattern']);

        self::$routes[$method][] = &$route;

        return new class($route) {
            private $routeRef;

            public function __construct(&$r)
            {
                $this->routeRef = &$r;
            }

            public function name($name)
            {
                $this->routeRef['name'] .= $name;
                Router::registerNamedRoute($this->routeRef);
                return $this;
            }

            public function middleware($mw)
            {
                $arr = is_array($mw) ? $mw : [$mw];
                $this->routeRef['middleware'] = array_merge($this->routeRef['middleware'], $arr);
                return $this;
            }

            public function exceptMiddleware($mw)
            {
                $arr = is_array($mw) ? $mw : [$mw];
                $this->routeRef['exceptMiddleware'] = array_merge($this->routeRef['exceptMiddleware'], $arr);
                return $this;
            }

            public function additionalMiddleware($mw)
            {
                $arr = is_array($mw) ? $mw : [$mw];
                $this->routeRef['additionalMiddleware'] = array_merge(
                    $this->routeRef['additionalMiddleware'],
                    $arr
                );
                return $this;
            }

            public function meta(array $meta)
            {
                if (!is_array($this->routeRef['meta'])) {
                    $this->routeRef['meta'] = [];
                }

                $this->routeRef['meta'] = array_merge(
                    $this->routeRef['meta'],
                    $meta
                );
                return $this;
            }
        };
    }

    public static function registerNamedRoute($route)
    {
        if (!empty($route['name'])) {
            self::$namedRoutes[$route['name']] = $route['pattern'];
        }
    }

    public static function group($attributes, $callback)
    {
        self::$groupStack[] = $attributes;
        call_user_func($callback);
        array_pop(self::$groupStack);
    }

    /* ----------------------
     * Dispatch
     * ---------------------*/
    public static function dispatch($uri, $method)
    {
        // =====================================================================
        // SECURITY CHECKS
        // =====================================================================
        
        $hostHeader = $_SERVER['HTTP_HOST'] ?? '';
        $hostname   = explode(':', $hostHeader)[0];

        $isLocalhost = (
            $hostname === 'localhost' || 
            $hostname === '127.0.0.1' || 
            $hostname === '::1' ||
            strpos($hostname, '192.168.') === 0 || 
            strpos($hostname, '10.0.') === 0
        );

        if (!$isLocalhost) {
            // A. Block Direct IP Access
            if (filter_var($hostname, FILTER_VALIDATE_IP)) {
                return self::handleError(403, "Direct IP access is not allowed. Please access via https://myinternship.id", $uri);
            }

            // B. Force HTTPS
            $isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
                || (isset($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], 'https') !== false);

            if (!$isHttps) {
                $redirectUrl = 'https://' . $hostHeader . $_SERVER['REQUEST_URI'];
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: ' . $redirectUrl);
                exit;
            }
        }
        // =====================================================================

        $uri = self::normalize($uri);

        if ($method === 'POST') {
            to_old();
        }

        // 1. Method Override Support
        if ($method === 'POST' && isset($_POST['_method'])) {
            $override = strtoupper($_POST['_method']);
            if (in_array($override, ['PUT', 'DELETE', 'PATCH'])) {
                $method = $override;
            }
        }

        // 2. Handle HEAD Requests
        $is_head = false;
        if ($method === 'HEAD') {
            $method = 'GET';
            $is_head = true;
        }

        // 3. Cek ketersediaan Method di Route
        if (!isset(self::$routes[$method])) {
            return self::handleError(405, "Method Not Allowed", $uri);
        }

        // 4. Loop Routes
        foreach (self::$routes[$method] as $routeItem) {

            $pattern = self::normalize($routeItem['pattern']);

            // ==========================================
            // 🔥 OPTIONAL PARAMETER SUPPORT
            // ==========================================

            // 1. Ubah Optional Parameter: /add/{?id}  -->  /add(?:/([^/]+))?
            $regex = preg_replace('#/\{\?([a-zA-Z0-9_]+)\}#', '(?:/([^/]+))?', $pattern);

            // 2. Ubah Required Parameter: /user/{id}  -->  /user/([^/]+)
            $regex = preg_replace('#\{([a-zA-Z0-9_]+)\}#', '([^/]+)', $regex);

            $regex = "#^" . $regex . "$#";

            if (preg_match($regex, $uri, $matches)) {

                array_shift($matches); // remove full match

                // Ambil nama param (support {?name} dan {name})
                preg_match_all('#\{\??([a-zA-Z0-9_]+)\}#', $pattern, $paramNames);

                $params = [];
                foreach ($paramNames[1] as $i => $name) {
                    $params[$name] = isset($matches[$i]) && $matches[$i] !== '' ? urldecode($matches[$i]) : null;
                }

                // Middleware Handling
                if (!empty($routeItem['middleware'])) {
                    foreach ($routeItem['middleware'] as $mw) {
                        if (!empty($routeItem['exceptMiddleware']) && in_array($mw, (array)$routeItem['exceptMiddleware'])) {
                            continue; 
                        }

                        if (isset(self::$middlewareHandlers[$mw])) {
                            $handler = self::$middlewareHandlers[$mw];
                            call_user_func([__CLASS__, $handler]);
                        }
                    }
                }

                if (!empty($routeItem['additionalMiddleware'])) {
                    foreach ($routeItem['additionalMiddleware'] as $mw) {
                        if ($mw instanceof Closure) {
                            $mw(); continue;
                        }
                        if (is_callable($mw)) {
                            call_user_func($mw); continue;
                        }
                        if (is_string($mw) && isset(self::$middlewareHandlers[$mw])) {
                            $handler = self::$middlewareHandlers[$mw];
                            call_user_func([__CLASS__, $handler]); continue;
                        }
                        self::handleError(500, 'Invalid additional middleware');
                    }
                }
    
                // Execute Callback
                ob_start();
                $response = call_user_func($routeItem['callback'], $params);
                $content = ob_get_clean(); 

                if ($is_head) {
                    if (!headers_sent()) {
                        header('Content-Length: ' . strlen($content));
                    }
                    exit; 
                } else {
                    echo $content;
                    return $response;
                }
            }
        }

        return self::handleError(404, "Route not found", $uri);
    }

    /* ----------------------
     * Named route helper
     * ---------------------*/
    public static function url($name, $params = [])
    {
        if (!isset(self::$namedRoutes[$name])) {
            throw new Exception("Named route '{$name}' not found");
        }

        $uri = self::$namedRoutes[$name];

        // 1. Ganti parameter yang dikirim user
        foreach ($params as $k => $v) {
            // Cek format wajib {key}
            if (strpos($uri, "{" . $k . "}") !== false) {
                $uri = str_replace("{" . $k . "}", $v, $uri);
            }
            // Cek format opsional {?key}
            elseif (strpos($uri, "{?" . $k . "}") !== false) {
                $uri = str_replace("{?" . $k . "}", $v, $uri);
            }
        }

        // 2. HAPUS sisa parameter opsional yang TIDAK diisi user
        // Regex ini mencari pola "/{?kata}" dan menghapusnya
        $uri = preg_replace('#/\{\?([a-zA-Z0-9_]+)\}#', '', $uri);

        // (Opsional) Hapus parameter opsional jika berada di paling depan (tanpa slash)
        $uri = preg_replace('#\{\?([a-zA-Z0-9_]+)\}#', '', $uri);

        return self::baseUrl(ltrim($uri, '/'));
    }
    public static function url2($name, $params = [])
    {
        if (!isset(self::$namedRoutes[$name])) {
            throw new Exception("Named route '{$name}' not found");
        }

        $uri = self::$namedRoutes[$name];

        foreach ($params as $k => $v) {
            $uri = str_replace("{" . $k . "}", $v, $uri);
        }

        return self::baseUrl(ltrim($uri, '/'));
    }

    /* ----------------------
     * Error handler untuk API
     * ---------------------*/
    private static function apiError( int $code, string $message, array $debug = [] ) 
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');

        $response = [
            'status'  => 0,
            'code'    => $code,
            'message' => $message
        ];

        if (env('SERVER_MODE') === 'DEVELOPMENT') {
            $response['debug'] = array_merge([
                'ip'     => self::getClientIp(),
                'method' => $_SERVER['REQUEST_METHOD'] ?? '',
                'route'  => self::fullRoute()
            ], $debug);
        }

        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }


    public static function currentUrl() {
        return self::fullRoute();
    }

    /**
     * Check if current request is an AJAX request
     * @return bool
     */
    public static function isAjax(): bool
    {
        // normal check X-Requested-With
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }

        // tambahan: jika URL mengandung /api/, anggap AJAX
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            return true;
        }

        return false;
    }


    /* ----------------------
     * Error handler untuk Web
     * ---------------------*/
    public static function handleError($code, $message, $route = '')
    {
        http_response_code($code);

        if (empty($route)) {
            $route = self::normalize(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? 'Direct Access';

        $error = [
            'timestamp' => date('Y-m-d H:i:s'),
            'code'      => $code,
            'message'   => $message,
            'route'     => $route,
            'referer'   => $referer,
            'method'    => $_SERVER['REQUEST_METHOD'] ?? '',
            'ip'        => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent'=> $_SERVER['HTTP_USER_AGENT'] ?? '',
            'file'      => debug_backtrace()[0]['file'] ?? '',
            'line'      => debug_backtrace()[0]['line'] ?? '',
            'get'       => $_GET,
            'post'      => $_POST,
            'raw_input' => file_get_contents('php://input')
        ];

        // logging
        Router::log('Router_Error', [
            'code'    => $code,
            'message' => $message,
            'route'   => $route,
            'referer' => $error['referer'],
            'file'    => $error['file'],
            'line'    => $error['line']
        ]);

        // handle AJAX request
        if (self::isAjax()) {
            header('Content-Type: application/json');
            if (env('SERVER_MODE') !== 'PRODUCTION') {
                echo json_encode($error, JSON_PRETTY_PRINT);
            } else {
                echo json_encode([
                    'code' => $code,
                    'message' => $message
                ]);
            }
            exit;
        }

        // handle normal request
        if (env('SERVER_MODE') !== 'PRODUCTION') {
            renderView('errors.error_debug', ['error' => $error]);
        } else {
            if ($code >= 400 && $code < 500) {
                renderView("errors.error_{$code}", ['message' => $message]);
            } else {
                renderView('errors.error_500', ['message' => 'Internal Server Error']);
            }
        }

        die();
    }


    /* ----------------------
     * Utility
     * ---------------------*/
    public static function baseUrl($path = '')
    {
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

        $scriptName = isset($_SERVER['SCRIPT_NAME']) 
            ? str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']) 
            : '/';

        $base = rtrim($scheme . '://' . $host . $scriptName, '/');

        return $base . '/' . ltrim($path, '/');
    }


    private static function normalize($route)
    {
        $route = preg_replace('#/+#', '/', $route);
        if ($route !== '/') $route = rtrim($route, '/');
        return ltrim($route, '/');
    }

    public static function apiDocs()
    {
        $docs = [];

        foreach (self::$routes as $method => $routes) {
            foreach ($routes as $route) {

                $pattern = self::normalize($route['pattern']);

                if (strpos($pattern, 'api/') !== 0) {
                    continue;
                }

                $docs[] = [
                    'method'      => $method,
                    'endpoint'    => '/' . $pattern,
                    'name'        => $route['name'] ?? '',
                    'middleware'  => $route['middleware'] ?? [],
                    'meta'        => $route['meta'] ?? []
                ];
            }
        }

        return $docs;
    }
}