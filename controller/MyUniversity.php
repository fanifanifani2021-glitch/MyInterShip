<?php



set_rules('name', 'Name', ['required'], ['required' => 'Cannot be empty !']);
set_rules('website', 'Website', ['required', 'valid_url'], ['required' => 'Cannot be empty !']);
set_rules('logo', 'Logo', ['required', 'valid_url'], ['required' => 'Cannot be empty !']);
$cek_form = form_validation();

if ($cek_form === false) {
  make_snackbar('Check your submitted data and try again later');
  make_old('ModalEdit');

  redirect_back(1); exit;
}

$data = array(
    'nama_kampus' => $_POST['name'],
    'website' => $_POST['website'],
    'logo' => $_POST['logo'],
);

$update = table_update('tb_kampus', $data, ['id_kampus' => $_SESSION['CDC_Data'] ['id_kampus']]);

if ( $update ) {
    reflash_all();
    make_swal2('success', 'Success', 'University Data has been updated');  
    redirect_referrer(1); exit;
} else {
    make_snackbar('Check your submitted data and try again later');
    make_old('ModalEdit');
  
    redirect_back(1); exit;
}