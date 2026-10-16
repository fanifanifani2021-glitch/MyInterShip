


<?php $__env->startSection('main-content'); ?>

<?php
$sql = "SELECT * FROM user_company WHERE id_company = :id_company AND user_type NOT IN ('HRD') ORDER BY id_user_company DESC";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->execute(['id_company' => $_SESSION['id_company']]);

$supervisor_data = $stmt->fetchAll( PDO::FETCH_ASSOC );

$error_fullname = $_SESSION['error']['user_fullname'];
$count_fullname = count($error_fullname);
$error_userphone = $_SESSION['error']['user_phone'];
$count_userphone = count($error_userphone);
$error_useremail = $_SESSION['error']['user_email'];
$count_useremail = count($error_useremail);
$error_username = $_SESSION['error']['username'];
$count_username = count($error_username);

$error_fullname2 = $_SESSION['error']['user_fullname2'];
$count_fullname2 = count($error_fullname2);
$error_userphone2 = $_SESSION['error']['user_phone2'];
$count_userphone2 = count($error_userphone2);
$error_useremail2 = $_SESSION['error']['user_email2'];
$count_useremail2 = count($error_useremail2);
$error_username2 = $_SESSION['error']['username2'];
$count_username2 = count($error_username2);

?>


<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Supervisor Management</h2>
                    <h5 class="text-white op-7 mb-2">Managing Supervisor</h5>
                </div>
                <div class="ml-md-auto py-2 py-md-0">
                    <button class="btn btn-secondary btn-round" data-toggle="modal" data-target="#ModalTambah"><i class="fas fa-plus mr-2"></i> Add Supervisor</button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">

                <div class="card full-height">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Supervisor List</h4>
                        </div>
                    </div>
                    <div class="card-body">
                            <table id="basic-datatables" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Fullname</th>
                                        <th>Phone</th>
                                        <th>E-Mail</th>
                                        <th>Username</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-staff">
                                    <?php $__currentLoopData = $supervisor_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $no => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?= $no + 1; ?>.</td>
                                            <td><?= $row['user_fullname'] ?></td>
                                            <td><?= $row['user_phone'] ?></td>
                                            <td><?= $row['user_email'] ?></td>
                                            <td><?= $row['username'] ?></td>
                                            <td>

                                                <div class="btn-group btn-round">
                                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Action
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

                                                        <!-- <button data-toggle="modal" data-target="#ModalEdit<?= $row['id_user_company'] ?>" type="button" class="dropdown-item">
                                                        <i class="fas fa-info-circle mr-2"></i>Detail Supervisor
                                                    </button> 
                                                    
                                                    <div class="dropdown-divider"></div> -->


                                                        <button class="dropdown-item" data-toggle="modal" data-target="#ModalReset" onclick="modal_reset('<?php echo $row['id_user_company']; ?>')"><i class="fas fa-sync mr-2"></i>Reset Password</button>
                                                        <button data-toggle="modal" data-target="#ModalEdit<?= $row['id_user_company'] ?>" type="button" class="dropdown-item">
                                                            <i class="fas fa-edit mr-2"></i>Edit Supervisor
                                                        </button>
                                                        <button class="dropdown-item" data-toggle="modal" data-target="#ModalHapus" onclick="modal_hapus('<?php echo $row['id_user_company']; ?>')"><i class="fas fa-trash-alt mr-2"></i>Delete Supervisor</button>

                                                    </div>
                                                </div>



                                            </td>
                                        </tr>

                                        <!-- modal edit hrd -->
                                        <div class="modal fade" id="ModalEdit<?= $row['id_user_company'] ?>">
                                            <div class="modal-dialog modal-md">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <span class="fw-mediumbold">
                                                                Edit Supervisor</span>
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <form action="index.php?form=hrd&aksi=edit_spv" method="POST"> <?= csrf() ?>
                                                            <input type="hidden" name="id_user_company" value="<?= $row['id_user_company'] ?>" />
                                                            <input type="hidden" name="loop" value="<?= $row['id_user_company'] ?>" />
                                                            <input type="hidden" name="old_username" value="<?= $row['username'] ?>" />
                                                            <input type="hidden" name="old_user_phone" value="<?= $row['user_phone'] ?>" />
                                                            <input type="hidden" name="old_user_email" value="<?= $row['user_email'] ?>" />

                                                            <div class="form-group">
                                                                <label for="edit_user_fullname">FullName <label class="text-danger">*</label></label>
                                                                <input type="text" placeholder="Fill HRD Name" class="form-control <?= show_error('edit_user_fullname'); ?>  " name="edit_user_fullname" id="edit_user_fullname" value="<?= $row['user_fullname'] ?>">
                                                                <?= form_error('edit_user_fullname'); ?>
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="edit_user_phone">Phone <label class="text-danger">*</label></label>
                                                                <input type="text" placeholder="Fill HRD Phone" class="form-control <?= show_error('edit_user_email'); ?>  " name="edit_user_phone" id="edit_user_phone" value="<?= $row['user_phone'] ?>">
                                                                <?= form_error('edit_user_phone'); ?>
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="edit_user_email">E-Mail <label class="text-danger">*</label></label>
                                                                <input type="text" placeholder="Fill HRD Email" class="form-control <?= show_error('edit_user_email'); ?>  " name="edit_user_email" id="edit_user_email" value="<?= $row['user_email'] ?>">
                                                                <?= form_error('edit_user_email'); ?>
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="edit_username">Username <label class="text-danger">*</label></label>
                                                                <input type="text" placeholder="Fill HRD Username" class="form-control <?= show_error('edit_username'); ?>" name="edit_username" id="edit_username" value="<?= $row['username'] ?>">
                                                                <?= form_error('edit_username'); ?>
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
                                        <!-- /.tutupmodal -->

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>


            			<!-- modal tambah hrd -->
			<div class="modal fade" id="ModalTambah">
				<div class="modal-dialog modal-md">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">
								<span class="fw-mediumbold">
									Add Supervisor</span>
							</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">

							<form action="index.php?form=hrd&aksi=tambah_spv" method="POST">

								<input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>" />
								<input type="hidden" name="id_company" value="<?php echo $_SESSION['id_company'] ?>" />

								<div class="form-group">
									<label for="user_fullname">Fullname <label class="text-danger">*</label></label>
									<input type="text" placeholder="Fill Supervisor Fullname" name="user_fullname" id="user_fullname" class="form-control <?= show_error('user_fullname'); ?>">
									<?= form_error('user_fullname'); ?>
								</div>

								<div class="form-group">
									<label for="user_phone">Phone <label class="text-danger">*</label></label>
									<input type="text" placeholder="Fill Supervisor Phone" name="user_phone" id="user_phone" class="form-control <?= show_error('user_phone'); ?>">
									<?= form_error('user_phone'); ?>
								</div>

								<div class="form-group">
									<label for="user_email">E-Mail <label class="text-danger">*</label></label>
									<input type="text" placeholder="Fill Supervisor E-Mail" name="user_email" id="user_email" class="form-control <?= show_error('user_email'); ?>  ">
									<?= form_error('user_email'); ?>
								</div>

								
								<div class="form-group">
									<label for="username">Username <label class="text-danger">*</label></label>
									<input onchange="showLoginInfo()" type="text" placeholder="Fill Supervisor Username" name="username" id="username" class="form-control <?= show_error('username'); ?>">
									<?= form_error('username'); ?>
								</div>


								<div class="form-group">
									<label for="password">Password <label class="text-danger">*</label></label>
									<input onchange="showLoginInfo()" type="text" placeholder="Fill Supervisor Password" name="password" id="password" class="form-control <?= show_error('password'); ?>"
									value="<?= old('password', 'password') ?>"
									>
									<?= form_error('password'); ?>
								</div>

								<div class="form-group">
									<h3>Supervisor can login using</h3>
									<h4>Login Link : <a href="<?= base_url('index.php?page=login_industri') ?>">Login Industri</a></h4>
									<h4>Username : <span id="username-login"><?= old('username') ?></span></h4>
									<h4>Password : <span id="password-login"><?= old('password', 'password') ?></span></h4>
								</div>



								<script>
									function showLoginInfo() {
										var username = document.getElementById('username').value; 
										var password =  document.getElementById('password').value;

										document.getElementById('username-login').innerHTML = username;
										document.getElementById('password-login').innerHTML = password;
									}
								</script>

						</div>
						<div class="modal-footer justify-content-between">
							<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Close</button>
							<button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Save</button>
							</form>

						</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.tutupmodal -->

			<!-- modal hapus -->
			<div class="modal fade" id="ModalHapus" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header no-bd">
							<h5 class="modal-title">
								<span class="fw-mediumbold">
									Delete Data</span>
							</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<form action="index.php?form=hrd&aksi=hapus_spv" method="POST">
								<input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>">
								<input type="hidden" id="id_hapus" name="id_hapus" value="">
								Are you sure to delete current Supervisor ?
						</div>
						<div class="modal-footer justify-content-between">
							<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancel</button>
							<button type="submit" class="btn btn-primary pull-right"><i class="fas fa-trash mr-1"></i>Delete</button>
						</div>
						</form>

					</div>
				</div>
			</div>
			<!-- tutup modal -->

			<!-- modal hapus -->
			<div class="modal fade" id="ModalReset" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header no-bd">
							<h5 class="modal-title">
								<span class="fw-mediumbold">
									Reset Password</span>
							</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<form action="index.php?form=hrd&aksi=reset_spv" method="POST"> <?= csrf() ?>
								<input type="hidden" id="id_reset" name="id_reset" value="">
								Are you sure to want to reset password for current Supervisor ?
						</div>
						<div class="modal-footer justify-content-between">
							<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Cancel</button>
							<button type="submit" class="btn btn-primary pull-right"><i class="fas fa-sync mr-1"></i>Reset</button>
						</div>
						</form>

					</div>
				</div>
			</div>
			<!-- tutup modal -->
        </div>
    </div>
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>
<!-- Datatables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<!-- Datatables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<script>
    CKEDITOR.replace('description', {
        removeButtons: 'PasteFromWord,Styles,Format,Font,FontSize'
    , });
    CKEDITOR.replace('requirement', {
        removeButtons: 'PasteFromWord,Styles,Format,Font,FontSize'
    , });

</script>



<script>
    $(document).ready(function() {


        let table = $('#basic-datatables').DataTable({
            "responsive": true
            , "pageLength": 15
            , "lengthMenu": [10, 15, 25, 50]
        });



    });

    function modal_hapus(id) {
			var id_hapus = document.getElementById('id_hapus');
			id_hapus.value = id;
		}

		function modal_reset(id) {
			var id_reset = document.getElementById('id_reset');
			id_reset.value = id;
		}

</script>

<?php if(old('ModalTambah') !== NULL): ?>

<script>
    $(document).ready(function() {
        $('#ModalTambah').modal('show');
    });

</script>
<?php endif; ?>

<?php if(old('ModalEdit') !== NULL): ?>

<script>
    $(document).ready(function() {
        $("#ModalEdit<?php echo e(old('LoopEdit')); ?>").modal('show');
    });

</script>
<?php endif; ?>


<?php $__env->stopPush(); ?>

<?php echo $__env->make('templates.industry', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\myinternship\views/industry/supervisor_management/index.blade.php ENDPATH**/ ?>