<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Page Title -->
    <title>{{ $title ?? 'Error' }} | @env('APP_NAME')</title>

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
    * PAGE CSS FILES
    ======================================================== -->
    <!-- CSS Files -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <style>
        /* Custom Styles for Error Pages */
        body {
            background: #f1f4f9;
            font-family: "Poppins", sans-serif; /* Menggunakan font yang sudah di-load */
            color: #1f2937;
        }

        .error-container {
            max-width: 700px;
            margin: 60px auto;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            border: 1px solid #e5e8eb;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        /* LOGO */
        .error-logo {
            width: 120px;
            margin-bottom: 15px;
            opacity: 0;
            animation: fadeSlideDown 0.7s ease-out forwards;
        }

        /* HEADER */
        .error-header {
            text-align: center;
            margin-bottom: 32px;
            animation: fadeSlideDown 0.6s ease-out;
        }

        .error-title {
            font-size: 38px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
            color: #1c1c1c;
            animation: popIn 0.4s ease-out;
        }

        .error-subtitle {
            font-size: 17px;
            color: #5a5a5a;
            font-weight: 500;
        }

        .error-desc {
            color: #777;
            font-size: 14px;
            margin-top: 6px;
            margin-bottom: 25px;
        }

        /* SEPARATOR OR */
        .separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
            color: #9ca3af;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .separator::before,
        .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e5e7eb;
        }

        .separator:not(:empty)::before { margin-right: 15px; }
        .separator:not(:empty)::after { margin-left: 15px; }

        /* BUTTON STYLE FIXES */
        .btn {
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        /* ANIMATIONS */
        @keyframes fadeSlideDown {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes popIn {
            from { opacity: 0; transform: scale(0.94); }
            to { opacity: 1; transform: scale(1); }
        }

        .elapsed {
            margin-top: 25px;
            font-size: 11px;
            color: #aaa;
            font-family: monospace;
        }
    </style>

    @php
      $need_extra = true;
    @endphp
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
        $time = "(Elapsed Time: " . number_format((float)(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']), 4, '.', '') . "s)";
    @endphp

    <div class="container">
        <div class="error-container">

            <img src="{{ base_url('assets/jobcy/img/logo.png') }}" class="error-logo" alt="Logo">

            @yield('main-content')

            <div class="mt-4 d-flex justify-content-center">
                
                <div style="width: 100%; max-width: 340px;">

                   <div class="d-flex mb-3">
                        <a href="javascript:history.back()" class="btn btn-light border shadow-sm w-50 mr-2 d-flex align-items-center justify-content-center py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="mr-2" viewBox="0 0 16 16">
                                <path d="M5.854 4.146a.5.5 0 0 0-.708.708L7.293 7H1.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3z"/>
                                <path d="M13.5 14a.5.5 0 0 0 .5-.5V2.5a.5.5 0 0 0-1 0v11a.5.5 0 0 0 .5.5z"/>
                            </svg>
                            Back
                        </a>

                        <a href="{{ base_url() }}" class="btn btn-primary shadow-sm w-50 d-flex align-items-center justify-content-center py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="mr-2" viewBox="0 0 16 16">
                                <path d="M8 .5l6 6V15H2V6.5l6-6z"/>
                                <path d="M7 9v6h2V9H7z" fill="#fff"/>
                            </svg>
                            Home
                        </a>
                    </div>

                    @if ( $need_login )
                    <div class="separator">or</div>

                    <div class="d-flex">
                        
                        <a href="{{ base_url('auth/login') }}" class="btn btn-dark shadow-sm w-50 mr-2 d-flex align-items-center justify-content-center py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="mr-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
                                <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                            </svg>
                            Login
                        </a>

                        <a href="{{ base_url('/dashboard') }}" class="btn btn-info shadow-sm w-50 d-flex align-items-center justify-content-center py-2 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="mr-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3z"/>
                            </svg>
                            Dashboard
                        </a>

                    </div>
                    @endif

                </div>
            </div>

            <div class="elapsed">{{ $time }}</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

    {!! Notification::renderFooter() !!} 

    @include('partials.core-functionality')
    @include('partials.notifications')

    @stack('footer-scripts')

</body>
</html>