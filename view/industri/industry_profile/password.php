
<form id="form_profile" action="index.php?form=industry_profile&action=password" method="POST"> <?= csrf()?>

	<?= input_hidden('id_user_company', $_SESSION['id_user_company'])?>

<div class="card-body form-row">

	<div class="form-group col-md-12">
		<label>Old Password <b class="text-danger">*</b></label>
		<input type="password" class="form-control <?= show_error('old_password') ?>" name="old_password" placeholder="Enter Your Old Password" value="<?= old('old_password') ?>">
		<?= form_error('old_password') ?>
	</div>
	
	<div class="form-group col-md-12">
		<label>New Password <b class="text-danger">*</b></label>
		<input type="password" class="form-control <?= show_error('new_password') ?>" name="new_password" placeholder="Enter Your New Password" value="<?= old('new_password') ?>">
		<?= form_error('new_password') ?>
	</div>
	
	<div class="form-group col-md-12">
		<label>Password Confirmation <b class="text-danger">*</b></label>
		<input type="password" class="form-control <?= show_error('new_password2') ?>" name="new_password2" placeholder="Enter Your New Password" value="<?= old('new_password2') ?>">
		<?= form_error('new_password2') ?>
	</div>






</div>



</form>

<div class="card-footer">
	<!-- <button type="submit" class="btn btn-success"><i class="fas fa-sync mr-1"></i>Change Password</button> -->
	<?php if ( $_SESSION['old'] ['open-form'] != 'open') : ?>
	<button onclick="confirmation()" id="cancel_btn" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
	<button onclick="enable_form()" id="edit_btn" class="btn btn-warning" hidden><i class="fas fa-edit mr-1"></i>Edit</button>
	<button onclick="submit_form()" class="btn btn-success" id="save_btn"><i class="fas fa-sync mr-1"></i>Change Password</button>
	<?php else : ?>
	<button onclick="confirmation()" id="cancel_btn" class="btn btn-danger" hidden><i class="fas fa-times mr-1"></i>Cancel</button>
	<button onclick="enable_form()" id="edit_btn" class="btn btn-warning"><i class="fas fa-edit mr-1"></i>Edit</button>
	<button onclick="submit_form()" class="btn btn-success" disabled id="save_btn"><i class="fas fa-sync mr-1"></i>Change Password</button>
	<?php endif; ?>

</div>
