<!DOCTYPE html>
<html lang="id">
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        /* --- GLOBAL STYLE --- */
        body {
            background: #f3f6fb;
            font-family: 'Poppins', system-ui, -apple-system, sans-serif; /* Menambahkan Poppins sebagai font utama */
            color: #1f2937;
            font-size: 16px; 
            margin: 0;
            padding: 0;
        }

        /* --- CARD UTAMA (.page) --- */
        .page {
            /* Desktop Default */
            width: 100%;
            max-width: 1400px; 
            margin: 40px auto;
            background: #fff;
            padding: 40px;
            
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-sizing: border-box;
        }

        /* --- LOGO & HEADER --- */
        .manual-logo {
            max-height: 64px;
            margin-bottom: 16px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 30px;
        }

        h1 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 8px;
            color: #111827;
        }

        /* --- TYPOGRAPHY --- */
        p, li { line-height: 1.6; color: #4b5563; }
        .table th, .table td { font-size: 14px; vertical-align: middle; }
        
        /* Badge style override */
        .badge {
            font-size: 0.85em;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 0.5em 0.8em;
        }

        /* --- FOOTER --- */
        footer {
            margin-top: 40px;
            font-size: 13px;
            color: #9ca3af;
            text-align: center;
            padding-bottom: 20px;
        }

        /* =========================================
           LOGIC STICKY HEADER (DESKTOP DEFAULT)
           ========================================= */
        
        /* 1. Header Utama (Thead) */
        .table-fixed-head thead th {
            position: sticky;
            top: 0;
            z-index: 1020; 
            background-color: #e9ecef; /* Abu-abu solid */
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            /* Cegah teks turun baris di desktop agar tinggi konsisten */
            white-space: nowrap; 
        }

        /* 2. Header Tanggal (Sub-Header) */
        .sticky-date-header td {
            position: sticky;
            top: 48px; /* Desktop top offset (sesuai tinggi header utama) */
            z-index: 1010; 
            
            background-color: #343a40; /* bg-dark solid */
            color: #fff;
            box-shadow: 0 2px 2px rgba(0,0,0,0.1);
            border: none;
        }

        /* =========================================
           RESPONSIVE MOBILE (MAX-WIDTH 768px)
           ========================================= */
        @media (max-width: 768px) {
            /* 1. Layout Full Screen */
            .page {
                max-width: 100%;
                margin: 0;
                border-radius: 0;
                border: none;
                padding: 15px 10px; /* Padding tipis */
            }

            /* 2. Resize Font */
            h1 { font-size: 20px !important; margin-bottom: 5px; }
            h2 { font-size: 18px !important; }
            .manual-logo { max-height: 36px; }
            
            /* 3. Tabel Font Lebih Kecil */
            .table th, .table td {
                font-size: 12px; 
                padding: 0.5rem 0.3rem; 
                white-space: normal; /* Biarkan teks wrap di HP */
            }

            /* --- PENYESUAIAN STICKY DI MOBILE --- */
            
            /* Header Utama */
            .table-fixed-head thead th {
                white-space: normal; /* Izinkan teks turun baris */
                font-size: 11px; /* Perkecil header */
                box-shadow: 0 1px 2px rgba(0,0,0,0.1); 
            }

            /* Header Tanggal */
            .sticky-date-header td {
                /* PENYESUAIAN PENTING: */
                /* Karena font header utama mengecil, tingginya berkurang. */
                /* Kurangi jarak top dari 48px (desktop) menjadi 38px */
                top: 70px; 
            }
            
            /* Container Scroll */
            .table-responsive {
                max-height: 80vh !important;
            }
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

    <div class="page">

        {{-- LOGO + TITLE --}}
        <div class="page-header">
            <img src="{{ base_url('assets/jobcy/img/logo.png') }}" class="manual-logo" alt="MyInternship">

            <h1>@yield('page-title')</h1>
        </div>

        {{-- CONTENT --}}
        @yield('content')

        <footer>
            © {{ date('Y') }} MyInternship
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

    {!! Notification::renderFooter() !!} 

    @include('partials.core-functionality')
    @include('partials.notifications')

</body>

</html>