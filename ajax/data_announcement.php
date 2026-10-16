<?php
header('Access-Control-Allow-Origin: *');
error_reporting(false);
session_start();

include 'ajax-init.php';


$limit = '4';
$page = 1;
if ($_POST['page'] > 1) {
  $start = (($_POST['page'] - 1) * $limit);
  $page = $_POST['page'];
} else {
  $start = 0;
}

$query = "SELECT * FROM announcement WHERE `status` NOT IN ('Draft') ";


// $query .= 'ORDER BY id_mhs ASC ';

$filter_query = $query . 'LIMIT ' . $start . ', ' . $limit . '';

$statement = $koneksi_pdo->prepare($query);
$statement->execute();
$total_data = $statement->rowCount();

$statement = $koneksi_pdo->prepare($filter_query);
$statement->execute();
$result = $statement->fetchAll();
$total_filter_data = $statement->rowCount();

if ($total_data > 0) {

$output = '
<!-- Right content -->

    <!-- Featured_job_start -->

        <div class="container">
            <!-- Count of Job list Start -->
            <div class="row mb-3">
                <div class="col-lg-12">
                    <div class="count-job mb-35">
                        <span>' . $total_data . ' Announcement</span>
                        <!-- Select job items start -->
                        
                        <!--  Select job items End-->
                    </div>
                </div>
            </div>
';

} else {
  $output = '';
}

if ($total_data > 0) {
  foreach ($result as $row) {

    $have = '';

    if ( $row['have_attachment'] == 1 ) {
      $have = '<p><a download target="_blank" href="viewer.php?src=attachment_announcement&id=' .base64_encode($row['id_announcement']). '"><i class="fas fa-download mr-1"></i> Download Resources</a></p>';
    }

    // src="./upload/announcement/' .$row['id_announcement']. '/' .$row['picture']. '" 
    $nim = base64_encode($row['id_mhs']);
    $skill = preg_replace('/(,)(?=[^\s])/', ', ', $row['skill']);
    $output .= '
              <!-- single-job-content -->                        
              <div class="card mb-3 form-inline card-body">

                <div class="row">

                <div class="col-md-5">

                  <img class="img-fluid img-thumbnail" 
                    
                    src="viewer.php?src=announcement&id=' .base64_encode($row['id_announcement']). '" 
                    alt="Card image cap">
                </div>

                <div class="col-md-7">                
                  <a href="index.php?page=announcement_detail&p='.$row['id_announcement'].'"><h3 class="card-title text-bold">'.$row['title'].'</h3></a>
                  
                  <p class="card-text"><small class="text-muted">
                  <i class="fas fa-clock mr-1"></i> '.date('M d, Y', strtotime($row['created_at']) ).'
                  | <i class="fas fa-user mr-1"></i> ' .$row['created_by']. '
                  </small></p>

                  <p class="card-text">' .mb_strimwidth( strip_tags($row['content']) , 0, 180, "..."). '</p>

                  ' .$have. '


                  <a href="index.php?page=announcement_detail&p='.$row['id_announcement'].'" class="mt-2 btn btn-primary btn-sm">Read More</a>

                </div>
                </div>
              </div>';
  }
} else {
  $output .= '
    <div class="mt-5">
        <h2 class="text-center text-muted">No Data Found !</h2>
    </div>
  ';
}

$output .= '
               
                        <!-- Featured_job_end -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Job List Area End -->
<!--Pagination Start  -->
        <div class="pagination-area pb-115 text-center">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="single-wrap d-flex justify-content-center">
                            <nav aria-label="Page navigation example">
                                <ul class="pagination pg-primary">
';



$total_links = ceil($total_data / $limit);
$previous_link = '';
$next_link = '';
$page_link = '';

if ($total_links > 4) {
  if ($page < 5) {
    for ($count = 1; $count <= 5; $count++) {
      $page_array[] = $count;
    }
    $page_array[] = '...';
    $page_array[] = $total_links;
  } else {
    $end_limit = $total_links - 5;
    if ($page > $end_limit) {
      $page_array[] = 1;
      $page_array[] = '...';
      for ($count = $end_limit; $count <= $total_links; $count++) {
        $page_array[] = $count;
      }
    } else {
      $page_array[] = 1;
      $page_array[] = '...';
      for ($count = $page - 1; $count <= $page + 1; $count++) {
        $page_array[] = $count;
      }
      $page_array[] = '...';
      $page_array[] = $total_links;
    }
  }
} else {
  for ($count = 1; $count <= $total_links; $count++) {
    $page_array[] = $count;
  }
}

for ($count = 0; $count < count($page_array); $count++) {
  if ($page == $page_array[$count]) {
    $page_link .= '
    <li class="page-item active ml-2 mr-2">
      <a class="page-link" href="#">' . $page_array[$count] . '</a>
      </li>
    ';

    

    $previous_id = $page_array[$count] - 1;
    if ($previous_id > 0) {
      $previous_link = '
      <li class="page-item mr-2">
        <a aria-label="Previous" class="page-link" href="javascript:void(0)" data-page_number="' . $previous_id . '">
        <span class="bx bx-chevrons-left"></span></a>
      </li>
      ';


    } else {
      $previous_link = '
      <li class="page-item disabled mr-2">
        <a aria-label="Previous - Disabled" class="page-link" href="#">
          <span class="bx bx-chevrons-left"></span>
        </a>
      </li>
      ';
    }
    $next_id = $page_array[$count] + 1;
    if ($next_id >= $total_links) {
      $next_link = '
      <li class="page-item disabled ml-2">
        <a class="page-link" href="#">
          <span class="bx bx-chevrons-right"></span>
        </a>
      </li>
        ';
    } else {
      $next_link = '
        <li class="page-item ml-2">
          <a class="page-link" href="javascript:void(0)" data-page_number="' . $next_id . '">
            <span class="bx bx-chevrons-right"></span>
          </a>
        </li>';
    }
  } else {
    if ($page_array[$count] == '...') {
      $page_link .= '
      <li class="page-item disabled ml-2">
        <a class="page-link" href="#">...</a>
      </li>
      ';
    } else {
      $page_link .= '
      <li class="page-item ml-2">
        <a class="page-link" href="javascript:void(0)" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . '</a>
      </li>
      ';
    }
  }
}

$output .= $previous_link . $page_link . $next_link;
$output .= '
                              </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        <!--Pagination End  -->
';

echo $output;
