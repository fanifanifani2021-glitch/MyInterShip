<?php

$id_kampus = $_SESSION['CDC-Data']['id_kampus'];

$sql = "SELECT * FROM tb_kampus WHERE id_kampus = :id_kampus LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->bindParam(':id_kampus', $id_kampus, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() != 1) {
    make_swal2('error', 'Error', 'University data not found !');
    redirect_back(1);
    exit;
}

$univerity_data = $stmt->fetch(PDO::FETCH_ASSOC);

switch ($_GET['action']):
    default:
        view('exception/error_404');

        break;

    case 'delete':
        $id_hapus = base64_decode( $_POST['id_hapus'] );

        // Get Data
        $sql = "SELECT * FROM company_mou WHERE id_mou = :id_mou AND id_kampus = :id_kampus LIMIT 1";
        $stmt = $_SERVER['PDO']->prepare( $sql );


        $stmt->bindParam( 'id_mou', $id_hapus, PDO::PARAM_INT );
        $stmt->bindParam( 'id_kampus', $id_kampus, PDO::PARAM_INT );

        $stmt->execute();
        
        if ( $stmt->rowCount() != 1 ) {
            make_snackbar('(1) Check your submitted data and try again');
            redirect_back(1); exit;
        }

        $company_mou = $stmt->fetch( PDO::FETCH_ASSOC );

        $mou_dir = './upload/company_mou/';

        $where = array( 'id_mou' => $id_hapus );

        // DELETE
        $delete = table_delete2('company_mou', $where );

        if ( $delete === TRUE ) {

            if ( is_file( $mou_dir. $company_mou['mou_file'] ) ) {
                unlink( $mou_dir. $company_mou['mou_file'] );
            }

            make_swal2('success', 'Delete Data Success', 'Company MoU have been deleted successfully !');
            redirect('company_mou_management', 1);
            exit;
        } else {
            make_snackbar('(2) Check your submitted data and try again');
            redirect_back(1); exit;
        }

        break;


    case 'add':

        set_rules('company', 'Company', ['required', 'exists:company.id_company'], ['required' => 'This field is required', 'exists' => 'Company does not exist']);
        set_rules('mou_type', 'MoU Type', ['required', 'allowed_values:Internship'], ['required' => 'This field is required', 'allowed_values' => 'Value is not allowed']);
        set_rules('mou_date', 'MoU Date', ['required', 'valid_date'], ['required' => 'This field is required', 'valid_date' => 'Enter valid date']);
        set_rules('mou_description', 'MoU Description', ['required'], ['required' => 'This field is required']);
        set_rules('mou_file', 'MoU File', ['required', 'allowed_types:pdf', 'max_size:5MB'], ['required' => 'This field is required', 'allowed_types' => 'Only PDF files are allowed', 'max_size' => 'Maximum file size is 5MB']);

        $form = form_validation();

        if ( $form === false) {
            make_snackbar('(1) Check your submitted data and try again');
            redirect_back(1);
            exit;
        }

        $mou_file = escape_string(random_strings(15) . "-" . random_strings(15) . "-" . random_strings(15) . "-" . uniqid() . ".pdf");
        $mou_dir = './upload/company_mou/';

        if ( !is_dir($mou_dir) ) {
            mkdir( $mou_dir );
        }


        if (move_uploaded_file($_FILES['mou_file']['tmp_name'], $mou_dir . $mou_file) === FALSE) {
            error('mou_file', 'Failed to upload MoU File');

            make_snackbar('(2) Please check your submitted data and try again later');

            redirect_back(1);
            exit;
        }

        $data = array(
            'id_mou' => null,
            'id_kampus' => $id_kampus,
            'id_company' => $_POST['company'],
            'mou_type' => $_POST['mou_type'],
            'mou_date' => $_POST['mou_date'],
            'mou_description' => $_POST['mou_description'],
            'mou_status' => 'Active',
            'mou_file' => $mou_file,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => NULL
        );

        $add = table_insert2('company_mou', $data);

        if ($add === false) {

            if ( is_file( $mou_dir. $mou_file ) ) {
                unlink( $mou_dir. $mou_file);
            }

            make_snackbar('(3) Failed to add Company MoU, please try again later');
            redirect_back(1);
            exit;
        } else {
            make_swal2('success', 'Add Data Success', 'Company MoU have been added successfully !');
            redirect('company_mou_management', 1);
            exit;
        }


        break;

endswitch;
