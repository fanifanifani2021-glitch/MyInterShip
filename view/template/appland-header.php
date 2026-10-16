  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top header-transparent ">
    <div class="container d-flex align-items-center justify-content-between">


      <div class="row d-inline-flex flex-row flex-row d-flex justify-content-center justify-content-xl-center justify-content-sm-center">

        <!-- <div class="col-lg-4 col-md-10 text-center mt-1 form-inline d-none d-md-block">
          <a href="index.php" class="logo d-flex justify-content-sm-arround d-none d-md-block">
            <img class="d-none d-md-block" src="./assets/img/my_internship_logo.png" alt="" style="transform: scale(1.5); margin-left:15px;">
            <img src="./assets/img/my_internship_logo_small.png" alt="" style="transform: scale(1.5); margin-left:15px;">
            <span style="color:#374c99; font-size:12px">Polibatam Talent Hub</span>
          </a>
        </div> -->


        <div class="col-lg-4 col-md-10 text-center form-inline">
          <a href="index.php" class="logo d-flex" title="My Internship Logo">
            <img fetchpriority="high" rel="preload" as="image" src="./assets/img/my_internship_tulisan.png" alt="" style="transform: scale(1.5); ">
            <!-- <img src="./assets/img/my_internship_logo_small.png" alt="" style="transform: scale(1.5); margin-left:15px;"> -->
            <!-- <span style="color:#374c99; font-size:12px">Polibatam Talent Hub</span> -->
          </a>
        </div>


        <div class="col-lg-7 col-md-10 text-center mt-1 form-inline d-none d-xl-block">
          <a href="#" class="logo d-flex justify-content-sm-arround" title="#">

          </a>
        </div>

        <div class="col-lg-1 col-md-2 text-center mt-1 form-inline d-none d-xl-block">
          <a href="#" class="logo d-flex justify-content-sm-arround" title="#">

          </a>
        </div>

        <div class="col-lg-12 col-md-10 text-center mt-1 form-inline d-none d-xl-block">
          <a href="#" class="logo d-flex justify-content-sm-arround" title="Supported By Logo">
            <img src="./assets/img/my_internship_logo_small.png" alt="" style="transform: scale(1.5); margin-right:5px;">
            <img src="assets/img/ilo/ilo.png" alt="">
            <img src="assets/img/polibatam_logo.png" alt="" style="margin-right:1px;">
            <img src="assets/img/ilo/Sp.png" alt="">
            <img src="assets/img/ilo/bej.png" alt="">
          </a>
        </div>



      </div>


      <nav id="navbar" class="navbar">
        <ul>
        <?php if ( in_array( $_SESSION['role'], [NULL, '', '-'] )  ) : ?>

          <li><a class="nav-link scrollto" href="index.php#hero">Home</a></li>
        <?php else: ?>
          <li><a class="nav-link scrollto" href="index.php?page=home">Home</a></li>
        <?php endif; ?>

          <li><a class="nav-link scrollto" href="index.php#features">Features</a></li>
          <li><a class="nav-link scrollto <?php checker($routes, ['announcements'], 'active') ?>" href="index.php?page=announcements">Announcements</a></li>
          <li><a class="nav-link scrollto" href="index.php#faq">FAQ</a></li>
          <li><a class="nav-link scrollto" href="index.php#contact">Contact Us</a></li>
          <!-- <li><a class="getstarted scrollto" href="#login">Login</a></li> -->

          <!-- <li class="dropdown"><a href="#"><span>Login</span> <i class="bi bi-chevron-down"></i></a>
            <ul>
              <li><a href="index.php?page=express_login">Fake Login</a></li>
              <li>
                <div class="dropdown-divider"></div>
              </li>
              <li>
                <a href="index.php?page=login_cdc">CDC Login</a>
              </li>


              <li>
                <div class="dropdown-divider"></div>
              </li>
              <li>
                <a href="index.php?page=login_industri">Industry Login</a>
              </li>

              <li>
                <div class="dropdown-divider"></div>
              </li>

              <li>
                <a href="index.php?page=login">Polibatam Login</a>
              </li>
            </ul>
          </li> -->

          <?php if ( in_array( $_SESSION['role'], [NULL, '', '-'] )  ) : ?>

          <li class="dropdown"><a href="#"><span>Login</span> <i class="bi bi-chevron-down"></i></a>
            <ul>

              <?php if ($_SERVER['SERVER_NAME'] == 'localhost') : ?>
                <li><a href="index.php?page=express_login">Fake Login</a></li>
                <li>
                  <div class="dropdown-divider"></div>
                </li>
              <?php endif; ?>

              <li>
                <a href="index.php?page=login_cdc">CDC Login</a>
              </li>



              <li>
                <div class="dropdown-divider"></div>
              </li>
              <li>
                <a href="index.php?page=login_industri">Industry Login</a>
              </li>

              <?php if ($_SERVER['UNIVERSITY'] == 'POLIBATAM') : ?>


                <li>
                  <div class="dropdown-divider"></div>
                </li>
                <li>
                  <a href="index.php?page=login">Polibatam Login</a>
                </li>

              <?php else : ?>
                <li>
                  <div class="dropdown-divider"></div>
                </li>
                <li>
                  <a href="index.php?page=student_login">Student Login</a>
                </li>

                <li>
                  <div class="dropdown-divider"></div>
                </li>
                <li>
                  <a href="index.php?page=lecturer_login">Lecturer Login</a>
                </li>

              <?php endif; ?>





            </ul>
          </li>
          <?php else : ?>
            <li><a class="nav-link scrollto" href="index.php">Dashboard</a></li>

          <?php endif; ?>

        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->