<?php

use Encrypter;
use Response;
use Router;
use Redirect;

	// =========================================================================
    // 🛡️ BLOCK DIRECT IP ACCESS (INTERCEPTOR)
    // =========================================================================
    $hostHeader = $_SERVER['HTTP_HOST'] ?? '';
    $hostname   = explode(':', $hostHeader)[0];

    // Daftar Host / IP yang diizinkan untuk keperluan development
    $trustedLocal = ['localhost', '127.0.0.1', '::1'];

    // Cek apakah hostname berupa IP dan BUKAN localhost
    if (filter_var($hostname, FILTER_VALIDATE_IP) && !in_array($hostname, $trustedLocal, true)) {
        
        // Ambil URL lengkap yang sedang diakses pengguna/bot
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $accessedUrl = $scheme . '://' . $hostHeader . ($_SERVER['REQUEST_URI'] ?? '/');

        $errorMessage = "Direct IP access is not allowed. Please access via https://myinternship.id";

        // Cukup panggil Router::handleError, dia akan otomatis menangani tampilan
        // (Error Page HTML jika dari browser, atau Response JSON jika dari AJAX/Request API)
        Router::handleError(403, $errorMessage, $accessedUrl);
        exit;
    }

// Buat Session Token
if ($_SESSION['token'] == '') {
	$length = '64';
	$_SESSION['token'] = random_strings($length);
}

// Routing POST => Untuk Form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {


	// Make OLD variable
	$cek_form = to_old();

	// Cek apakah akses index.php dengan variabel selain ?form
	if ($_SERVER['QUERY_STRING'] != "" && strstr($_SERVER['QUERY_STRING'], "form") === FALSE) {
		http_response_code(400);
		renderView('errors.error_400', ['title' => '400 - Bad Request']);
		exit;
	}
	
	
	// Kalau token tidak ditemukan, langsung error 419
	if (cek_token() === false) {
		http_response_code(419);
		renderView('errors.error_419', ['title' => '419 - Expired']);

		exit;
	}


	// Untuk Dev saja
	if ($_SESSION['SERVER_MODE'] == 'DEVELOPMENT' && $_POST['DEVELOPMENT'] == 'DEVELOPMENT') {
		dev_mode();
		exit;
	}

	if (isset($_POST['_method']) && $_POST['_method'] == 'DELETE') {
		$form = $_GET['form'];
		switch ($form) {
			default:
				http_response_code(404);

				if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'XMLHttpRequest') {
					renderView('errors.error_404', ['title' => '404 - Page Not Found']);
				} else {
					echo json_encode([
						'welcome_message' => $_SERVER['API_WELCOME'],
						'status' => 0,
						'message' => 'Invalid request !'
					], JSON_PRETTY_PRINT);
				}

				break;


			case 'university_delete':
				controller('UniversityData');
				break;
		}


		exit;
	}


	$form = $_GET['form'];
	switch ($form) {

		default:
			http_response_code(404);

			if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'XMLHttpRequest') {
				renderView('errors.error_404', ['title' => '404 - Page Not Found']);
			} else {
				echo json_encode([
					'welcome_message' => $_SERVER['API_WELCOME'],
					'status' => 0,
					'message' => 'Invalid request !'
				], JSON_PRETTY_PRINT);
			}
			break;
			
			
		case 'mark_internship':
			controller('MarkInternship', [] );
			
		case 'supervised_student':
			controller('SupervisedStudentController', ['action' => $_GET['action']], function () {
				throttle('SupervisedStudent', 'SupervisedStudent', 5, 5, "seconds");
			});
			break;

					
		case 'company_mou':
			controller('CompanyMoUController',[], function () {
				throttle('CompanyMoU', 'CompanyMoU', 5, 5, "seconds");
			});
			break;

		case 'company_data':
			controller('CompanyData');
			break;


		// Internship Letter
		case 'submit_internship_letter_request':
			controller('StudentController/InternshipLetter', ['method' => 'post'], function () {
				throttle('InternshipLetter', 'submit_internship_letter_request', 1, 60, "seconds");
			});
			break;

		
		case 'internship-coordinator/letter_approval':
			controller('CoordinatorController/InternshipLetter', ['method' => 'letter_approval'], function () {
				throttle('InternshipLetter', 'internship-coordinator-letter_approval', 1, 30, "seconds");
			});
			break;
			
		case 'cdc/letter_approval':
			controller('CDCController/InternshipLetter', ['method' => 'letter_approval'], function () {
				throttle('InternshipLetter', 'cdc-letter_approval', 1, 30, "seconds");
			});
			break;

		case 'student/internship_letter/acceptance_form':
			controller('StudentController/InternshipLetter', ['method' => 'submit_acceptance_form'], function () {
				throttle('InternshipLetter', 'submit_acceptance_form', 1, 30, "seconds");
			});
			break;
		



		case 'university_data':
			controller('UniversityData',[], function () {
				throttle('UniversityData', 'UniversityData', 5, 5, "seconds");
			});
			break;

		case 'university':
			controller('proses_university',[], function () {
				throttle('University', 'University', 5, 5, "seconds");
			});
			break;


		case 'internship':
			controller('proses_internship',[], function () {
				throttle('Internship', 'Internship', 5, 5, "seconds");
			});
			break;

		case 'cdc_profile':
			controller('proses_cdc_profile');
			break;


		case 'filter_internship_student_list':
			view('upkpk/intern_student_list/filter_internship_student_list');
			break;

		case 'filter_lecturer':
			view('upkpk/filter_lecturer');
			break;


		case 'setPolibatamSupervisor':
			controller('setPolibatamSupervisor');
			break;

		case 'lecturer':
			controller('proses_lecturer');
			break;

		case 'preview_import_lecturer':
			controller('proses_preview_import_lecturer');
			break;

		case 'import_lecturer':
			controller('proses_import_lecturer');
			break;


		case 'preview_import_perusahaan':
			controller('proses_preview_import_perusahaan');
			break;

		case 'import_perusahaan':
			controller('proses_import_perusahaan');
			break;



		case 'student_intern_certificate':
			view('student_intern_certificate');
			break;


			// AUTH
			
		case 'flogin':
			controller('proses_flogin', [], function () {
				throttle('ExpressLogin', 'ExpressLogin', 2, 5, "seconds");
			});
			break;


		case 'logout':
			controller('AuthController/LogoutController', [], function () {
				allowed_method('POST');
				throttle('Logout', 'Logout', 2, 5, "seconds");
			});
			break;


			// Dosen
		case 'lecturer_profile':
			controller('proses_lecturer_profile',[], function () {
				throttle('LecturerProfile', 'LecturerProfile', 5, 5, "seconds");
			});
			break;

		case 'add_supervisor':
			controller('proses_add_supervisor');
			break;

		case 'edit_supervisor':
			controller('proses_edit_supervisor');
			break;

		case 'add_student_intern':
			controller('proses_add_student_intern');
			break;

		case 'edit_student_intern':
			controller('proses_edit_student_intern');
			break;

		case 'intern_co':
			controller('proses_intern_co');
			break;


			// PKPK

		case 'company':
			controller('proses_perusahaan');
			break;


			// Industry
		case 'hrd':
			controller('proses_hrd');
			break;

		case 'offer':
			controller('proses_offer');
			break;


		case 'offer2':
			controller('OfferController');
			break;

		case 'validate_intern':
			controller('proses_validate_intern');
			break;

		case 'industry_feedback':
			controller('proses_industry_feedback');
			break;


			// Mahasiswa

		case 'student_feedback':
			controller('proses_student_feedback');
			break;

		case 'final_report':
			controller('proses_final_report');
			break;


		case 'register_independent':
			controller('proses_register_independent');
			break;

		case 'dkm':
			controller('proses_dkm');
			break;

		case 'register_offer':
			controller('proses_register_offer');
			break;

		case 'logbook':
			controller('proses_logbook');
			break;

		case 'register_cooperation':
			controller('proses_register_cooperation');
			break;


		case 'logbook_approval':
			controller('proses_logbook_approval');
			break;


		case 'student_feedback':
			controller('proses_student_feedback');
			break;


		case 'attendance':
			controller('proses_attendance');
			break;

		case 'attendance_approval':
			controller('proses_attendance_approval');
			break;


		case 'discussion':
			controller('proses_discussion');
			break;

		case 'industry_profile':
			controller('proses_industry_profile');
			break;

		case 'jobdesk_dkm':
			controller('proses_jobdesk_dkm');
			break;

		case 'jobdesk_spv':
			controller('proses_jobdesk_spv');
			break;

		case 'graduation_profile_dkm':
			controller('proses_graduation_profile_dkm');
			break;

		case 'competence_unit_dkm':
			controller('proses_competence_unit_dkm');
			break;

		case 'competence_spv':
			controller('proses_competence_spv');
			break;


		case 'study_program':
			controller('proses_study_program');
			break;

		case 'preview_import_student':
			controller('proses_preview_import_student');
			break;

		case 'import_student':
			controller('proses_import_student');
			break;
			
		case 'company_stats':
		    controller('CompanyStats', [], function () {
                throttle('CompanyStats', 'CompanyStats', 5, 5, "seconds");
                allowed_method('POST');
            });
            break;
            
        case 'student_local_account':
			controller('AuthController/StudentLocalAccountController', ['method' => 'student_local_account_process'], function () {
				throttle('local_account_process', 'local_account_process', 5, 5, "seconds");
				allowed_method('POST');
			});
			break;
			
	}
} else
	// Routing GET => Untuk Halaman
	if ($_SERVER['REQUEST_METHOD'] == "GET") {


		if (isset($_SERVER['HTTP_REFERER'])) {
			$_SESSION['previous'] = $_SERVER['HTTP_REFERER'];
		} else {
			// Jika HTTP_REFERER tidak ada, menggunakan default atau data dari permintaan
			$_SESSION['previous'] = isset($_REQUEST['from']) ? $_REQUEST['from'] : base_url();
		}

		// Cek apakah akses index.php dengan variabel selain ?page
		if ($_SERVER['QUERY_STRING'] != "" && strstr($_SERVER['QUERY_STRING'], "page") === FALSE) {
			renderView('errors.error_404', ['title' => '404 - Page Not Found']);
			exit;
		}

		// Jika dapat $_GET['page']
		if (isset($_GET['page']) && $_GET['page'] != "") {


			// Switch - Case $_GET['page']
			$page = $_GET['page'];
			switch ($page) {


					// Jika tidak ada otomatis ke error_404
				default:
					http_response_code(404);

					if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'XMLHttpRequest') {
						renderView('errors.error_404', ['title' => '404 - Page Not Found']);
					} else {
						echo json_encode([
							'welcome_message' => $_SERVER['API_WELCOME'],
							'status' => 0,
							'message' => 'Invalid request !'
						], JSON_PRETTY_PRINT);
					}
					break;


				case 'show_data':
					controller('ShowDataController', ['method' => $_GET['action']], function () {
						throttle('SHowDataController', $_GET['action'], 5, 5, "seconds");
					});
					break;


				## Halaman Create Local Account
				case 'student_local_account':
				    controller('AuthController/StudentLocalAccountController', ['method' => 'index'], function () {
							allowed_method('GET');
					});
				    break;

					
				case 'logbook_approval':
        		    controller('view/LogbookApproval');
        			break;


				case 'attendance_approval':
					controller('view/AttendanceApproval');
					break;

				case 'addhistory_attendance_internship':
					controller('view/AttendanceHistory');
					break;



				case 'export_company':
					controller('export/export_company');
					break;


				case 'assign_supervisor_lecturer':
					controller('InternshipController', ['method' => 'assign_supervisor_lecturer']);
					break;



				case 'local_account12379':
					controller('special/local_account');
					break;



				case 'print_logbook_internship':
					controller('PrintLogbook');
					break;

				case 'print_attendance_internship':
					controller('PrintAttendance');
					break;


				case 'print_student_feedback':
					controller('PrintStudentFeedback');
					break;

				case 'print_industry_feedback':
					controller('print/PrintIndustryFeedback');
					break;


				case 'logbook_student':
					renderView('lecturer.internship_data.student_logbook', ['title' => 'Student Logbook', 'routes' => 'logbook_student']);
					break;

				case 'attendance_student':
					renderView('lecturer.internship_data.student_attendance', ['title' => 'Student Attendance', 'routes' => 'attendance_student']);
					break;
					
				case 'discussion_student':
					renderView('lecturer.internship_data.student_discussion', ['title' => 'Student Discussion', 'routes' => 'discussion_student']);
					break;

				case 'administration_student':
					renderView('lecturer.internship_data.administration_student', ['title' => 'Student Administration', 'routes' => 'administration_student']);
					break;

				case 'add_internship':
					view('upkpk/internship_management/add_internship');
					break;

				case 'edit_internship':
					view('upkpk/internship_management/edit_internship');
					break;

				
				case 'discussion_intern_co':
					view('upkpk/internship_discussion/internship_discussion');
					break;

				case 'discussion_join_intern_co':
					view('upkpk/internship_discussion/internship_discussion_join');
					break;

				case 'discussion_comment_summary_intern_co':
					view('upkpk/internship_discussion/internship_discussion_comment_summary');
					break;


				case 'internship_detail_intern_co':
					view('upkpk/intern_student_list/internship_detail_intern_co');
					break;

				case 'cdc_profile':
					view('upkpk/cdc_profile/cdc_profile');
					break;

				case 'student_detail_intern_co':
					view('upkpk/student_detail_intern_co');
					break;


				case 'export_intern_student':
					view('upkpk/intern_student_list/export_intern_student');
					break;

				case 'offline':
					include "./offline.html";
					break;

				case 'student_intern_certificate':
					view('student_intern_certificate');
					break;


				case 'intern_student_list':
					renderView('cdc.student_management.index', ['title' => 'Student Management', 'route' => 'intern_student_list']);
					break;
					
				case 'add_student_intern':
					renderView('cdc.student_management.add', ['title' => 'Student Management', 'route' => 'intern_student_list']);
					break;
	
				# Misc


				case 'view_pdf':
					view('view_pdf');
					break;

					# Dosen
				case 'lecturer_profile':
					renderView('lecturer.lecturer_profile', ['title' => 'Lecturer Profile', 'routes' => 'lecturer_profile']);
					break;


					## DKM
				case 'profile_jobdesc':
					view('dosen/dkm/profile_jobdesc/profile_jobdesc');
					break;

				case 'competence_unit_manager':
					view('dosen/dkm/profile_jobdesc/competence_unit_manager');
					break;



				case 'student_internship_detail':
					view('dosen/dkm/student_intern_management/student_internship_detail');
					break;

				case 'student_intern_competency':
					view('dosen/dkm/student_intern_management/student_intern_competency/student_intern_competency');
					break;

				case 'student_intern_competency_detail':
					view('dosen/dkm/student_intern_management/student_intern_competency/student_intern_competency_detail');
					break;

				case 'import_student_intern':
				// 	view('dosen/dkm/student_intern_management/import_student_intern');
				// 	break;
					renderView('coordinator.student_management.import', ['title' => 'Student Management', 'routes' => 'student_intern_management'], function () {
						allowed_method('GET');
					});
					break;

				case 'student_intern_management':
				    renderView('coordinator.student_management.index', ['title' => 'Student Management', 'routes' => 'student_intern_management'], function () {
						allowed_method('GET');
					});
			        break;
			       
			    case 'export_student_intern':
					controller('export/export_student_intern');
					break;

				// case 'add_student_intern':
				// 	view('dosen/dkm/student_intern_management/add_student_intern');
				// 	break;

				case 'edit_student_intern':
					view('dosen/dkm/student_intern_management/edit_student_intern');
					break;

				case 'student_intern_detail':
					view('dosen/dkm/student_intern_management/student_intern_detail');
					break;

				case 'jobdesk_management':
					renderView('lecturer.jobdesk_management.index', ['title' => 'Jobdesk Mnagement', 'routes' => 'jobdesk_management' ]);
					break;

				case 'supervisor_management':
				// 	view('dosen/dkm/supervisor_management/supervisor_management');
				    renderView('coordinator.supervisor_management.index', ['title' => 'Lecturer Management', 'routes' => 'supervisor_management']);

					break;

				case 'add_supervisor':
					view('dosen/dkm/supervisor_management/add_supervisor');
					break;

				case 'edit_supervisor':
					view('dosen/dkm/supervisor_management/edit_supervisor');
					break;

				case 'supervisor_detail':
					view('dosen/dkm/supervisor_management/supervisor_detail');
					break;

				case 'student_intern_attendance':
					view('dosen/dkm/student_intern_management/student_intern_attendance/student_intern_attendance');
					break;

				case 'student_intern_attendance_detail':
					view('dosen/dkm/student_intern_management/student_intern_attendance/student_intern_attendance_detail');
					break;

				case 'student_intern_registration':
					view('dosen/dkm/student_intern_management/student_intern_registration');
					break;

				case 'student_intern_final_report':
					view('dosen/dkm/student_intern_management/student_intern_final_report');
					break;

				case 'student_intern_company':
					view('dosen/dkm/student_intern_management/student_intern_company');
					break;

				case 'student_intern_supervisor':
					view('dosen/dkm/student_intern_management/student_intern_supervisor');
					break;

				case 'student_intern_lecturer':
					view('dosen/dkm/student_intern_management/student_intern_lecturer');
					break;

				case 'student_intern_jobdesk':
					view('dosen/dkm/student_intern_management/student_intern_jobdesk/student_intern_jobdesk');
					break;

				case 'student_intern_discussion':
					view('dosen/dkm/student_intern_management/student_intern_discussion/student_intern_discussion');
					break;

				case 'student_intern_join_discussion':
					view('dosen/dkm/student_intern_management/student_intern_discussion/student_intern_join_discussion');
					break;

				case 'student_intern_discussion_comment_summary':
					view('dosen/dkm/student_intern_management/student_intern_discussion/student_intern_discussion_comment_summary');
					break;

				case 'student_intern_logbook':
					view('dosen/dkm/student_intern_management/student_intern_logbook/student_intern_logbook');
					break;

				case 'student_intern_logbook_detail':
					view('dosen/dkm/student_intern_management/student_intern_logbook/student_intern_logbook_detail');
					break;

				case 'student_intern_student_feedback':
					view('dosen/dkm/student_intern_management/student_intern_student_feedback');
					break;

				case 'student_intern_industry_feedback':
					view('dosen/dkm/student_intern_management/student_intern_industry_feedback');
					break;








				## DPM
				case 'intern_student_management': // Route lama
					// 4. Render Halaman Pemberitahuan (UX untuk Manusia)
					renderView('errors.error_301', [
						'newUrl' => router('lecturer/my/student.index'),
						'seconds' => 5 
					], function() {
						http_response_code(301);
						header("Refresh: 5; url=" .router('lecturer/my/student.index'));

					});
				break;
					
                case 'add_supervised_student':
					controller('SupervisedStudentController', ['method' => 'add'], function () {
					    throttle('SupervisedStudent', 'SupervisedStudent', 5, 5, "seconds");
					});
					break;


                case 'intern_student_detail':
					$nim = base64_decode($_GET['nim']) ?? null;
					$routerUrl = router('lecturer/my/student.detail', ['nim' => $nim]);
					Response::redirect( $routerUrl, 100 ); exit;
					break;


				case 'intern_student_internship_detail':
					// renderView('lecturer.internship_management.internship_detail', ['title' => 'Internship Detail', 'routes' => 'intern_student_management']);
					$id_internship = base64_decode($_GET['id_internship']) ?? null;

					$sqlNim = "SELECT nim FROM internship WHERE id_internship = ?";
					$resultNim = $_SERVER['PDO']->prepare($sqlNim);
					$resultNim->execute([$id_internship]);
					$nim = $resultNim->fetchColumn() ?? null;

					$routerUrl = router('lecturer/my/student.internship.detail', ['nim' => $nim, 'id_internship' => $id_internship]);
					Response::redirect( $routerUrl, 100); exit;
					break;
					
				case 'intern_student_jobdesk':
					renderView('lecturer.internship_management.student_jobdesc.index', ['title' => 'Jobdesc', 'routes' => 'intern_student_management']);
					break;

				case 'intern_student_competency':
					view('dosen/dpm/intern_student_management/intern_student_competency/intern_student_competency');
					break;

				case 'intern_student_competency_detail':
					view('dosen/dpm/intern_student_management/intern_student_competency/intern_student_competency_detail');
					break;

				case 'intern_student_registration':
					view('dosen/dpm/intern_student_management/intern_student_registration');
					break;

				case 'intern_student_company':
					view('dosen/dpm/intern_student_management/intern_student_company');
					break;

				case 'intern_student_supervisor':
					view('dosen/dpm/intern_student_management/intern_student_supervisor');
					break;

                case 'intern_student_discussion':
					renderView('lecturer.internship_management.discussion.index',['title' => 'Discussion', 'routes' => 'intern_student_management']);
					break;

                case 'intern_student_discussion_join':
					renderView('lecturer.internship_management.discussion.detail',['title' => 'Discussion - Join', 'routes' => 'intern_student_management']);
					break;

				case 'intern_student_discussion_comment_summary':
					view('dosen/dpm/intern_student_management/intern_student_discussion/intern_student_discussion_comment_summary');
					break;

                case 'intern_student_attendance':
					renderView('lecturer.internship_management.attendance.index',['title' => 'Attendance', 'routes' => 'intern_student_management']);
					break;

                case 'intern_student_attendance_detail':
					renderView('lecturer.internship_management.attendance.detail',['title' => 'Attendance - Detail', 'routes' => 'intern_student_management']);
					break;

				case 'intern_student_logbook':
					renderView('lecturer.internship_management.logbook.index',['title' => 'Logbook', 'routes' => 'intern_student_management']);
					break;

				case 'intern_student_logbook_detail':
					renderView('lecturer.internship_management.logbook.detail',['title' => 'Logbook - Detail', 'routes' => 'intern_student_management']);
					// view('dosen/dpm/intern_student_management/intern_student_logbook/intern_student_logbook_detail');
					break;

				case 'intern_student_final_report':
					view('dosen/dpm/intern_student_management/intern_student_final_report');
					break;

				case 'intern_student_student_feedback':
					view('dosen/dpm/intern_student_management/intern_student_student_feedback');

					break;

				case 'intern_student_industry_feedback':
					view('dosen/dpm/intern_student_management/intern_student_industry_feedback');
					break;




					# Mahasiwa
				case 'student_feedback_internship':
					renderView('student.my_internship.student_feedback.index', ['title' => 'Student Feedback', 'routes' => 'my_internship'], function () {
						allowed_method('GET');
					});
					break;


				case 'register_offer':
					view('mahasiswa/register_offer');
					break;

				
				case 'my_registration':
					view('mahasiswa/my_internship/my_registration');
					break;

				case 'my_supervisor':
					view('mahasiswa/my_internship/my_supervisor');
					break;

				case 'my_lecturer':
					view('mahasiswa/my_internship/my_lecturer');
					break;

				case 'jobdesk_internship':
					view('mahasiswa/jobdesk_internship/jobdesk_internship');
					break;

				case 'add_attendance_internship':
					// 1. Ambil ID mentah dari format lama (base64)
					$raw_id = base64_decode($_GET['id_internship']);
					
					// 2. Encrypt kembali agar sesuai dengan kebutuhan Router {id}
					$id_enc = Encrypter::encrypt($raw_id);
					
					Response::redirect(router('student.attendance.addHistory', ['id' => $id_enc]));
					// view('mahasiswa/attendance_internship/add_attendance_internship');
					break;

				case 'check_in_attendance_internship':
					// 1. Ambil ID mentah dari format lama (base64)
					$raw_id = base64_decode($_GET['id_internship']);
					
					// 2. Encrypt kembali agar sesuai dengan kebutuhan Router {id}
					$id_enc = Encrypter::encrypt($raw_id);
					
					// 3. Gunakan Response::redirect untuk eksekusi pengalihan aman
					Response::redirect(router('student.attendance.checkIn', ['id' => $id_enc]));
					break;

				case 'check_out_attendance_internship':
					// Proses yang sama untuk sinkronisasi ID
					$raw_id = base64_decode($_GET['id_internship']);
					$id_enc = Encrypter::encrypt($raw_id);
					
					// Alihkan ke rute checkOut yang sudah kita buat sebelumnya
					Response::redirect(router('student.attendance.checkOut', ['id' => $id_enc]));
					break;


			    case 'detail_attendance_internship':
					// Redirect::to(router('student.attendance.detail', ['id' => Encrypter::encrypt( base64_decode($_GET['id_internship']) ), 'attendance_id' => Encrypter::encrypt( base64_decode($_GET['id_attendance']) )]) );
					controller('view/AttendanceDetail');
					break;


				case 'final_report_internship':
					// 1. Ambil ID mentah dari base64 (parameter lama)
					$raw_id = base64_decode($_GET['id_internship']);
					
					// 2. Encrypt kembali agar sesuai dengan kebutuhan Router baru {id}
					$id_enc = Encrypter::encrypt($raw_id);
					
					// 3. Gunakan Response::redirect untuk menghindari error dependency
					// Pastikan nama rute sesuai dengan yang kamu definisikan di Router::group (student.final-report.index)
					Response::redirect(router('student.final-report.index', ['id' => $id_enc]));
					break;




				case 'industry_feedback_internship':
				    renderView('student.my_internship.industry_feedback.index', ['title' => 'Industry Feedback', 'routes' => 'my_internship'], function () {
					    allowed_method('GET');
					});
					break;


				case 'register_independent':
					view('mahasiswa/register_internship/register_independent');
					break;



				case 'student/internship_letter/detail':
					controller('StudentController/InternshipLetter', ['method' => 'detail']);
					break;

				case 'student/internship_letter/acceptance_form':
					controller('StudentController/InternshipLetter', ['method' => 'acceptance_form']);
					break;
					

				case 'internship-coordinator/internship_letter/detail':
					controller('CoordinatorController/InternshipLetter', ['method' => 'detail']);
					break;

				case 'print_internship_letter':
					controller('PrintController/InternshipLetter', ['method' => 'print']);
					break;




				// My Internship
				case 'my_internship':
					Redirect::to(router('student.internship'), 10);
					break;

				case 'internship_detail':
					Redirect::to(router('student.internship.detail', ['id' => Encrypter::encrypt( base64_decode($_GET['id_internship']) )]), 10);
					break;

				case 'company_internship':
					view('mahasiswa/my_internship/company_internship');
					break;

				case 'logbook_internship':
					Redirect::to(router('student.logbook.index', ['id' => Encrypter::encrypt( base64_decode($_GET['id_internship']) )]), 10);
					// controller('view/LogbookInternshipMahasiswa', ['method' => 'index']);
					break;

				case 'add_logbook_internship':
					Redirect::to(router('student.logbook.add', ['id' => Encrypter::encrypt( base64_decode($_GET['id_internship']) )]), 10);
					// controller('view/LogbookInternshipMahasiswa', ['method' => 'add']);
					break;

				case 'edit_logbook_internship':
					Redirect::to(router('student.logbook.edit', ['id' => Encrypter::encrypt( base64_decode($_GET['id_internship']) ), 'logbook_id' => Encrypter::encrypt( base64_decode($_GET['id_logbook']) )]), 10);
					// controller('view/LogbookInternshipMahasiswa', ['method' => 'edit']);
					break;

				case 'detail_logbook_internship':
					Redirect::to(router('student.logbook.detail', ['id' => Encrypter::encrypt( base64_decode($_GET['id_internship']) ), 'logbook_id' => Encrypter::encrypt( base64_decode($_GET['id_logbook']) )]), 10);
					// controller('view/LogbookInternshipMahasiswa', ['method' => 'detail']);
					break;


				case 'attendance_internship':
					Redirect::to(router('student.attendance.index', ['id' => Encrypter::encrypt( base64_decode($_GET['id_internship']) )]), 10);
					break;

				case 'edit_attendance_internship':
					view('mahasiswa/attendance_internship/edit_attendance_internship');
					break;

					## Student - Discussion
				case 'discussion_internship':
					controller('view/DiscussionInternshipMahasiswa', ['method' => 'index']);
					break;

				case 'discussion_join_internship':
					view('mahasiswa/discussion_internship/discussion_join_internship');
					break;

				case 'discussion_comment_summary_internship':
					view('mahasiswa/discussion_internship/discussion_comment_summary_internship');
					break;




					# UKPK
				case 'intern_co_management':
					view('upkpk/intern_co_management/intern_co_management');
					break;

				case 'add_intern_co':
					view('upkpk/intern_co_management/add_intern_co');
					break;

				case 'edit_intern_co':
					view('upkpk/intern_co_management/edit_intern_co');
					break;

				case 'detail_intern_co':
					view('upkpk/intern_co_management/detail_intern_co');
					break;

					# Company Management
				case 'company_management':
					renderView('cdc.company_management.index', ['title' => 'Company Management', 'routes' => 'company_management', 'route' => 'company_management'], function () {
						allowed_method('GET');
					});
					break;

				case 'company_detail':
					renderView('cdc.company_management.detail', ['title' => 'Company Detail', 'routes' => 'company_management', 'route' => 'company_management'], function () {
						allowed_method('GET');
					});
					break;
					
                case 'company_summary':
                    renderView('cdc.company_management.summary', ['title' => 'Company Summary', 'route' => 'company_management', 'routes' => 'company_management'], function () {
                        allowed_method('GET');
                    });
                    break;

					# Job Offer
				case 'job_offer':
					renderView('cdc.job_offer.index', ['title' => 'Job Offer', 'routes' => 'job_offer', 'route' => 'job_offer'], function () {
						allowed_method('GET');
					});
					break;




					// Company MoU Management
				case 'company_mou_management':
					controller('view/CompanyMoUController', ['method' => 'index']);
					break;

				case 'detail_mou':
					controller('view/CompanyMoUController', ['method' => 'detail']);
					break;

				case 'add_mou':
					controller('view/CompanyMoUController', ['method' => 'add']);
					break;

				case 'edit_mou':
					controller('view/CompanyMoUController', ['method' => 'edit']);
					break;




				case 'import_company':
					view('upkpk/kelola_perusahaan/import_perusahaan');
					break;


				case 'add_company':
					view('upkpk/kelola_perusahaan/tambah_perusahaan');
					break;

				case 'edit_company':
					view('upkpk/kelola_perusahaan/edit_perusahaan');
					break;



					# Industri
				case 'offer_detail':
					view('industri/detail_offer');
					break;


				case 'my_company':
					renderView('industry.my_company', ['title' => 'My Company', 'routes' => 'my_company', 'route' => 'my_company']);
					break;


				case 'intern_logbook':
					renderView('industry.internship_data.intern_logbook', ['title' => 'Student Logbook', 'routes' => 'intern_logbook', 'route' => 'intern_logbook']);
					break;
					
				case 'intern_attendance':
					renderView('industry.internship_data.intern_attendance', ['title' => 'Student Attendance', 'routes' => 'intern_attendance', 'route' => 'intern_attendance']);
					break;
	
					
				case 'industry_profile':
					view('industri/industry_profile/industry_profile');
					break;

					## SPV
				case 'intern_list':
					view('industri/intern_list/intern_list');
					break;

				case 'intern_list_detail':
					view('industri/intern_list/intern_list_detail');
					break;

				case 'intern_list_competency':
					view('industri/intern_list/intern_list_competency/intern_list_competency');
					break;

				case 'intern_list_add_competency':
					view('industri/intern_list/intern_list_competency/intern_list_add_competency');
					break;

				case 'intern_list_competency_edit_competency':
					view('industri/intern_list/intern_list_competency/intern_list_edit_competency');
					break;


				case 'intern_list_competency_detail':
					view('industri/intern_list/intern_list_competency/intern_list_competency_detail');
					break;


				case 'intern_list_registration':
					view('industri/intern_list/intern_list_registration');
					break;

				case 'intern_list_student':
					view('industri/intern_list/intern_list_student');
					break;

				case 'intern_list_lecturer':
					view('industri/intern_list/intern_list_lecturer');
					break;

					## # Jobdesk
				case 'intern_list_jobdesk':
					view('industri/intern_list/intern_list_jobdesk/intern_list_jobdesk');
					break;


					## # Discussion
				case 'intern_list_discussion':
					view('industri/intern_list/intern_list_discussion/intern_list_discussion');
					break;

				case 'intern_list_discussion_join':
					view('industri/intern_list/intern_list_discussion/intern_list_discussion_join');
					break;

				case 'intern_list_discussion_comment_summary':
					view('industri/intern_list/intern_list_discussion/intern_list_discussion_comment_summary');
					break;


					## # Logbook
				case 'intern_list_logbook':
					view('industri/intern_list/intern_list_logbook/intern_list_logbook');
					break;

				case 'intern_list_logbook_detail':
					view('industri/intern_list/intern_list_logbook/intern_list_logbook_detail');
					break;

					## # Atendance
				case 'intern_list_attendance':
					view('industri/intern_list/intern_list_attendance');
					break;

					## # Final Report
				case 'intern_list_final_report':
					view('industri/intern_list/intern_list_final_report');
					break;

					## # Student Feedback
				case 'intern_list_student_feedback':
					view('industri/intern_list/intern_list_student_feedback');
					break;

					## # Industry Feedback
				case 'intern_list_industry_feedback':
					view('industri/intern_list/intern_list_industry_feedback');
					break;



					## HRD
				case 'industry_supervisor_management':
					renderView('industry.supervisor_management.index', ['title' => 'Supervisor Management', 'routes' => 'industry_supervisor_management', 'route' => 'industry_supervisor_management']);
					break;

					// case 'industry_offer':
					// 	view('industri/kelola_offer');
					// break;

				case 'industry_offer':
					controller('view/OfferManagement');
					break;

				case 'offer_detail':
					view('industri/detail_offer');
					break;

					## # Intern Management => Punya HRD
				case 'internship_management':
					renderView('industry.internship_management.index', ['title' => 'Internship Management', 'route' => 'internship_management', 'routes' => 'internship_management']);
					// view('industri/intern_management/accepted_intern_management/accepted_intern_management');
					break;
				
				case 'finished_intern_management':
					view('industri/intern_management/finished_intern_management');
					break;


				case 'accepted_intern_management':
					view('industri/intern_management/accepted_intern_management/accepted_intern_management');
					break;

				case 'competency_intern_management':
					view('industri/intern_management/accepted_intern_management/competency_intern_management/competency_intern_management');
					break;

				case 'competency_detail_intern_management':
					view('industri/intern_management/accepted_intern_management/competency_intern_management/competency_detail_intern_management');
					break;



				case 'detail_intern_management':
					renderView('industry.internship_management.detail', ['title' => 'Student Detail', 'route' => 'internship_management', 'routes' => 'internship_management']);
						break;

				case 'mark_intern_management':
					renderView('industry.internship_management.mark',['title' => 'Mark Internship', 'route' => 'internship_management', 'routes' => 'internship_management']);
					break;

				case 'registration_intern_management':
					view('industri/intern_management/accepted_intern_management/registration_intern_management');
					break;

				case 'supervisor_intern_management':
					view('industri/intern_management/accepted_intern_management/supervisor_intern_management');
					break;

				case 'lecturer_intern_management':
					view('industri/intern_management/accepted_intern_management/lecturer_intern_management');
					break;

				case 'jobdesk_intern_management':
					view('industri/intern_management/accepted_intern_management/jobdesk_intern_management/jobdesk_intern_management');
					break;

				case 'discussion_intern_management':
					view('industri/intern_management/accepted_intern_management/discussion_intern_management/discussion_intern_management');
					break;

				case 'discussion_join_intern_management':
					view('industri/intern_management/accepted_intern_management/discussion_intern_management/discussion_join_intern_management');
					break;

				case 'discussion_comment_summary_intern_management':
					view('industri/intern_management/accepted_intern_management/discussion_intern_management/discussion_comment_summary_intern_management');
					break;


				case 'attendance_intern_management':
					view('industri/intern_management/accepted_intern_management/attendance_intern_management');
					break;
				case 'attendance_detail_intern_management':
					view('industri/intern_management/accepted_intern_management/attendance_detail_intern_management');
					break;

				case 'logbook_intern_management':
					view('industri/intern_management/accepted_intern_management/logbook_intern_management/logbook_intern_management');
					break;

				case 'logbook_detail_intern_management':
					view('industri/intern_management/accepted_intern_management/logbook_intern_management/logbook_detail_intern_management');
					break;

				case 'final_report_intern_management':
					view('industri/intern_management/accepted_intern_management/final_report_intern_management');
					break;

				case 'student_feedback_intern_management':
					view('industri/intern_management/accepted_intern_management/student_feedback_intern_management');
					break;


				case 'industry_feedback_intern_management':
					view('industri/intern_management/accepted_intern_management/industry_feedback_intern_management');
					break;

				case 'rejected_intern_management':
					view('industri/intern_management/rejected_intern_management');
					break;

				case 'add_industry_feedback':
					view('industri/intern_management/add_industry_feedback');
					break;



					## # Validate Intern Candidate
				case 'intern_candidate':
					renderView('industry.intern_candidate.index', ['title' => 'Intern Candidate', 'route' => 'intern_candidate', 'routes' => 'intern_candidate']);
					// view('industri/intern_candidate/intern_candidate');
					break;

				case 'validate_intern':
					renderView('industry.intern_candidate.validate', ['title' => 'Validate Intern', 'route' => 'intern_candidate', 'routes' => 'intern_candidate']);
					// view('industri/intern_candidate/validate_intern');
					break;

				case 'revalidate_intern_management':
					view('industri/intern_management/revalidate_intern_management');
					break;
			}
		} else

			// Jika tidak dapat $_GET['page']
			if (!isset($_GET['page']) || $_GET['page'] == "") {

				$role = $_SESSION['role'];

				if ($role === '' || $role === null) {
					renderView('landing.home', ['title' => 'Home', 'routes' => 'home']);
					// abort(400);
				} elseif ($role === 'Mahasiswa') {
					renderView('student.dashboard', ['title' => 'Student Dashboard', 'routes' => 'dashboard']);
				} elseif ($role === 'Dosen') {
					renderView('lecturer.dashboard', ['title' => 'Lecturer Dashboard', 'routes' => 'dashboard']);
				} elseif ($role === 'UPKPK') {
					renderView('cdc.dashboard', ['title' => 'CDC Dashboard', 'routes' => 'dashboard', 'route' => 'dashboard']);
				} elseif ($role === 'industri') {
					renderView('industry.dashboard', ['title' => 'Industry Dashboard', 'routes' => 'dashboard', 'route' => 'dashboard']);
				// 	view('industri/dashboard_industri');
				} else {
					controller('LandingController\LandingController', ['method' => 'other']);
				}
			} else {

				http_response_code(404);

				echo json_encode([
					'welcome_message' => $_SERVER['API_WELCOME'],
					'status' => 0,
					'message' => 'Invalid Request delete !'
				], JSON_PRETTY_PRINT);
			}
	}
