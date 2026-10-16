
<?php 
header('Access-Control-Allow-Origin: *');

error_reporting( false ); session_start();

include 'ajax-init.php';


function time_elapsed_string($datetime, $full = false)
{
	$now = new DateTime;
	$ago = new DateTime($datetime);
	$diff = $now->diff($ago);

	$diff->w = floor($diff->d / 7);
	$diff->d -= $diff->w * 7;

	$string = array(
		'y' => 'year',
		'm' => 'month',
		'w' => 'week',
		'd' => 'day',
		'h' => 'hour',
		'i' => 'minute',
		's' => 'second',
	);
	foreach ($string as $k => &$v) {
		if ($diff->$k) {
			$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
		} else {
			unset($string[$k]);
		}
	}

	if (!$full) $string = array_slice($string, 0, 1);
	return $string ? implode(', ', $string) . ' ago' : 'just now';
}




$id_company = base64_decode ( base64_decode($_POST['id_company'] ));

$sql_notif = "SELECT * FROM internship WHERE id_company = '$id_company' AND status IN ('WAITING','Waiting', 'waiting') ";
$exec_notif = $koneksi_db->query($sql_notif);


$sql_old_notif = "SELECT * FROM internship WHERE id_company = '$id_company' AND status NOT IN ('WAITING','Waiting', 'waiting') ";
$exec_old_notif = $koneksi_db->query($sql_old_notif);

$total_notif = $exec_notif->num_rows;

$output .= "

<a class='nav-link dropdown-toggle' href='#' id='notifDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
<i class='fa fa-bell'></i>";

if ( $total_notif > 0 ) {
$output .= "<span class='notification'>$total_notif</span>";
}

$output .= "</a>";
$output .= "<ul class='dropdown-menu messages-notif-box animated fadeIn' aria-labelledby='notifDropdown'>

<li>
    <div class='dropdown-title'>New Notification</div>
</li>";

if ( $total_notif <= 0 ) {

$output .= "<li>
    <div class='dropdown-title'>You don't have new notification</div>
</li>";

} else {
$output = "<li>
    <div class='message-notif-scroll scrollbar-outer'>
        <div class='notif-center'>";

        foreach ($exec_notif as $i_notif => $dat_notif ):
            $output .= "<a href='#'>
                <div class='notif-icon notif-primary'> <i class='fa fa-user-plus'></i> </div>
                <div class='notif-content'>
                    <span class='block'>
                        New intern registered
                    </span>
                    <span class='time'>" .time_elapsed_string($dat_notif['timestamp_register']). "</span> 
                </div>
            </a>";

        endforeach;
            
        $output .= "</div>
    </div>
</li>";

}

$output .= "<div class='dropdown-title'>Older Notification</div>";

if ( $exec_old_notif->num_rows <= 0 ) {

$output .= "
<li>
    <div class='dropdown-title'>You don't have older notification</div>
</li>";
} else {

    $output .= "    
<li>
    <div class='message-notif-scroll scrollbar-outer'>
        <div class='notif-center'>";

            foreach ($exec_old_notif as $i_notif => $dat_notif ):
                $output .= "<a href='#'>
                <div class='notif-icon notif-primary'> <i class='fa fa-user-plus'></i> </div>
                <div class='notif-content'>
                    <span class='block'>
                        New intern registered
                    </span>
                    <span class='time'>" .time_elapsed_string($dat_notif['timestamp_register']). "</span> 
                </div>
            </a>";

            endforeach;

            
            
            $output .= "  </div>
    </div>
</li>";

}


$output .= "</ul>";

echo '';