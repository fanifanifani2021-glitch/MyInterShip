<?php
error_reporting(false);
session_start();

$request = $_GET['api'];
switch ($request) {

	default:

		response_json(404, [
			'error_code' => 404,
			'status' => 0, 'message' => 'API not available !'
		]);

		break;
		
		case 'talenthub_login':
			api('talenthub/login', 'POST', function () {
				throttle( 'from_talenthub', 'login', 20, 5, 'seconds');
			});
		break;
		
		case 'companies_ranking_received_student':
    		api('companies_ranking_received_student', 'POST', function () {
    			throttle('companies_ranking_received_student', 'companies_ranking_received_student', 20, 5, 'seconds');
    		});
		break;
		
		case 'companies_ranking_major':
    		api('companies_ranking_major', 'POST', function () {
    			throttle('companies_ranking_major', 'companies_ranking_major', 20, 5, 'seconds');
    		});
		break;
		
	
		case 'talenthub_student_search':
		    api('talenthub/student_search', 'POST', function () {
			throttle('from_talenthub', 'student_search', 20, 5, 'seconds');
			});
		break;
		
				// Stats
		case 'myinternship_stats':
			api('myinternship/MyInternshipStats', 'GET');
		break;

		case 'count_company':
			api('myinternship/stats/CountCompany', 'GET');
		break;

		case 'count_job':
			api('myinternship/stats/CountJob', 'GET');
		break;

		case 'count_internship':
			api('myinternship/stats/CountInternship', 'GET');
		break;

		
	    case 'random_company':
	        api('company/random_company', 'GET');
	    break;

		case 'random_company_offer':
			api('company/random_company_offer', 'GET');
		break;
		
    	case 'random_company_job':
    	    api('company/random_company_job', 'GET');
    	break;

		case 'available_api':
			api('available_api', 'GET');
			break;
	
		case 'myinternship_weather_info':
			api('myinternship/MyInternshipWeatherInfo', 'POST');
			break;
	
		case 'myinternship_student_login':
			api('myinternship/MyInternshipStudentLogin', 'POST');
			break;
	
		case 'myinternship_lecturer_login':
			api('myinternship/MyInternshipLecturerLogin', 'POST');
			break;
	
		case 'myinternship_industry_login':
			api('myinternship/MyInternshipIndustryLogin', 'POST');
			break;
	
	
		case 'polibatam_login':
			api('polibatam/LoginAPI', 'POST');
			break;
	
		case 'old_polibatam_login':
			api('polibatam/OldLoginAPI', 'POST');
			break;
	
		case 'polibatam_biodata':
			api('polibatam/BiodataAPI', 'POST');
			break;
	
	
		case 'logbook_detail':
			api('logbook/logbook_detail', 'POST');
			break;
	
	
		case 'attendance_detail':
			api('attendance/attendance_detail', 'POST');
			break;
	
		case 'LogbookByInternship':
			api('logbook/LogbookByInternship', 'POST');
			break;
	
		case 'LogbookByLecturer':
			api('logbook/LogbookByLecturer', 'POST');
			break;
	
	
	
	
		case 'company_detail':
			api('company/company_detail', 'GET');
			break;
	
		case 'company_list':
			api('company/company_list', 'POST');
			break;
	
		case 'login_api':
			api('company/industry_login', 'POST');
			break;
	
		case 'industry_login':
			api('company/industry_login', 'POST');
			break;
	
		case 'job':
			api('company/job', 'GET', function () {
    			throttle('from_talenthub', 'job', 20, 5, 'seconds');
    		});
			break;
	
		case 'job_detail':
    		api('company/job_detail', 'GET', function () {
    			throttle('from_talenthub', 'job_detail', 20, 5, 'seconds');
    		});
    		break;

    	case 'job_list':
    		api('company/job_list', 'POST', function () {
    			throttle('from_talenthub', 'job_list', 20, 5, 'seconds');
    		});
    		break;
    
    	case 'company_user':
    		api('company/company_user', 'POST', function () {
    			throttle('from_talenthub', 'company_user', 20, 5, 'seconds');
    		});
    		break;
    
    	case 'prodi':
    		api('prodi', 'GET', function () {
    			throttle('from_talenthub', 'prodi', 20, 5, 'seconds');
    		});
    		break;
    
    	case 'prodi_eng':
    		api('prodi_eng', 'GET', function () {
    			throttle('from_talenthub', 'prodi_eng', 20, 5, 'seconds');
    		});
    		break;
}
