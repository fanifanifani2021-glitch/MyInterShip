<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Page Title -->
    <title>{{ $title ?? 'User Manual' }} | @env('APP_NAME')</title>

    <!-- Rewrite Base Url -->
    <base href="{{ base_url() }}">

    <!-- Icons -->
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="icon" type="image/png" sizes="96x96" href="favicon-96x96.png">
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">

    <!-- PWA -->
    <link rel="manifest" href="site.webmanifest">
    <meta name="theme-color" content="#FFAD46">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="MyInternship">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $_SERVER['APP_DESCRIPTION'] ?>">
    <meta name="keywords" content="<?= $_SERVER['APP_KEYWORDS'] ?>">
    <meta name="author" content="<?= $_SERVER['APP_AUTHOR'] ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://polibatam.ac.id/">
    
    <!-- Meta for Security -->
    <meta name="csrf-token" content="<?= csrf_token() ?>">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    
    <!-- Fonts and icons -->
    <script src="./assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                "families": ["Lato:300,400,700,900"]
            }
            , custom: {
                "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"]
                , urls: ['./assets/css/fonts.min.css']
            }
            , active: function() {
                sessionStorage.fonts = true;
            }
        });

    </script>

    <!-- UpUp Service Worker -->
    <script src="./upup.min.js"></script>
    <script>
        if (typeof UpUp !== 'undefined') {
            UpUp.start({
                // 1. Halaman HTML Offline
                'content-url': "{{ base_url('offline.html') }}",

                // 2. DAFTAR ASET WAJIB (Logo, Font, CSS)
                // Masukkan path logo Anda di sini agar didownload oleh Service Worker
                'assets': [
                    "{{ base_url('assets/jobcy/img/logo.png') }}",
                    // Jika ada font atau gambar lain, masukkan di sini juga
                ],

                // 3. Lokasi Service Worker
                'service-worker-url': 'upup.sw.min.js',

                // 4. Versioning (Optional)
                'cache-version': '{{ date("Ymd") }}',
            });
        }
    </script>

    <!-- PWA -->
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }
    </script>



    <!-- =======================================================
    PAGE CSS FILES
    ======================================================== -->
    <!-- CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f3f6fb;
            font-family: "Lato", system-ui, -apple-system, sans-serif;
            color: #1f2937;
        }

        .manual-container {
            max-width: 920px;
            margin: 60px auto;
            background: #fff;
            padding: 48px 52px;
            border-radius: 12px;
            border: 1px solid #e5e8eb;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .05);
        }

        /* HEADER */
        .manual-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .manual-logo {
            width: 110px;
            margin-bottom: 12px;
        }

        .manual-title {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -0.6px;
        }

        .manual-subtitle {
            font-size: 16px;
            color: #6b7280;
            max-width: 640px;
            margin: 8px auto 0;
        }

        /* SECTION */
        .manual-section {
            margin-bottom: 36px;
        }

        .manual-section h4 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .manual-section p,
        .manual-section li {
            font-size: 15px;
            line-height: 1.7;
            color: #374151;
        }

        .manual-section ul {
            padding-left: 20px;
        }

        /* STEP BOX */
        .step-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #0d6efd;
            border-radius: 8px;
            padding: 16px 18px;
            margin-bottom: 12px;
        }

        .step-box b {
            display: block;
            margin-bottom: 4px;
        }

        /* NOTE */
        .manual-note {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 14px 16px;
            font-size: 14px;
            margin-bottom: 12px;
        }

        /* BUTTONS */
        .manual-actions {
            margin-top: 42px;
            display: flex;
            justify-content: center;
            gap: 14px;
        }

        .btn-manual {
            min-width: 130px;
            padding: 10px 18px;
            font-weight: 600;
            border-radius: 8px;
        }

        .elapsed {
            margin-top: 18px;
            font-size: 12px;
            text-align: center;
            color: #9ca3af;
        }

        .manual-grid {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 36px;
            margin-top: 40px;
        }

        .manual-content {
            min-width: 0;
        }

        .manual-header {
            text-align: center;
            margin-bottom: 20px;
        }


        /* TOC */
        .manual-toc {
            position: sticky;
            top: 40px;
            align-self: start;
        }

        .toc-title {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .toc-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .toc-list li {
            margin-bottom: 8px;
        }

        .toc-list a {
            font-size: 14px;
            color: #374151;
            text-decoration: none;
            padding-left: 8px;
            border-left: 2px solid transparent;
        }

        .toc-list a:hover {
            color: #0d6efd;
            border-left-color: #0d6efd;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .manual-grid {
                grid-template-columns: 1fr;
            }

            .manual-toc {
                position: relative;
                margin-bottom: 24px;
            }
        }

        body.dark {
            background: #0f172a;
            color: #e5e7eb;
        }

        body.dark .manual-container {
            background: #020617;
            border-color: #1e293b;
        }

        body.dark .manual-section p,
        body.dark .manual-section li {
            color: #cbd5f5;
        }

        body.dark .manual-toc a {
            color: #cbd5f5;
        }

        body.dark .manual-toc a:hover {
            color: #38bdf8;
        }

        body.dark .step-box {
            background: #020617;
            border-color: #1e293b;
        }

        body.dark .manual-note {
            background: #1e293b;
            border-color: #fbbf24;
        }


        /* BUTTON STYLE */
        .btn-custom {
            min-width: 120px;
            border-radius: 6px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 15px;
        }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* ===========================
        Manual Screenshot Styling
        =========================== */

        .manual-figure {
            margin: 20px 0 28px;
            text-align: center;
        }

        .manual-img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            background: #fff;
            padding: 6px;
        }

        .manual-caption {
            margin-top: 8px;
            font-size: 13px;
            color: #6b7280;
            font-style: italic;
        }

    </style>

    {{-- Page Custom CSS --}}
    @stack('head-scripts')

    {{-- Show Notification --}}
    @php
      if(function_exists('show_notification')) {
          show_notification();
      }
    @endphp

    {!! Notification::renderHead() !!}

</head>

<body>

    @php
    $time = "(Elapsed Time: " .
    number_format((float)(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']), 2) . "s)";
    @endphp

    <div class="manual-container">

        <!-- HEADER (FULL WIDTH) -->
        <div class="manual-header">
            <img src="{{ base_url('assets/jobcy/img/logo.png') }}" class="manual-logo" alt="MyInternship">

            <div class="manual-title">@yield('manual-title')</div>

            <div class="manual-subtitle">
                @yield('manual-subtitle')
            </div>

            <button class="btn btn-sm btn-light mt-3" id="toggleDark">
                🌙 Dark Mode
            </button>
        </div>

        <!-- ACTION BUTTON -->
        <div class="manual-actions">
            <a href="javascript:history.back()" class="btn btn-light btn-manual btn-custom btn-icon">
                <!-- SVG Back -->
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M5.854 4.146a.5.5 0 0 0-.708.708L7.293 7H1.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3z" />
                    <path d="M13.5 14a.5.5 0 0 0 .5-.5V2.5a.5.5 0 0 0-1 0v11a.5.5 0 0 0 .5.5z" />
                </svg>Back</a>
            <a href="{{ router('home') }}" class="btn btn-primary btn-manual btn-custom btn-icon">
                <!-- SVG Home -->
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 .5l6 6V15H2V6.5l6-6z" />
                    <path d="M7 9v6h2V9H7z" fill="#fff" />
                </svg>
                Home</a>
        </div>

        <!-- GRID: TOC + CONTENT -->
        <div class="manual-grid">

            <aside class="manual-toc">
                <div class="toc-title">On this page</div>
                <ul class="toc-list">
                    @yield('manual-toc')
                </ul>
            </aside>

            <main class="manual-content">
                @yield('manual-content')
            </main>

        </div>

        <!-- ACTION BUTTON -->
        <div class="manual-actions">
            <a href="javascript:history.back()" class="btn btn-light btn-manual btn-custom btn-icon">
                <!-- SVG Back -->
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M5.854 4.146a.5.5 0 0 0-.708.708L7.293 7H1.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3z" />
                    <path d="M13.5 14a.5.5 0 0 0 .5-.5V2.5a.5.5 0 0 0-1 0v11a.5.5 0 0 0 .5.5z" />
                </svg>Back</a>
            <a href="{{ router('home') }}" class="btn btn-primary btn-manual btn-custom btn-icon">
                <!-- SVG Home -->
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 .5l6 6V15H2V6.5l6-6z" />
                    <path d="M7 9v6h2V9H7z" fill="#fff" />
                </svg>
                Home</a>
        </div>

        <div class="elapsed">{{ $time }}</div>

    </div>

    <script>
        const btn = document.getElementById('toggleDark');

        if (localStorage.getItem('manual_dark') === '1') {
            document.body.classList.add('dark');
        }

        btn.addEventListener('click', () => {
            document.body.classList.toggle('dark');
            localStorage.setItem(
                'manual_dark',
                document.body.classList.contains('dark') ? '1' : '0'
            );
        });
    </script>

    {!! Notification::renderFooter() !!} 

    @include('partials.core-functionality')
    @include('partials.notifications')


</body>

</html>