<div class="card-body">


	<div class="form-group">
		<label class="">Current Profile Picture</label>
		<div class="form-group">


			<!-- <div class="col-lg-4 col-md-9 col-sm-8"> -->
			<div class="avatar avatar-xxl">
				<img src="viewer.php?src=profile_picture" alt="..." class="avatar-img rounded">
			</div>
			<!-- </div> -->

			<a target="_blank" download href="viewer.php?src=profile_picture&download=true" class="btn btn-info btn-sm"><i class="fas fa-download mr-2"></i>Download</a>
			
			<?php if ( !in_array($data['profile_picture'], ['profile.jpg', 'profile.png', null] ) ): ?>
			<button type="button" href="#" onclick="delete_profile()" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt mr-2"></i>Remove</button>
			<?php endif ?>
		</div>

	</div>


	<form id="form_profile" action="index.php?form=industry_profile&action=profile" method="POST" enctype="multipart/form-data" onload="disabled_form()"> <?= csrf() ?> <?= input_hidden('nim', $_SESSION['nim']) ?>


		<div class="form-group">
			<label class="">New Profile Picture <span class="required-label">*</span></label>
			<input type="file" name="new_profile" accept="image/png, image/jpg, image/jpeg" id="new_profile" onchange="validateFile(this, '2MB')" class="form-control <?= show_error('new_profile') ?>" />

			<?= form_error('new_profile') ?>
			<span class="text-sm float-right">Allowed file : png, jpeg and jpg | Max Size : 2MB</span>

		</div>


	</form>


	<div class="form-group">

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

</div>