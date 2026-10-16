<?php defined('BASE_PATH') or header('Location: /'); ?>

<?php
session_start();

if (in_array($_SESSION['role'], [NULL, '', '-'])) {
  $index = 'index.php';
  $login = false;
} else {
  $index = 'index.php?page=home';
  $login = true;
}
?>

<header id="header" class="fixed-top d-flex align-items-center">
  <div class="container d-flex align-items-center justify-content-between">

    <div class="logo">
      <h1><a href="index.php"><img src="assets/jobcy/img/logo.png" alt="" class="img.png"></a></h1>
      <!-- Uncomment below if you prefer to use an image logo -->
      <!-- <a href="index.php"><img src="assets/jobcy/img/logo.png" alt="" class="img-fluid"></a>-->
    </div>

    <nav id="navbar" class="navbar">
      <ul>
        <li><a class="nav-link scrollto" href="<?= base_url($index . '#hero') ?>">Home</a></li>
        <li><a class="nav-link scrollto" href="<?= base_url($index . '#about') ?>">About</a></li>
        <li><a class="nav-link scrollto" href="#services">Features</a></li>
        <li><a class="nav-link scrollto <?php checker($routes, ['announcements'], 'active') ?>" href="<?= base_url('index.php?page=announcements') ?>">Announcements</a></li>
        <li><a class="nav-link scrollto" href="<?= base_url($index . '#faq') ?>">FAQ</a></li>
        <li><a class="nav-link scrollto" href="<?= base_url($index . '#contact') ?>">Contact Us</a></li>

        <?php if (!$login) : ?>
          <li class="dropdown"><a class="getstarted scrollto" href="#"><span>Login</span> <i class="bi bi-chevron-down"></i></a>
            <ul>

              <?php if ($_SERVER['SERVER_NAME'] == 'localhost') : ?>
                <li><a href="<?= base_url('index.php?page=express_login') ?>">Fake Login</a></li>
              <?php endif; ?>

              <!-- <li><a href="#signupModal" class="text-dark fw-medium fs-13" data-bs-toggle="modal">CDC Login</a></li> -->
              <li><a href="<?= base_url('index.php?page=login_cdc') ?>" class="text-dark fw-medium fs-13">CDC Login</a></li>
              <li><a href="<?= base_url('index.php?page=login_industri') ?>" class="text-dark fw-medium fs-13">Industry Login</a></li>
              <li><a href="<?= base_url('index.php?page=student_login') ?>" class="text-dark fw-medium fs-13">Student Login</a></li>
              <li><a href="<?= base_url('index.php?page=lecturer_login') ?>" class="text-dark fw-medium fs-13">Lecturer Login</a></li>
            </ul>
          </li>

        <?php else : ?>
          <li><a class="getstarted scrollto" href="index.php"><span>Dashboard</span></a>
          </li>
        <?php endif; ?>

      </ul>
      <i class="bi bi-list mobile-nav-toggle"></i>
    </nav><!-- .navbar -->

  </div>
</header><!-- End Header -->