<form id="form_profile" action="index.php?form=industry_profile&action=identity" method="POST">
	<?= csrf() ?> <?= input_hidden('id_user_company', $_SESSION['id_user_company']) ?>
	<?= input_hidden('old_username', $data['username']) ?> <?= input_hidden('old_user_email', $data['user_email']) ?>
	<?= input_hidden('old_user_phone', $data['user_phone']) ?>

	<div class="card-body">
		<div class="row mt-3">
			<div class="col-md-6">
				<div class="form-group form-group-default">
					<label>Name <b class="text-danger">*</b></label>
					<input type="text" class="form-control" name="user_fullname" placeholder="Enter Your Full Name" value="<?= $data['user_fullname'] ?>">
					<?= form_error('user_fullname') ?>

				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group form-group-default">
					<label>Username <b class="text-danger">*</b></label>
					<input type="text" class="form-control" name="username" placeholder="Enter Your Username" value="<?= $data['username'] ?>">
					<?= form_error('username') ?>
				</div>
			</div>

		</div>

		<div class="row mt-3">

			<div class="col-md-6">
				<div class="form-group form-group-default">
					<label>E-Mail <b class="text-danger">*</b></label>
					<input type="email" class="form-control" name="user_email" placeholder="Enter Your E-Mail" value="<?= $data['user_email'] ?>">
					<?= form_error('user_email') ?>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group form-group-default">
					<label>Phone Number <b class="text-danger">*</b></label>
					<input type="text" class="form-control" name="user_phone" placeholder="Enter Your Phone Number" value="<?= $data['user_phone'] ?>">
					<?= form_error('user_phone') ?>

				</div>
			</div>
		</div>

	</div>
</form>


<div class="card-footer">
	<?php if ($_SESSION['old']['open-form'] != 'open') : ?>
		<button onclick="confirmation()" id="cancel_btn" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
		<button onclick="enable_form()" id="edit_btn" class="btn btn-warning" hidden><i class="fas fa-edit mr-1"></i>Edit</button>
		<button onclick="submit_form()" class="btn btn-success" id="save_btn"><i class="fas fa-save mr-1"></i>Save</button>
	<?php else : ?>
		<button onclick="confirmation()" id="cancel_btn" class="btn btn-danger" hidden><i class="fas fa-times mr-1"></i>Cancel</button>
		<button onclick="enable_form()" id="edit_btn" class="btn btn-warning"><i class="fas fa-edit mr-1"></i>Edit</button>
		<button onclick="submit_form()" class="btn btn-success" disabled id="save_btn"><i class="fas fa-save mr-1"></i>Save</button>
	<?php endif; ?>

</div>