<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting( false ); session_start();

$title = 'Company Profile'; $route = 'intern_student_management';
include "./view/dosen/template/head.php";

// Pengecekan Login Dosen Pembimbing
include './view/dosen/template/cek_login_dpm.php';

?>


<?php
// GET id_internship
$id_internship = base64_decode($_GET['id_internship']);


// Cek Internship
$sql = "SELECT * FROM internship WHERE id_internship = '$id_internship'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);


// Jika Internship tidak ditemukan
if ( $exec->num_rows != 1 ) {
	make_swal2('error', 'Not Allowed', 'Internship Data is not found !');
	redirect_back(1); exit();
}

$internship_data = $exec->fetch_assoc();
$nim = $internship_data['nim'];
$id_company = $internship_data['id_company'];

$sql2 = "SELECT * FROM company WHERE id_company = $id_company";
$data = $_SERVER['KONEKSI_DB']->query($sql2)->fetch_assoc();


if( in_array($data['status'], [1, 'verified', 'Verified'] ) ){
    $status = '<button type="button" class="btn btn-sm btn-success">'.$data['status'].'</button>';
}else

if( in_array($data['status'], [0, 'not verified', 'Not Verified'] ) ){
    $status = '<button type="button" class="btn btn-sm btn-danger">'.$data['status'].'</button>';
}else

if( in_array($data['status'], [2, 'blacklist', 'Blacklist'] ) ){
    $status = '<button type="button" class="btn btn-sm btn-dark">'.$data['status'].'</button>';
}

$status .= '<i class="fas fa-info-circle text-info ml-2" data-toggle="tooltip" data-placement="top" title="1. Verified : Company is verified and registered, can receive student internship from Polibatam; 2. Not Verified : Company is not yet verified and not registered; 3. Blacklist : Company is blacklist from receiving Polibatam`s student internship;"></i>';

if ( $data['access_type'] == '1' ){
    $data['access_type'] = '1 - Can Make Internship Offer';
} else if ( $data['access'] == 2 ) {
    $data['access_type'] = '2 - Can not Make Internship Offer';
} else {
    $data['access_type'] = 'Other';

}

?>

<body>
	<div class="wrapper">

		<?php include "./view/dosen/template/main-header.php"; ?>
		<?php include "./view/dosen/template/sidebar.php"; ?>	

		<div class="main-panel">
			<div class="container">
				<div class="page-inner mt-2">


                    <div class="page-header">
						<h4 class="page-title">Company Profile</h4>
						<ul class="breadcrumbs">
							<li class="nav-home">
								<a href="index.php">
									<i class="flaticon-home"></i>
								</a>
							</li>
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=intern_student_management">Intern Students Management</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=intern_student_detail&nim=<?= base64_encode($nim) ?>">Student Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=intern_student_internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Company Profile</a>
							</li>
						</ul>
					</div>


					<div class="row mt-2">

                        <!-- Company Detail -->
						<div class="col-md-12">

							<div class="card full-height">
								<div class="card-header">
									<div class="d-flex align-items-center float-left">
										<h4 class="card-title">Company Identity</h4>
									</div>                 
								</div>
								<div class="card-body">

                                    <table class="table table-bordered table-hover">
                                            <tbody>
                                                <tr>
                                                    <th width="30%">Name <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['name']; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Type <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['type']; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Phone <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['phone']; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Email <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['email']; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Website <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['website']; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Facebook <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['facebook']; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Twitter <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['twitter']; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Instagram <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['instagram']; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Logo <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['logo']; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Address <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['address']; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Province <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['province']; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">City <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['city']; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Status <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$status; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Access Type <span class="float-right">:</span></th>
                                                    <td width="70%"><span><?=$data['access_type']; ?></span></td>
                                                </tr>
                                            </tbody>
                                    </table>
                                
                                    <?php if ( $_SESSION['user_type'] == 'HRD' ): ?>

                                        <button class="btn btn-secondary btn-md float-left" data-toggle="modal" data-target="#ModalEdit"><i class="fas fa-edit mr-1"></i>Edit Company</button>
                                    <?php endif; ?>
                                </div>
                                    
							</div>
						</div>


                        <!-- Company Description -->
                        <div class="col-md-12">

							<div class="card full-height">
								<div class="card-header">
									<div class="d-flex align-items-center float-left">
										<h4 class="card-title">Description</h4>
									</div>                 
								</div>
								<div class="card-body">
                                    <?=$data['description']; ?>

                                </div>
                                    
							</div>
						</div>
					</div>
                    
				</div>

                
			</div>

			<?php include "./view/dosen/template/footer.php"; ?>

                                                <!-- modal edit -->
                                                <div class="modal fade" id="ModalEdit">
													<div class="modal-dialog modal-lg">
														<div class="modal-content">
															<div class="modal-header">
															<h5 class="modal-title">
																	<span class="fw-mediumbold">
																	Edit Company</span> 
																</h5>
															<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																<span aria-hidden="true">&times;</span>
															</button>
															</div>

                                                            <form action="index.php?form=my_company" method="POST"> <?= csrf(); ?>
                                                                <?= input_hidden('id_company', $_SESSION['id_company']) ?>

															<div class="modal-body form-row">


                                                            <div class="form-group col-md-12">
                                                            <label for="name">Name <label class="text-danger">*</label></label>
                                                            <input type="text" placeholder="Fill Company Name" class="form-control <?= show_error('name') ?>" 
                                                            name="name" id="name2" value="<?=$data['name']; ?>">
                                                            <?= form_error('name') ?>
                                                            </div>

                                                            <div class="form-group col-md-12">
                                                            <label for="type">Type <label class="text-danger">*</label></label>
                                                            <select class="form-control <?= show_error('company_type') ?>" name="company_type" id="type2">
                                                                <option hidden><?=$data['type']; ?></option>
                                                                <option>agency</option>
                                                                <option>design</option>
                                                                <option>education</option>
                                                                <option>engineering</option>
                                                                <option>finance</option>
                                                                <option>government</option>
                                                                <option>health</option>
                                                                <option>IT and telco</option>
                                                                <option>logistic</option>
                                                                <option>marketing</option>
                                                                <option>media</option>
                                                                <option>others</option>
                                                            </select>
                                                            <?= form_error('company_type'); ?>
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                            <label for="phone">Phone <label class="text-danger">*</label></label>
                                                            <input type="text" placeholder="Fill Company Phone" 
                                                                class="form-control <?= show_error('phone') ?>" name="phone" id="phone2" value="<?=$data['phone']; ?>">
                                                            <?= form_error('phone'); ?>
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                            <label for="email">E-Mail <label class="text-danger">*</label></label>
                                                            <input type="text" placeholder="Fill Company Email" class="form-control <?= show_error('email') ?>" name="email" id="email2" value="<?=$data['email']; ?>">  
                                                            <?= form_error('email'); ?>  
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                            <label for="website">Website</label>
                                                            <input type="text" placeholder="Fill Company Website" class="form-control <?= show_error('website') ?>" name="website" id="website2" value="<?=$data['website']; ?>">    
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                            <label for="facebook">Facebook</label>
                                                            <input type="text" placeholder="Fill Company Facebook" class="form-control <?= show_error('facebook') ?>" name="facebook" id="facebook2" value="<?=$data['facebook']; ?>">    
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                            <label for="twitter">Twitter</label>
                                                            <input type="text" placeholder="Fill Company Twitter" class="form-control <?= show_error('twitter') ?>" name="twitter" id="twitter2" value="<?=$data['twitter']; ?>">    
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                            <label for="instagram">Instagram</label>
                                                            <input type="text" placeholder="Fill Company Instagram" class="form-control <?= show_error('instagram') ?>" name="instagram" id="instagram2" value="<?=$data['instagram']; ?>">    
                                                            </div>

                                                            <div class="form-group col-md-12">
                                                            <label for="header">Header <label class="text-danger">*</label></label>
                                                            <input type="text" placeholder="Fill Company Header" class="form-control <?= show_error('header') ?>" name="header" id="header2" value="<?=$data['header']; ?>">  
                                                            <?= form_error('header'); ?>  
                                                            </div>

                                                            <div class="form-group col-md-12">
                                                            <label for="address">Address <label class="text-danger">*</label></label>
                                                            <input type="text" placeholder="Fill Company Address" class="form-control <?= show_error('address') ?>" name="address" id="address2" value="<?=$data['address']; ?>">    
                                                            <?= form_error('address'); ?>
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                            <label for="province">Province <label class="text-danger">*</label></label>
                                                            <input type="text" placeholder="Fill Company Province" class="form-control <?= show_error('province') ?>" name="province" id="province2" value="<?=$data['province']; ?>">    
                                                            <?= form_error('province'); ?>
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                            <label for="city">City <label class="text-danger">*</label></label>
                                                            <input type="text" placeholder="Fill Company City" class="form-control <?= show_error('city') ?>" name="city" id="city2" value="<?=$data['city']; ?>">  
                                                            <?= form_error('city'); ?>  
                                                            </div>

                                                            <div class="form-group col-md-12">
                                                            <label for="description">Description <label class="text-danger">*</label></label>
                                                            <textarea class="form-control <?= show_error('description') ?>" placeholder="Fill Company Description" id="description2" name="description" rows="4"><?=$data['description']; ?></textarea>
                                                            <?= form_error('description'); ?>
                                                            </div>

                                                            </div>
                                                            <div class="modal-footer justify-content-between">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Close</button>
                                                            <button type="submit" class="btn btn-primary"><i class="fas fa-edit mr-1"></i>Edit</button>
                                                            </form>

															</div>
														</div>
														<!-- /.modal-content -->
												</div>
												<!-- /.modal-dialog -->
		</div>




		</div>
		
	</div>

	<?php include "./view/dosen/template/script.php"; ?>

</body>
</html>