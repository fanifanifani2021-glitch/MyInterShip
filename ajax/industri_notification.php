<?php
header('Access-Control-Allow-Origin: *');

error_reporting(false); session_start();

include 'ajax-init.php';

$id_company = base64_decode(base64_decode($_POST['id_company']));

$sql_notif = "SELECT * FROM internship WHERE id_company = '$id_company' AND status IN ('WAITING','Waiting', 'waiting') ";
$exec_notif = $koneksi_db->query($sql_notif);
$total_notif = mysqli_num_rows($exec_notif);



$count .= "
<ul class='dropdown-menu messages-notif-box animated fadeIn' aria-labelledby='notifDropdown' id=''>

<li>
	<div class='dropdown-title'>New Notification</div>
</li>";

if ($total_notif <= 0) {

	$count .= "
        <li>
		    <div class='dropdown-title'>You don't have new notification</div>
		</li>";
}

else {

	foreach ($exec_notif as $i_notif => $dat_notif) :

		$count .= "<li class=''> 
            <div class='message-notif-scroll scrollbar-outer'>
			    <div class='notif-center'>";

		$count .= "<a href='#'>
						<div class='notif-icon notif-primary'> <i class='fa fa-user-plus'></i> </div>
						<div class='notif-content'>
						<span class='block'>
						New intern registered
						</span>
						<span class='time'>" . time_elapsed_string($dat_notif['timestamp_register']) . "</span>
					    </div>
					    </a>";

		$count .= "</div>
			</div>
		</li>";

	endforeach;


	$count .=    "</ul>";
}


echo $count;
