<?php
error_reporting(false); session_start();


// Buat Session Token
if ($_SESSION['token'] == '') {
	$length = '64';
	$_SESSION['token'] = random_strings($length);
}


$request = $_GET['request'];
switch ($request) {

	default:
		echo json_encode([
			'welcome_message' => $_SERVER['API_WELCOME'],
			'status' => 0,
			'message' => 'Invalid request !'
		], JSON_PRETTY_PRINT);
		http_response_code(404);
		break;

	case 'datatable':
		route('Datatable/DatatableController', 'POST', ['method' => $_GET['data']]);
		break;
		
	case 'show_data':
		route('ShowDataController', 'POST', ['method' => $_GET['action']]);
	break;

	case 'assignSupervisor':
		route('LecturerController/LecturerController', 'POST', ['method' => 'assignSupervisor']);
		break;

	case 'getLectureByCampus':
		route('LecturerController/LecturerController', 'POST', ['method' => 'getLectureByCampus']);
		break;

	case 'resetStudentPassword':
		route('AccountPasswordController', 'POST', ['method' => 'student']);
		break;

	case 'removeStudent':
		route('StudentController/StudentController', 'POST', ['method' => 'destroy']);
		break;


	case 'company_stats':
		route('CompanyController', 'POST', ['method' => 'company_stats']);
		break;


	case 'delete_user_company':
		route('UserCompanyController', 'DEL', ['method' => 'delete']);
		break;

	case 'bulk_delete_logbook':
		route('LogbookController', 'DEL', ['method' => 'bulk_delete']);
		break;


	case 'bulk_approval_logbook':
		route('LogbookController', 'POST', ['method' => 'bulk_approval']);
		break;

	case 'bulk_approval_attendance':
		route('AttendanceController', 'POST', ['method' => 'bulk_approval']);
		break;


	case 'getDiscussionTopic':
		route('DisussionData', 'POST', ['method' => 'index']);
		break;


	case 'attendance_delete':
		route('AttendanceDelete', 'DEL');
		break;


	case 'disscussion_delete':
		route('DisscussionDelete', 'DEL');
		break;

	case 'comment_edit':
		route('CommentEdit', 'PUT');
		break;


	case 'comment_report':
		route('CommentReport', 'POST');
		break;

	case 'comment_cancel_report':
		route('CommentCancelReport', 'POST');
		break;


	case 'comment_delete':
		route('CommentDelete', 'DEL');
		break;

	case 'profile_remove':
		route('ProfileRemove', 'DEL');
		break;

	case 'logbook_delete':
		route('LogbookDelete', 'DEL');
		break;

	case 'AnnouncementData':
		route('AnnouncementData', 'POST');
		break;

	case 'logout':
		route('Logout', 'POST');
		break;

	case 'validation_reset';
		route('ValidationReset', 'GET');
		break;

	case 'AssignLecturerSupervisor':
		route('LecturerSupervisor', 'POST');
		break;

	case 'session_timeout':
		route('SessionTimeout', 'GET');
		break;
}
