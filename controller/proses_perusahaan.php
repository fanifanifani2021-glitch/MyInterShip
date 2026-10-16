<?php
error_reporting( false ); session_start();
$title = "Proses Perusahaan";
include "./controller/controller-init.php";

$aksi = $_GET['aksi'];


// Tambah Perusahaan
if ( $aksi == "tambah" ) {

    $type = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $_POST['type']); 
    $fill_important = mysqli_real_escape_string($_SERVER['KONEKSI_DB'], $_POST['fill_important']); 

    $redirect = base64_decode($_POST['redirect']);


    //set_rules( 'company_logo', 'Company Logo' , ['required', 'allowed_types:png|jpeg|jpg', 'max_size:100KB'], ['required' => 'Cannot be empty !', 'max_size' => 'Maximum file size is 100KB !' ,'allowed_types' => 'Allowed file type is .png , .jpeg and .jpg or the file is not valid !'] );
    set_rules( 'name', 'Name' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'type', 'Type' , ['required'], ['required' => 'Cannot be empty !'] );

    if ( $_POST['type'] == 'Other' ) {
        set_rules( 'type_other', 'Type' , ['required'], ['required' => 'Cannot be empty !'] );
        $type = mysqli_real_escape_string($koneksi_db,$_POST['type_other']);
    } else {
        $type = mysqli_real_escape_string($koneksi_db,$_POST['type']);
    }


    if ( $fill_important == 0 ) {

        // Input Not Allowed
        set_rules( 'website', 'Website' , ['allow_not_required'], ['required' => 'Cannot be empty !', 'valid_url' => 'Please enter valid website !'] );
        set_rules( 'facebook', 'Facebook' , ['allow_not_required'], ['required' => 'Cannot be empty !'] );
        set_rules( 'twitter', 'Twitter' , ['allow_not_required'], ['required' => 'Cannot be empty !'] );
        set_rules( 'instagram', 'Instagram' , ['allow_not_required'], ['required' => 'Cannot be empty !'] );
        set_rules( 'email', 'Email' , ['required'], ['required' => 'Cannot be empty !', 'valid_email' => 'Please enter valid email !'] );
        set_rules( 'phone', 'Phone' , ['required'], ['required' => 'Cannot be empty !'] );
        set_rules( 'logo', 'Logo' , ['required','valid_url'], ['required' => 'Cannot be empty !', 'valid_url' => 'Please enter valid url !'] );
    }


    set_rules( 'description', 'Description' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'address', 'Address' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'province', 'Province' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'city', 'City' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'status', 'Status' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'access_type', 'Access_type' , ['required'], ['required' => 'Cannot be empty !'] );

    $cek_form = form_validation();  


    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit;
    }


    $data = array(
        'id_company' => NULL, 'name' => $_POST['name'], 'type' => $type, 
        'phone' => $_POST['phone'], 'email' => $_POST['email'], 'website' => $_POST['website'], 
        'facebook' => $_POST['facebook'], 'twitter' => $_POST['twitter'], 'instagram' => $_POST['instagram'], 
        'logo' => $_POST['logo'], 'address' => $_POST['address'], 'province' => $_POST['province'], 
        'city' => $_POST['city'], 'description' => $_POST['description'], 'status' => $_POST['status'], 
        'access_type' => $_POST['access_type'],
        'id_kampus' => $_SESSION['CDC_Data'] ['id_kampus']
    );
    
    $sql = table_insert2('company', $data);

    if ( $sql === TRUE) {
        make_snackbar('Company has been added');
        
        // make_old('id_company', )

        // make_swal2('success', 'Add Data Success', 'Company Data have been successfully added !');
        
                
        $inserted_id = $_SERVER['PDO']->lastInsertId() ?? $_SERVER['KONEKSI_DB']->insert_id ?? 0;

        make_old('id_company', $inserted_id);
        make_old('company', $inserted_id);

        if ( $redirect ) {
            redirect( $redirect, 1); exit;
        } else {
            redirect('company_management', 1); exit();
        }
    }
    else {
        make_snackbar('Check your submitted data and try again later');

        // make_snackbar('Server under heavy load, please try again later !');
        // make_snackbar('Failed while querying data, please try again later !');
        redirect_back(1);
        exit;
    }


exit;
} else 


// Edit Perusahaan
if ( $aksi == "edit" ) {
    
    set_rules( 'id_company', 'id_company' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'name', 'Name' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'type', 'Type' , ['required'], ['required' => 'Cannot be empty !'] );

    if ( $_POST['type'] == 'Other' ) {
        set_rules( 'type_other', 'Type' , ['required'], ['required' => 'Cannot be empty !'] );
        $type = mysqli_real_escape_string($_SERVER['KONEKSI_DB'],$_POST['type_other']);
    } else {
        $type = mysqli_real_escape_string($_SERVER['KONEKSI_DB'],$_POST['type']);
    }


        set_rules( 'website', 'Website' , ['allow_not_required'], ['required' => 'Cannot be empty !', 'valid_url' => 'Please enter valid website !'] );
        set_rules( 'facebook', 'Facebook' , ['allow_not_required'], ['required' => 'Cannot be empty !'] );
        set_rules( 'twitter', 'Twitter' , ['allow_not_required'], ['required' => 'Cannot be empty !'] );
        set_rules( 'instagram', 'Instagram' , ['allow_not_required'], ['required' => 'Cannot be empty !'] );



    set_rules( 'phone', 'Phone' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'email', 'Email' , ['required'], ['required' => 'Cannot be empty !', 'valid_email' => 'Please enter valid email !'] );
    set_rules( 'logo', 'Logo' , ['required','valid_url'], ['required' => 'Cannot be empty !', 'valid_url' => 'Please enter valid url !'] );
    set_rules( 'description', 'Description' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'address', 'Address' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'province', 'Province' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'city', 'City' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'status', 'Status' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'access_type', 'Access_type' , ['required'], ['required' => 'Cannot be empty !'] );

    $cek_form = form_validation();  


    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit;
    }

    $where = array(
        'id_company' => $_POST['id_company'],
    );

    $data = array(
        'name' => $_POST['name'], 'type' => $type, 
        'phone' => $_POST['phone'], 'email' => $_POST['email'], 'website' => $_POST['website'], 
        'facebook' => $_POST['facebook'], 'twitter' => $_POST['twitter'], 'instagram' => $_POST['instagram'], 
        'logo' => $_POST['logo'], 'address' => $_POST['address'], 'province' => $_POST['province'], 
        'city' => $_POST['city'], 'description' => $_POST['description'], 'status' => $_POST['status'], 
        'access_type' => $_POST['access_type'],
    );
    
    $sql = table_update2( 'company', $data, $where);

    if ( $sql === TRUE) {
        make_snackbar('Company has been edited successfully !');

        // make_swal2('success', 'Edit Data Success', 'Company Data have been successfully edited !');
        redirect('company_detail&id='.base64_encode($_POST['id_company']), 1); 
        exit();
    }
    else {
        make_snackbar('Check your submitted data and try again later');
        // make_toast3('error', 'Edit Data Failed', 'Check the data you have submitted and try again later !');
        redirect_back(1);
        exit;
    }
exit;
} else


// Hapus Perusahaan
if ( $aksi == "hapus" ) {

    $id = mysqli_real_escape_string($koneksi_db,$_POST['id_hapus']);

    $sql = mysqli_query($koneksi_db, "DELETE FROM company WHERE id_company = '$id';" );
    
    if ( $sql === TRUE ) {
        // make_snackbar('Check your submitted data and try again later');

        make_swal2('success', 'Delete Data Success', 'Company Data have been successfully deleted !');
        redirect('company_management', 1); 
        exit();
    } else {
        // make_snackbar('Check your submitted data and try again later');
        make_swal2('error', 'Delete Data Fail', 'Failed when deleting Company Data !');
        redirect_back(1);
        exit;
    }
exit;
} else 


// Edit Validasi Perusahaan
if ($aksi == "editvalidasi"){
    $id = mysqli_real_escape_string($koneksi_db,$_POST['id_editcompany2']);
    $status = mysqli_real_escape_string($koneksi_db,$_POST['status']);
    $access_type = mysqli_real_escape_string($koneksi_db,$_POST['access_type']);
    
    set_rules( 'id_editcompany2', 'id_editcompany2' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'status', 'Status' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'access_type', 'Access_Type' , ['required'], ['required' => 'Cannot be empty !'] );

    $cek_form = form_validation();  

    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');
        redirect_back(1); exit;
    }

    $where = array(
        'id_company' => $_POST['id_editcompany2'],
    );

    $data = array(
        'status' => $_POST['status'], 'access_type' => $_POST['access_type'],
    );

    $sql = table_update( 'company', $data, $where );

    if ( $sql === TRUE) {
        reflash_all();
        make_snackbar('Company Validation has been updated');

        redirect('company_detail&id='. base64_encode( $_POST['id_editcompany2'] ), 1); 
        exit();
    }
    else {
        make_snackbar('Check your submitted data and try again later');
        // make_swal2('error', 'Update Validation Fail', 'Check your submitted data and try again later !');
        redirect_back(1); exit;
    }

} else


// Tambah User Company
if ($aksi == "add_usercompany"){
    
    set_rules( 'user_fullname', 'User_fullname' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'user_phone', 'User_phone' , ['allow_not_required'] );
    
    set_rules( 'user_email', 'User_email' , ['allow_not_required'] );
    
    // set_rules( 'user_phone', 'User_phone' , ['required', 'is_unique:user_company.user_phone'], ['required' => 'Cannot be empty !', 'is_unique' => 'Phone Number is already in use !'] );
    // set_rules( 'user_email', 'User_email' , ['required', 'is_unique:user_company.user_email', 'valid_email'], ['required' => 'Cannot be empty !', 'is_unique' => 'E-Mail is already in use !', 'valid_email' => 'E-Mail is not valid !'] );
    
    
    set_rules( 'user_type', 'user_type' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'username', 'username' , ['required', 'is_unique:user_company.username'], ['required' => 'Cannot be empty !', 'is_unique' => 'Username already taken !'] );

    $cek_form = form_validation();  

    if ( $_POST['user_type'] == 'HRD') {

        $sql = "SELECT * FROM user_company WHERE user_type = 'HRD' AND id_company = '".$_POST['id_company']."'";
        $exec = $_SERVER['KONEKSI_DB']->query($sql);

        if ( $exec->num_rows != 0 ){
            $cek_form = false;
            error('user_type', 'You can only have one HRD per company !');
        }

    }


    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');
        make_sound('error');


        make_old('ModalTambahUserCompany', 'open');
        redirect_back(1); exit;
    }

    // Data yang akan di-insert ke dalam tabel
    $data = array(
        'id_company' => $_POST['id_company'], 
        'user_fullname' => $_POST['user_fullname'], 
        'user_phone' => $_POST['user_phone'], 
        'user_email' => $_POST['user_email'], 
        'username' => $_POST['username'], 
        'password' => password_hash($_SERVER['INDUSTRY_DEFAULT_PASSWORD'], PASSWORD_DEFAULT), 
        'user_type' => $_POST['user_type'], 
        'profile_picture' => 'profile.jpg'
    );
    
    try {
        // Siapkan query untuk insert data
        $sql = "INSERT INTO user_company 
                (id_user_company, id_company, user_fullname, user_phone, user_email, username, password, user_type, profile_picture) 
                VALUES (NULL, :id_company, :user_fullname, :user_phone, :user_email, :username, :password, :user_type, :profile_picture)";
        
        // Prepare statement menggunakan PDO
        $stmt = $_SERVER['PDO']->prepare($sql);
    
        // Binding parameter dengan data dari $data
        $stmt->bindParam(':id_company', $data['id_company']);
        $stmt->bindParam(':user_fullname', $data['user_fullname']);
        $stmt->bindParam(':user_phone', $data['user_phone']);
        $stmt->bindParam(':user_email', $data['user_email']);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':user_type', $data['user_type']);
        $stmt->bindParam(':profile_picture', $data['profile_picture']);
    
        // Eksekusi statement
        $stmt->execute();
    
        reflash_all();
        make_swal2('success', 'User Company Added', 'User Company have been added successfully !');
            
        if ( !in_array($_POST['redirect'], ['', null] ) ) {
            redirect( base64_decode($_POST['redirect']), 1 ); exit;
        } else {
            redirect('company_detail&id='.base64_encode($_POST['id_company']), 1);  exit;
        }
            
            
    } catch (PDOException $e) {
        make_old('ModalTambahUserCompany', 'open');
        make_snackbar('Error : ' .$e->getMessage() );
    
        // make_swal2('error', 'User Company Not Added', 'Check data you have submitted and try again later !');
        redirect_back(1); exit;
    }





}else


// Edit User Company
if ($aksi == "edit_usercompany"){
    
    // die("hello");

    set_rules( 'id_user_company', 'id_user_company' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'loop', 'loop' , ['required'], ['required' => 'Cannot be empty !'] );

    set_rules( 'old_user_phone', 'old_user_phone' , ['not_required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'old_user_email', 'old_user_email' , ['not_required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'old_username', 'old_username' , ['required'], ['required' => 'Cannot be empty !'] );


    set_rules( 'edit_user_fullname', 'User_fullname' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'edit_user_phone', 'User_phone' , ['required', ], ['required' => 'Cannot be empty !', 'unique_except' => 'Phone Number is already in use !'] );
    set_rules( 'edit_user_email', 'User_email' , ['required', ], ['required' => 'Cannot be empty !', 'unique_except' => 'E-Mail is already in use !'] );
    set_rules( 'edit_user_type', 'user_type' , ['required'], ['required' => 'Cannot be empty !'] );
    set_rules( 'edit_username', 'username' , ['required', 'unique_except:' .$_POST['old_username']. '|user_company.username' ], ['required' => 'Cannot be empty !', 'unique_except' => 'Username already taken !'] );

    $cek_form = form_validation();  

    // if ( $_POST['edit_user_type'] == 'HRD') {

    //     $sql = "SELECT * FROM user_company WHERE user_type = 'HRD' AND id_company = '".$_POST['id_company']."'";
    //     $exec = $_SERVER['KONEKSI_DB']->query($sql);


    //     if ( $_POST['old_user_type'] != $_POST['edit_user_type'] && $exec->num_rows != 0 ){
    //         $cek_form = false;
    //         error('edit_user_type', 'You can only have one HRD per company !');
    //     }

    // }



    if ( $cek_form === false ) {
        make_snackbar('Check your submitted data and try again later');

        make_old('ModalEditUserCompany', 'open');
        make_old('LoopEdit', $_POST['loop']);
        redirect_back(1); exit;
    }



    $where = array(
        'id_user_company' => $_POST['id_user_company'],
    );

    $data = array(
        'user_fullname' => $_POST['edit_user_fullname'], 'user_phone' => $_POST['edit_user_phone'], 
        'user_email' => $_POST['edit_user_email'], 'username' => $_POST['edit_username'], 
        'user_type' => $_POST['edit_user_type']
    );
    

    $sql = table_update( 'user_company', $data, $where);

    if ( $sql === TRUE) {
        reflash_all();

        make_snackbar('User Company data has been edited');

        // make_swal2('success', 'User Company Edited', 'User Company have been edited successfully !');
        redirect_referrer(1); exit();

    } else {
        reflash_all();

        make_snackbar('Check your submitted data and try again later (2)');

        // make_swal2('error', 'User Company Not Edited', 'Check data you have submitted and try again later !');

        make_old('ModalEditUserCompany', 'open');
        make_old('LoopEdit', $_POST['loop']);

        redirect_back(1); exit;
    }


}else


// Edit User Company
if ($aksi == "reset_usercompany"){

    set_rules( 'id_user_company', 'id_user_company' , ['required'], ['required' => 'Cannot be empty !'] );

    $cek_form = form_validation();


    if ( $cek_form === false ) {
        make_old('ModalEditUserCompany', 'open');
        make_old('LoopEdit', $_POST['loop']);
        redirect_back(1); exit;
    }



    $where = array(
        'id_user_company' => $_POST['id_user_company'],
    );

    $data = array(
        'password' => password_hash( $_SERVER['INDUSTRY_DEFAULT_PASSWORD'] , PASSWORD_DEFAULT),
    );


    $sql = table_update( 'user_company', $data, $where);

    if ( $sql === TRUE) {
        reflash_all();
        make_snackbar('User Company Password has been resetted');
        // make_swal2('success', 'User Company Password Resetted', 'User Company Password have been resetted successfully !');
        redirect_referrer(1); exit();
    } else {
        reflash_all();
        make_snackbar('Failed to reset password, try again later !');
        // make_swal2('error', 'User Company Password Not Resetted', 'Please  try again later !');
        redirect_back(1); exit;
    }


}

else {
    make_snackbar('You do not have permission to perform this action !');

    // make_swal2('error', 'Not Allowed', 'You don`t have permission to access this page !');
    redirect_back(1); exit;
}

