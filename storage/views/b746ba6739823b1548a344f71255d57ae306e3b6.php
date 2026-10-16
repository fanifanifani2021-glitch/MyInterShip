<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo e($title ?? 'Page Moved'); ?> | <?php echo env('APP_NAME'); ?></title>
    <base href="<?php echo e(base_url()); ?>">
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="favicon.ico">
    
    <?php if(isset($newUrl)): ?>
        <link rel="canonical" href="<?php echo e($newUrl); ?>">
    <?php endif; ?>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        /* --- LAYOUT UTAMA --- */
        html, body { height: 100%; width: 100%; }
        
        body { 
            background: #f1f4f9; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex; align-items: center; justify-content: center; 
            min-height: 100vh;
        }

        .container {
            display: flex; justify-content: center; align-items: center;
            height: 100%; width: 100%;
        }

        /* --- CARD STYLE --- */
        .moved-card { 
            max-width: 550px; 
            width: 100%; 
            background: #fff; 
            padding: 40px; 
            border-radius: 16px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.05); 
            text-align: center; 
            position: relative; 
            overflow: hidden; 
            margin: 0 auto; 
            border: 1px solid #eef2f6;
        }
        
        /* Garis atas warna-warni */
        .moved-card::before { 
            content: ''; position: absolute; top: 0; left: 0; right: 0; 
            height: 6px; 
            background: linear-gradient(90deg, #36b9cc, #1d8cf8); 
        }

        /* --- LOGO STYLE --- */
        .moved-logo {
            max-width: 160px; /* Ukuran maksimal logo */
            height: auto;
            margin-bottom: 25px;
            display: inline-block;
        }

        /* --- CONTENT STYLE --- */
        .icon-box { 
            width: 70px; height: 70px; 
            background: #e3f2fd; color: #1d8cf8; 
            border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; 
            margin: 0 auto 20px; 
            font-size: 28px; 
            animation: bounceIn 0.8s; 
        }

        .title { font-size: 26px; font-weight: 700; color: #2d3748; margin-bottom: 10px; }
        .subtitle { font-size: 15px; color: #718096; margin-bottom: 25px; line-height: 1.6; }
        
        .redirect-box { 
            background: #f8fafc; border: 1px dashed #cbd5e0; 
            border-radius: 10px; padding: 15px; 
            margin-bottom: 25px; font-size: 14px; color: #4a5568; 
        }
        .redirect-url { 
            display: block; font-weight: 600; color: #1d8cf8; 
            margin-top: 5px; word-break: break-all; 
        }
        
        .btn-primary { 
            background-color: #1d8cf8; border-color: #1d8cf8; 
            padding: 10px 30px; font-weight: 600; border-radius: 50px; 
            box-shadow: 0 4px 15px rgba(29, 140, 248, 0.3); 
            transition: all 0.3s; 
        }
        .btn-primary:hover { 
            background-color: #0d7bf5; transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(29, 140, 248, 0.4);
        }
        
        @keyframes  bounceIn {
            0% { transform: scale(0); opacity: 0; }
            60% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        .countdown { font-weight: bold; color: #e74c3c; }
    </style>
</head>
<body>

    <div class="container">
        <div class="moved-card">
            
            <img src="<?php echo e(base_url('assets/jobcy/img/logo.png')); ?>" alt="Logo" class="moved-logo">

            <?php echo $__env->yieldContent('content'); ?>
            
            <div class="text-center mt-4 text-muted small">
                &copy; <?php echo e(date('Y')); ?> <?php echo env('APP_NAME'); ?>
            </div>
        </div>
    </div>

    <?php echo $__env->yieldPushContent('footer-scripts'); ?>
</body>
</html><?php /**PATH C:\laragon\www\myinternship\views/templates/moved.blade.php ENDPATH**/ ?>