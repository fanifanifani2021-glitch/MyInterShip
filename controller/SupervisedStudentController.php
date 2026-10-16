<?php

switch ( $method ?? $_GET['action'] ) :

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

    case 'details':

        try {
            
            $studentNIM = $_POST['studentNIM'];
            $sql = "SELECT si.*, le.name AS lecturer_name, ps.jenjang, ps.study_program FROM student_internship si LEFT JOIN program_study ps ON ps.kode_prodi = si.program_study LEFT JOIN lecturer le ON le.nim_nik_unit = si.nik_dospem WHERE si.nim = :nim LIMIT 1";
            $stmt = $_SERVER['PDO']->prepare($sql);
            $stmt->execute(['nim' => $studentNIM]);

            if ( $stmt->rowCount() != 1 ) {
                throw new Exception ;
            }

            // Fetch result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'welcome_message' => $_SERVER['API_WELCOME'],
                'status' => 200,
                'error' => 0,
                'message' => 'Student found !',
                'data' => $result
            ], JSON_PRETTY_PRINT);

        } catch ( Exception $e ) {
            echo json_encode([
                'welcome_message' => $_SERVER['API_WELCOME'],
                'status' => 400,
                'error' => 1,
                'message' => 'Student not found !'
            ], JSON_PRETTY_PRINT);

        }      
    break;


    case 'add':
        renderView('lecturer.internship_management.add', ['title' => 'Add Supervised Student', 'routes' => 'intern_student_management']);
		break;


    case 'store':  
        try {
            // Prepare SQL statement
            $sql = "UPDATE student_internship SET nik_dospem = :nik_dosen WHERE nim = :nim";
            $stmt = $_SERVER['PDO']->prepare($sql);

            // Execute the statement with parameters
            $stmt->execute([
                'nik_dosen' => $_POST['nik_dosen'],
                'nim' => $_POST['nim']
            ]);

            // Check if exactly one row was updated
            if ( $stmt ) {
            // Success handling
            make_swal2('success', 'Success', 'Supervised Student had been added');
            redirect('intern_student_management', 100);
            exit;

            } else {
                throw new Exception('No rows were updated or multiple rows affected.');

            }


        } catch (Exception $e) {
            // Error handling
            make_swal2('error', 'Failed', 'Please try again later');
            redirect_back(100);
            exit;
        }
    break;

endswitch;