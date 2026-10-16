<?php $sql = "SELECT * FROM upkpk WHERE username = '" .$_SESSION['CDC_Data'] ['username']. "'"; 
$query = $_SERVER['DB']->query( $sql );

$data = $query->fetch_assoc();

?>

<div class="card-body">
    <form id="form" action="index.php?form=cdc_profile&action=identity" method="POST" > <?= csrf() ?> <?= input_hidden('old_username', $data['username'] ) ?>
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="form-group form-group-default">
                    <label>Name <b class="text-danger">*</b></label>
                    <input type="text" class="form-control" name="name" disabled placeholder="Enter Your Full Name" 
                    value="<?= old('name', $data['name'] ) ?>">
                    <?= form_error('name') ?>

                </div>
            </div>


            <div class="col-md-6">
                <div class="form-group form-group-default">
                    <label>Username <b class="text-danger">*</b></label>
                    <input type="text" class="form-control" name="username" disabled placeholder="Enter Your Username" 
                    value="<?= old('username', $data['username'] ) ?>">
                    <?= form_error('name') ?>

                </div>
            </div>



        </div>
    </form>

    <div class="text-left mt-3 mb-3">
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

<?php if ( old('open-profile') == 'open' ) : ?>
	<script>
		$(document).ready(function(){
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
