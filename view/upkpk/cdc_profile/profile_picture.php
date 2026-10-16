<div class="card-body">

	<!-- <div class="card card-profile">
		<div class="card-header" style="background-image: url('./assets/img/blogpost.jpg'); height: 300px; background-size: cover;background-repeat: no-repeat;">
		</div>
	</div> -->


	<form id="form" action="index.php?form=cdc_profile&action=profile" method="POST" enctype="multipart/form-data" onload="disabled_form()"> <?= csrf() ?> <?= input_hidden('nim', $_SESSION['nim']) ?>

		<div class="form-group row">
			<label class="col-lg-3 col-md-3 col-sm-4 mt-sm-2">Current Profile Picture</label>

			<div class="col-lg-4 col-md-9 col-sm-8">
				<div class="avatar avatar-xxl">
					<img src="viewer.php?src=profile_picture" alt="CDC Profile Picture" class="avatar-img rounded-circle">
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="">New Profile Picture <span class="required-label">*</span></label>
			<input type="file" name="new_profile" disabled accept="image/png, image/jpg, image/jpeg" id="new_profile" class="form-control <?= show_error('new_profile') ?>" />

			<?= form_error('new_profile') ?>

		</div>


	</form>

	<div class="form-group">
		<?php if ($_SESSION['old']['open-form'] == 'open') : ?>
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



<?php if (old('open-profile') == 'open') : ?>
	<script>
		$(document).ready(function() {
			enable_form;
		});
	</script>
<?php endif; ?>

<script>
	function submit_form() {
		$('form#form').submit();
	}

	function confirmation() {
		// alert('Are you sure you want to cancel ? (Your change will not be saved)');
		SoloAlert.confirm({
			title: "Are you sure you want to cancel ?",
			body: "Changes you made will not be saved !",
			useTransparency: true,
			onOk: () => {
				disabled_form()
			}
		});

	}

	function disabled_form() {

		$("#edit_btn").prop("disabled", false);
		$("#edit_btn").prop("hidden", false);


		$("#cancel_btn").prop("hidden", true);
		$("#cancel_btn").prop("disabled", true);

		$("#save_btn").prop("disabled", true);

		$("#form :input").prop("disabled", true);
		$("#form").trigger("reset");

		$("small").empty();

		$("#form :input").removeClass("is-invalid border-danger is-valid border-success");

	}

	function enable_form() {


		$("#edit_btn").prop("disabled", true);
		$("#edit_btn").prop("hidden", true);


		$("#cancel_btn").prop("hidden", false);
		$("#cancel_btn").prop("disabled", false);


		$("#save_btn").prop("disabled", false);

		$("#form :input").prop("disabled", false);

		$("#form :input").removeClass("is-invalid border-danger is-valid border-success");


	}
</script>