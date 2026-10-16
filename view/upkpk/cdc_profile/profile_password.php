<?php $sql = "SELECT * FROM upkpk WHERE username = '" .$_SESSION['CDC_Data'] ['username']. "'"; 
$query = $_SERVER['DB']->query( $sql );

$data = $query->fetch_assoc();

?>

<div class="card-body">
    <form id="form" action="index.php?form=cdc_profile&action=password" method="POST"> <?= csrf() ?> <?= input_hidden('username', $data['username'] ) ?>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="form-group form-group-default">
                    <label>Current Password <b class="text-danger">*</b></label>
                    <input type="password" class="form-control" name="current_password" disabled placeholder="Enter Your Current Password" 
                    value="<?= old('current_password') ?>">
                    <?= form_error('current_password') ?>

                </div>
            </div>

			<div class="col-md-12">
                <div class="form-group form-group-default">
                    <label>New Password <b class="text-danger">*</b></label>
                    <input type="password" class="form-control" name="new_password" disabled placeholder="Enter Your New Password" 
                    value="<?= old('new_password') ?>">
                    <?= form_error('new_password') ?>

                </div>
            </div>

			<div class="col-md-12">
                <div class="form-group form-group-default">
                    <label>New Password Confirmation <b class="text-danger">*</b></label>
                    <input type="password" class="form-control" name="new_password2" disabled placeholder="Confirm Your New Password" 
                    value="<?= old('new_password2') ?>">
                    <?= form_error('new_password2') ?>

                </div>
            </div>



        </div>
    </form>

    <div class="text-left mt-3 mb-3">
        <?php if ( old('open-profile') == 'open' ) : ?>
            <button onclick="confirmation()" id="cancel_btn" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
            <button onclick="enable_form()" id="edit_btn" class="btn btn-warning" hidden><i class="fas fa-edit mr-1"></i>Edit</button>
            <button onclick="submit_form()" class="btn btn-success" id="save_btn"><i class="fas fa-key mr-1"></i>Change Password</button>
        <?php else : ?>
            <button onclick="confirmation()" id="cancel_btn" class="btn btn-danger" hidden><i class="fas fa-times mr-1"></i>Cancel</button>
            <button onclick="enable_form()" id="edit_btn" class="btn btn-warning"><i class="fas fa-edit mr-1"></i>Edit</button>
            <button onclick="submit_form()" class="btn btn-success" disabled id="save_btn"><i class="fas fa-key mr-1"></i>Change Password</button>
        <?php endif; ?>

    </div>
</div>
