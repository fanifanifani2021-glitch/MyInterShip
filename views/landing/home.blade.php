@extends('templates.home')

@section('head-scripts')
<style>
    /* --- Page Specific Styles (Subtle Rounded) --- */

    :root {
        --primary-color: #005b9f;
        --accent-color: #3498DB;
        --text-dark: #2c3e50;
        --text-muted: #6c757d;
        --bg-light: #f8faff;
        --radius-main: 5px;
        /* Radius kecil konsisten */
    }

    section {
        padding: 80px 0;
        overflow: hidden;
    }

    /* Section Title */
    .section-title h2 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 20px;
        position: relative;
        display: inline-block;
        color: var(--primary-color);
    }

    .section-title h2::after {
        content: '';
        position: absolute;
        display: block;
        width: 60px;
        height: 4px;
        background: var(--accent-color);
        bottom: -10px;
        left: calc(50% - 30px);
        border-radius: 2px;
        /* Sedikit rounded pada garis */
    }

    .section-title p {
        margin-top: 15px;
        color: var(--text-muted);
    }

    /* --- Hero Section --- */
    #hero {
        width: 100%;
        background: #fff;
        border-bottom: 1px solid #eee;
        padding: 100px 0 80px;
    }

    #hero h1 {
        margin: 0 0 15px 0;
        font-size: 46px;
        font-weight: 800;
        line-height: 1.2;
        color: var(--text-dark);
    }

    #hero h1 span {
        color: var(--accent-color);
    }

    #hero h2 {
        color: #555;
        margin-bottom: 40px;
        font-size: 18px;
        font-weight: 400;
        line-height: 1.6;
    }

    /* Hero Image */
    .hero-img img {
        border-radius: var(--radius-main);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        /* Soft shadow */
    }

    /* --- Clients --- */
    .clients {
        padding: 30px 0;
        background: #fff;
    }

    .clients img {
        opacity: 0.6;
        transition: 0.3s;
        filter: grayscale(100%);
    }

    .clients img:hover {
        opacity: 1;
        filter: grayscale(0%);
    }

    /* --- Stats / Counts (Card Style) --- */
    .counts {
        background: var(--bg-light);
        padding: 60px 0;
    }

    .count-box {
        padding: 30px;
        width: 100%;
        background: #fff;
        border-radius: var(--radius-main);
        /* Radius Kecil */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        text-align: center;
        transition: 0.3s;
        border: 1px solid #eee;
    }

    .count-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        border-color: var(--accent-color);
    }

    .count-box i {
        display: block;
        font-size: 42px;
        color: var(--accent-color);
        margin-bottom: 15px;
    }

    .count-box span {
        font-size: 36px;
        font-weight: 700;
        display: block;
        color: var(--text-dark);
    }

    .count-box p {
        margin: 0;
        font-size: 14px;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* --- Features / Services --- */
    .services .icon-box {
        padding: 40px 30px;
        background: #fff;
        border-radius: var(--radius-main);
        /* Radius Kecil */
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        transition: 0.3s;
        height: 100%;
        border: 1px solid #eee;
    }

    .services .icon-box:hover {
        transform: translateY(-5px);
        border-color: var(--accent-color);
        box-shadow: 0 5px 20px rgba(52, 152, 219, 0.15);
    }

    .services .icon {
        margin-bottom: 20px;
        font-size: 36px;
        color: var(--accent-color);
        background: #eaf6ff;
        display: inline-block;
        padding: 15px;
        border-radius: var(--radius-main);
        /* Icon Box kecil */
    }

    .services .title {
        font-weight: 700;
        margin-bottom: 15px;
        font-size: 18px;
    }

    .services .description {
        font-size: 14px;
        line-height: 24px;
        margin-bottom: 0;
        color: var(--text-muted);
    }

    /* --- About Us --- */
    .about img {
        border-radius: var(--radius-main);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .about .content ul {
        list-style: none;
        padding: 0;
    }

    .about .content ul li {
        padding-left: 30px;
        position: relative;
        margin-bottom: 15px;
    }

    .about .content ul li i {
        position: absolute;
        left: 0;
        top: 2px;
        font-size: 20px;
        color: var(--accent-color);
    }

    /* --- FAQ --- */
    .faq .faq-item {
        margin-bottom: 15px;
        padding: 20px;
        background: #fff;
        border-radius: var(--radius-main);
        border: 1px solid #eee;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
    }

    .faq .faq-item h4 {
        font-size: 17px;
        font-weight: 700;
        margin-bottom: 10px;
        color: var(--primary-color);
    }

    .faq .faq-item i {
        color: var(--accent-color);
        margin-right: 10px;
    }

    .faq .faq-item p {
        margin-bottom: 0;
        color: var(--text-muted);
        font-size: 14px;
    }

    .faq .faq-item:hover {
        border-color: #ddd;
    }


    /* --- Floating Maintenance Toast --- */
    .maintenance-toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        width: 350px;
        background: #fff;
        border-left: 5px solid var(--accent-color);
        border-radius: var(--radius-main);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        padding: 15px;
        display: flex;
        flex-direction: column;
        animation: slideInRight 0.5s ease-out;
    }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    .maintenance-toast .toast-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .maintenance-toast .toast-title {
        color: var(--primary-color);
        font-weight: 700;
        font-size: 15px;
        margin: 0;
    }

    .maintenance-toast .close-toast {
        background: none;
        border: none;
        color: #ccc;
        font-size: 18px;
        cursor: pointer;
        line-height: 1;
    }

    .maintenance-toast .close-toast:hover { color: #333; }

    .maintenance-toast .toast-body {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.5;
    }

    .maintenance-toast .countdown-badge {
        margin-top: 10px;
        background: #eaf6ff;
        color: var(--accent-color);
        padding: 4px 10px;
        border-radius: 4px;
        font-weight: 700;
        font-size: 12px;
        display: inline-block;
        align-self: flex-start;
    }
</style>
@endsection

@section('content')
@php
/*
|--------------------------------------------------------------------------
| Upcoming Maintenance Logic
|--------------------------------------------------------------------------
*/
$upcomingMaintenance = Maintenance::getUpcoming(1);
$upcoming = !empty($upcomingMaintenance) ? $upcomingMaintenance[0] : null;

// Counts Logic (Tetap Sama)
$sql_student = "SELECT COUNT(nim) AS count_student FROM student_internship";
$stmt_student = $_SERVER['PDO']->prepare($sql_student);
$stmt_student->execute();
$count_student = $stmt_student->fetch(PDO::FETCH_ASSOC)['count_student'] ?? 0;

$sql_company = "SELECT COUNT(id_company) AS count_company FROM company";
$stmt_company = $_SERVER['PDO']->prepare($sql_company);
$stmt_company->execute();
$count_company = $stmt_company->fetch(PDO::FETCH_ASSOC)['count_company'] ?? 0;

$sql_kampus = "SELECT COUNT(id_kampus) AS count_kampus FROM tb_kampus";
$stmt_kampus = $_SERVER['PDO']->prepare($sql_kampus);
$stmt_kampus->execute();
$count_kampus = $stmt_kampus->fetch(PDO::FETCH_ASSOC)['count_kampus'] ?? 0;

$sql_lecturer = "SELECT COUNT(nim_nik_unit) AS count_lecturer FROM lecturer";
$stmt_lecturer = $_SERVER['PDO']->prepare($sql_lecturer);
$stmt_lecturer->execute();
$count_lecturer = $stmt_lecturer->fetch(PDO::FETCH_ASSOC)['count_lecturer'] ?? 0;
@endphp

@if($upcoming)
<div class="maintenance-toast" id="maintenanceToast">
    <div class="toast-header">
        <p class="toast-title"><i class="bi bi-tools me-2"></i>Upcoming Maintenance</p>
        <button class="close-toast" onclick="document.getElementById('maintenanceToast').style.display='none'">&times;</button>
    </div>
    <div class="toast-body">
        MyInternship will undergo <strong>{{ htmlspecialchars($upcoming->title) }}</strong> on 
        <span class="text-dark fw-bold">{{ date('d M Y, H:i', strtotime($upcoming->start_date)) }} WIB</span>.
    </div>
    <div class="countdown-badge">
        Starts in: <span id="home-countdown">-</span>
    </div>
</div>
@endif

<section id="hero">
    <div class="container">
        <div class="row align-items-center">


            <div class="col-lg-6 pt-5 pt-lg-0 order-2 order-lg-1">
                <h1 data-aos="fade-up">Manage your internship with <br><span>MyInternship</span></h1>
                <h2 data-aos="fade-up" data-aos-delay="400">
                    Industrial Learning Management System for Politeknik Negeri Batam. Structured, Transparent, and
                    Professional.
                </h2>
                <div class="mt-4" data-aos="fade-up" data-aos-delay="800">
                    <a href="{{ router('register.student.index') }}" class="btn-modern btn-primary-modern me-2">
                        Student Register
                    </a>

                    @if(empty($_SESSION['role']))
                    <a href="{{ router('login.index') }}" class="btn-modern btn-outline-modern">
                        Login Portal
                    </a>
                    @else
                    <a href="{{ router('dashboard') }}" class="btn-modern btn-outline-modern">
                        Go to Dashboard
                    </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 order-1 order-lg-2 hero-img text-center" data-aos="fade-left" data-aos-delay="200">
                <img src="assets/jobcy/img/myinternship-hero.png" class="img-fluid" alt="MyInternship Platform"
                    style="max-height: 450px;">
            </div>


        </div>

    </div>
</section>
<main id="main">

    <section id="clients" class="clients">
        <div class="container">
            <div class="row justify-content-center align-items-center g-4">
                <div class="col-lg-2 col-md-4 col-6 text-center">
                    <img src="assets/jobcy/img/clients/support-0.png" class="img-fluid px-3" alt="Tut Wuri"
                        data-aos="zoom-in" data-aos-delay="100">
                </div>
                <div class="col-lg-2 col-md-4 col-6 text-center">
                    <img src="assets/jobcy/img/clients/support-2.png" class="img-fluid px-3" alt="Polibatam"
                        data-aos="zoom-in" data-aos-delay="200">
                </div>
                <div class="col-lg-2 col-md-4 col-6 text-center">
                    <img src="assets/jobcy/img/clients/support-5.png" class="img-fluid px-3" alt="Vokasi"
                        data-aos="zoom-in" data-aos-delay="300">
                </div>
                <div class="col-lg-2 col-md-4 col-6 text-center">
                    <img src="assets/jobcy/img/clients/support-1.png" class="img-fluid px-3" alt="Kampus Merdeka"
                        data-aos="zoom-in" data-aos-delay="400">
                </div>
            </div>
        </div>
    </section>

    <section id="counts" class="counts">
        <div class="container">
            <div class="section-title text-center mb-5" data-aos="fade-up">
                <h2>Our Impact</h2>
                <p>Growing ecosystem connecting education and industry</p>
            </div>

            <div class="row gy-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="count-box">
                        <i class="bi bi-mortarboard"></i>
                        <span data-purecounter-start="0" data-purecounter-end="<?= $count_student ?>"
                            data-purecounter-duration="1.5" class="purecounter"></span>
                        <p>Students</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="count-box">
                        <i class="bi bi-building"></i>
                        <span data-purecounter-start="0" data-purecounter-end="<?= $count_company ?>"
                            data-purecounter-duration="1.5" class="purecounter"></span>
                        <p>Industries</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="count-box">
                        <i class="bi bi-people"></i>
                        <span data-purecounter-start="0" data-purecounter-end="<?= $count_lecturer ?>"
                            data-purecounter-duration="1.5" class="purecounter"></span>
                        <p>Mentors</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="count-box">
                        <i class="bi bi-bank"></i>
                        <span data-purecounter-start="0" data-purecounter-end="<?= $count_kampus ?>"
                            data-purecounter-duration="1.5" class="purecounter"></span>
                        <p>Partners</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="services" style="background-color: #fff;">
        <div class="container">

            <div class="section-title text-center" data-aos="fade-up">
                <h2>Key Features</h2>
                <p>Tools to support your journey</p>
            </div>

            <div class="row gy-4 mt-3">
                <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
                    <div class="icon-box">
                        <div class="icon"><i class="bi bi-search"></i></div>
                        <h4 class="title">Internship Discovery</h4>
                        <p class="description">Find relevant internship opportunities from our curated list of industry
                            partners.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="200">
                    <div class="icon-box">
                        <div class="icon"><i class="bi bi-card-checklist"></i></div>
                        <h4 class="title">Easy Registration</h4>
                        <p class="description">Seamless registration process with digital document verification for
                            independent or regular programs.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="300">
                    <div class="icon-box">
                        <div class="icon"><i class="bi bi-laptop"></i></div>
                        <h4 class="title">Digital Administration</h4>
                        <p class="description">Manage Logbooks, Attendance, and Monthly Reports digitally. Real-time
                            monitoring.</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section id="about" class="about section-bg" style="background-color: var(--bg-light);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <img src="assets/jobcy/img/goals.png" class="img-fluid" alt="About MyInternship">
                </div>
                <div class="col-lg-6 pt-4 pt-lg-0 content" data-aos="fade-left">
                    <div class="section-title text-start">
                        <h2>About Us</h2>
                    </div>
                    <p class="text-muted mb-4" style="line-height: 1.8;">
                        MyInternship is an internship manager application that manages all stages of internships, from
                        the registration process, implementation, to assessment.
                    </p>
                    <ul>
                        <li><i class="bi bi-check-circle-fill"></i> <strong>Integrated Ecosystem:</strong> Connecting
                            Students, Lecturers, and Industry.</li>
                        <li><i class="bi bi-check-circle-fill"></i> <strong>Real-time Monitoring:</strong> Track student
                            progress periodically.</li>
                        <li><i class="bi bi-check-circle-fill"></i> <strong>Assessment:</strong> Standardized evaluation
                            for soft skills and hard skills.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section id="faq" class="faq" style="background-color: #fff;">
        <div class="container">

            <div class="section-title text-center" data-aos="fade-up">
                <h2>FAQ</h2>
                <p>Frequently Asked Questions</p>
            </div>

            <div class="row justify-content-center mt-4">
                <div class="col-lg-10">

                    <div class="faq-item" data-aos="fade-up" data-aos-delay="100">
                        <h4><i class="bi bi-question-circle-fill me-2"></i> Who can use this platform?</h4>
                        <p class="ps-5">
                            Primarily for <strong>Polibatam Students</strong> (5th Semester+),
                            <strong>Lecturers</strong>, and <strong>Industry Partners</strong>.
                        </p>
                    </div>

                    <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
                        <h4><i class="bi bi-building me-2"></i> How does a Company register?</h4>
                        <p class="ps-5">
                            New Industry partners need to register via <strong>Polibatam CDC</strong>. Once verified,
                            HRD will receive account credentials.
                        </p>
                    </div>

                    <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                        <h4><i class="bi bi-journal-text me-2"></i> What about Logbooks?</h4>
                        <p class="ps-5">
                            Students input daily logbooks via dashboard. Industry Mentors can validate these entries
                            digitally.
                        </p>
                    </div>

                    <div class="faq-item" data-aos="fade-up" data-aos-delay="400">
                        <h4><i class="bi bi-award-fill me-2"></i> Certificate?</h4>
                        <p class="ps-5">
                            Yes. After finishing the final report and assessments, you can download your official
                            Completion Certificate.
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </section>

    @include('partials.contact-us')

</main>
@endsection

@section('footer-scripts')
@if($upcoming)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startTime = new Date("{{ $upcoming->start_date }}").getTime();
        const countdownEl = document.getElementById("home-countdown");

        const updateTimer = () => {
            const now = new Date().getTime();
            const distance = startTime - now;

            if (distance < 0) {
                countdownEl.innerHTML = "NOW";
                // Opsional: Sembunyikan toast jika sudah mulai
                // document.getElementById('maintenanceToast').style.display='none';
                return;
            }

            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

            countdownEl.innerHTML = `${hours}h ${minutes}m`;
        };

        setInterval(updateTimer, 60000); // Update per menit untuk hemat resource
        updateTimer();
    });
</script>
@endif
@endsection