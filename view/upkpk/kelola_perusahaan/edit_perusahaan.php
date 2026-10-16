<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Edit Company';
$route = 'company_management';
include "./view/upkpk/template/head.php";


$id_company = base64_decode($_GET['id_company']);

$sql = "SELECT * FROM company WHERE id_company = '$id_company'";
$company = $_SERVER['KONEKSI_DB']->query($sql);

if ($company->num_rows != 1) {
    make_swal2('error', 'Not Allowed', 'Company Data not found !');
    redirect_back(1);
    exit();
}

$company_data = $company->fetch_assoc();
?>

<body>
    <div class="wrapper">

        <?php include "./view/upkpk/template/main-header.php"; ?>
        <?php include "./view/upkpk/template/sidebar.php"; ?>

        <div class="main-panel">
            <div class="container">
                <div class="page-inner mt-2">


                    <div class="page-header">
                        <h4 class="page-title">Edit Company</h4>
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
                                <a href="index.php?page=company_management">Company Management</a>
                            </li>
                            <li class="separator">
                                <i class="flaticon-right-arrow"></i>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?page=company_detail&id=<?= base64_encode($id_company) ?>">Company Detail</a>
                            </li>
                            <li class="separator">
                                <i class="flaticon-right-arrow"></i>
                            </li>
                            <li class="nav-item">
                                <a href="#">Edit Company</a>
                            </li>
                        </ul>
                    </div>


                    <div class="row mt-2">
                        <div class="col-md-12">

                            <div class="card full-height">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h4 class="card-title">Edit Company</h4>
                                    </div>
                                </div>

                                <form action="index.php?form=company&aksi=edit" method="POST" id="form_edit">
                                    <?= csrf(); ?> <?= input_hidden('id_company', $id_company); ?>
                                    <div class="card-body form-row">


                                        <div class="form-group col-md-12">
                                            <label for="name">Name <label class="text-danger">*</label></label>
                                            <input type="text" placeholder="Fill Company Name" class="form-control <?= show_error('name') ?>" name="name" id="name" value="<?= $company_data['name'] ?>">
                                            <?= form_error('name'); ?>
                                        </div>

                                        <script type="text/javascript">
                                            function toggle_company() {
                                                var company_type = document.getElementById('type');
                                                var div_company_other = document.getElementById('div_company_other');
                                                var type_other = document.getElementById('type_other');


                                                if (company_type.value == 'Other') {
                                                    div_company_other.hidden = false;

                                                } else {
                                                    div_company_other.hidden = true;
                                                    type_other.value = '';
                                                }
                                            }
                                        </script>

                                        <div class="form-group col-md-12">
                                            <label for="type">Type <label class="text-danger">*</label></label>
                                            <select class="form-control <?= show_error('type') ?>" name="type" id="type" onchange="toggle_company()">
                                                <option hidden><?= $company_data['type'] ?></option>
                                                <!-- <option>Agency</option>
                                                <option>Design</option>
                                                <option>Education</option>
                                                <option>Engineering</option>
                                                <option>Finance</option>
                                                <option>Government</option>
                                                <option>Health</option>
                                                <option>IT and Telecomunication</option>
                                                <option>Logistic</option>
                                                <option>Marketing</option>
                                                <option>Media</option>
                                                <option>Other</option> -->

                                                <optgroup label="Types of Companies">
                                                    <option>Agency</option>
                                                    <option>Design</option>
                                                    <option>Education</option>
                                                    <option>Engineering</option>
                                                    <option>Finance</option>
                                                    <option>Government</option>
                                                    <option>Health</option>
                                                    <option>IT and Telecommunication</option>
                                                    <option>Logistic</option>
                                                    <option>Marketing</option>
                                                    <option>Media</option>
                                                    <option>Other</option>
                                                </optgroup>
                                                <optgroup label="Industry Specific Companies">
                                                    <option value="Advertising Agency">Advertising Agency</option>
                                                    <option value="Talent Agency">Talent Agency</option>
                                                    <option value="Employment Agency">Employment Agency</option>
                                                    <option value="Travel Agency">Travel Agency</option>
                                                    <option value="Engineering Firm">Engineering Firm</option>
                                                    <option value="Civil Engineering Company">Civil Engineering Company</option>
                                                    <option value="Mechanical Engineering Company">Mechanical Engineering Company</option>
                                                    <option value="Electrical Engineering Company">Electrical Engineering Company</option>
                                                    <option value="Bank">Bank</option>
                                                    <option value="Insurance Company">Insurance Company</option>
                                                    <option value="Investment Company">Investment Company</option>
                                                    <option value="Asset Management Company">Asset Management Company</option>
                                                    <option value="Electronic Manufacturing Company">Electronic Manufacturing Company</option>
                                                    <option value="Automotive Manufacturing Company">Automotive Manufacturing Company</option>
                                                    <option value="Food and Beverage Manufacturing Company">Food and Beverage Manufacturing Company</option>
                                                    <option value="Software Development Company">Software Development Company</option>
                                                    <option value="Cloud Technology Company">Cloud Technology Company</option>
                                                    <option value="Cybersecurity Company">Cybersecurity Company</option>
                                                    <option value="Hospital">Hospital</option>
                                                    <option value="Health Clinic">Health Clinic</option>
                                                    <option value="Pharmaceutical Company">Pharmaceutical Company</option>
                                                    <option value="Building Construction Company">Building Construction Company</option>
                                                    <option value="Infrastructure Construction Company">Infrastructure Construction Company</option>
                                                    <option value="School or Educational Institution">School or Educational Institution</option>
                                                    <option value="Educational Book Publishing Company">Educational Book Publishing Company</option>
                                                    <option value="E-commerce Company">E-commerce Company</option>
                                                    <option value="Retail Store">Retail Store</option>
                                                    <option value="Distribution Company">Distribution Company</option>
                                                    <option value="Film and Television Production Company">Film and Television Production Company</option>
                                                    <option value="Music Company">Music Company</option>
                                                    <option value="Gaming Company">Gaming Company</option>
                                                    <option value="Management Consulting">Management Consulting</option>
                                                    <option value="Financial Consulting">Financial Consulting</option>
                                                    <option value="Human Resources Consulting">Human Resources Consulting</option>
                                                    <option value="Agricultural Company">Agricultural Company</option>
                                                    <option value="Plantation Company">Plantation Company</option>
                                                </optgroup>
                                            </select>
                                            <?= form_error('type'); ?>
                                        </div>

                                        <div class="form-group col-md-12" hidden id='div_company_other'>
                                            <label for="type_other">Type (Other) <label class="text-danger">*</label></label>
                                            <input type="text" placeholder="Fill Company Type" class="form-control <?= show_error('type_other') ?>" name="type_other" id="type_other">
                                            <?= form_error('type_other'); ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="phone">Phone <label class="text-danger">*</label></label>
                                            <input type="text" placeholder="Fill Company Phone" class="form-control <?= show_error('phone') ?>" name="phone" id="phone" value="<?= $company_data['phone'] ?>">
                                            <?= form_error('phone'); ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="email">E-Mail <label class="text-danger">*</label></label>
                                            <input type="text" placeholder="Fill Company Email" class="form-control <?= show_error('email') ?>" name="email" id="email" value="<?= $company_data['email'] ?>">
                                            <?= form_error('email'); ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="website">Website</label>
                                            <input type="text" placeholder="Fill Company Website" class="form-control <?= show_error('website') ?>" name="website" id="website" value="<?= $company_data['website'] ?>">
                                            <?= form_error('website') ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="facebook">Facebook</label>
                                            <input type="text" placeholder="Fill Company Facebook" class="form-control <?= show_error('facebook') ?>" name="facebook" id="facebook" value="<?= $company_data['facebook'] ?>">
                                            <?= form_error('facebook') ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="twitter">Twitter</label>
                                            <input type="text" placeholder="Fill Company Twitter" class="form-control <?= show_error('twitter') ?>" name="twitter" id="twitter" value="<?= $company_data['twitter'] ?>">
                                            <?= form_error('facebook') ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="instagram">Instagram</label>
                                            <input type="text" placeholder="Fill Company Instagram" class="form-control <?= show_error('instagram') ?>" name="instagram" id="instagram" value="<?= $company_data['instagram'] ?>">
                                            <?= form_error('facebook') ?>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label for="logo">Logo <label class="text-danger">*</label></label>
                                            <input type="text" placeholder="Provide link to Company Logo" class="form-control <?= show_error('logo') ?>" name="logo" id="logo" value="<?= $company_data['logo'] ?>">
                                            <?= form_error('logo') ?>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label for="address">Address <label class="text-danger">*</label></label>
                                            <textarea type="text" placeholder="Fill Company Address" class="form-control <?= show_error('address') ?>" name="address" id="address" rows="3"><?= $company_data['address'] ?></textarea>
                                            <?= form_error('address'); ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="province">Province <label class="text-danger">*</label></label>
                                            <input type="text" placeholder="Fill Company Province" class="form-control <?= show_error('province') ?>" name="province" id="province" value="<?= $company_data['province'] ?>">
                                            <?= form_error('province'); ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="city">City <label class="text-danger">*</label></label>
                                            <input type="text" placeholder="Fill Company City" class="form-control <?= show_error('city') ?>" name="city" id="city" value="<?= $company_data['city'] ?>">
                                            <?= form_error('city'); ?>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label for="description">Description</label>
                                            <textarea class="form-control <?= show_error('description') ?>" placeholder="Fill Company Description" id="description" name="description" rows="3"><?= $company_data['description'] ?></textarea>
                                            <?= form_error('description') ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="status">Company Status <label class="text-danger">*</label></label>
                                            <select class="form-control <?= show_error('status') ?>" name="status" id="status">
                                                <option value="<?= $company_data['status'] ?>" hidden><?= ucwords($company_data['status']) ?></option>
                                                <option>Verified</option>
                                                <option>Not Verified</option>
                                                <option>Blacklist</option>
                                            </select>
                                            <?= form_error('status'); ?>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="access_type">Access Type <label class="text-danger">*</label></label>
                                            <select class="form-control <?= show_error('access_type') ?>" name="access_type" id="access_type">
                                                <option value="1" <?php checker($company_data['access_type'], [1], 'selected') ?>>1 - Can Make Offer</option>
                                                <option value="2" <?php checker($company_data['access_type'], [2], 'selected') ?>>2 - Can Not Make Offer</option>
                                            </select>
                                            <?= form_error('access_type'); ?>
                                        </div>

                                    </div>
                                    <div class="modal-footer justify-content-start">
                                        <button onclick="cancel_confirmation('index.php?page=company_detail&id=<?= base64_encode($id_company) ?>')" type="button" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
                                        <button onclick="reset_confirmation('form_edit')" type="button" class="btn btn-warning"><i class="fas fa-sync mr-1"></i>Reset</button>
                                        <button type="submit" class="btn btn-success"><i class="fas fa-edit mr-1"></i>Edit</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include "./view/upkpk/template/footer.php"; ?>

        </div>

    </div>

    <?php include "./view/upkpk/template/script.php"; ?>

    <!-- Soloalert -->
    <script src='./core/component/soloalert.js'></script>

    <!-- CKEDITOR -->
    <script src="./library/ckeditor/ckeditor.js"></script>

    <script>
        $(document).ready(function() {
            toggle_company();

            var description = CKEDITOR.replace('description', {
                language: 'en',
                extraPlugins: 'editorplaceholder',
                editorplaceholder: 'Enter company description',
                removeButtons: 'PasteFromWord,Styles,Format,Font,FontSize',
            });
        });

        $(document).ready(function() {

            setTimeout(function() {

                $('#cke_description').addClass('<?= show_error('description') ?>');

            }, 500);
        })
    </script>


    <!-- User Custom Script -->
    <script>
        // Untuk Inisiasi Konfirmasi Cancel
        function cancel_confirmation(location) {
            // alert('Are you sure you want to cancel ? (Your change will not be saved)');
            SoloAlert.confirm({
                title: "Are you sure you want to cancel ?",
                body: "Changes you made will not be saved !",
                useTransparency: true,
                onOk: () => {
                    window.location.href = location;
                }
            });

        }


        // Untuk Inisiasi Konfirmasi Reset
        function reset_confirmation(form) {

            SoloAlert.confirm({
                title: "Are you sure you want to reset ?",
                body: "Changes you made will be reset to default !",
                useTransparency: true,
                onOk: () => {
                    $("#" + form).trigger("reset");
                }
            });

        }
    </script>


</body>

</html>