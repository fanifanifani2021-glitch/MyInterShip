<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();
?>
<?php

if ($coor == 1) {
	$route = 'intern_co_management';
} else {
	$route = 'intern_student_list';
}

include "./view/upkpk/template/head.php";


?>

<body>
	<div class="wrapper">

		<?php include "./view/upkpk/template/main-header.php"; ?>
		<?php include "./view/upkpk/template/sidebar.php"; ?>

		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Internship Detail</h4>
						<ul class="breadcrumbs">
							<li class="nav-home">
								<a href="index.php">
									<i class="flaticon-home"></i>
								</a>
							</li>

							<?php if ($coor == 1) : ?>
								<li class="separator">
									<i class="flaticon-right-arrow"></i>
								</li>
								<li class="nav-item">
									<a href="index.php?page=intern_co_management">Coordinator Management</a>
								</li>

								<li class="separator">
									<i class="flaticon-right-arrow"></i>
								</li>
								<li class="nav-item">
									<a href="index.php?page=detail_intern_co&nik=<?= base64_encode($coordinator_data['nim_nik_unit']) ?>">Coordinator Detail</a>
								</li>

								<li class="separator">
									<i class="flaticon-right-arrow"></i>
								</li>
								<li class="nav-item">
									<a href="index.php?page=student_detail_intern_co&nim=<?= base64_encode($student_data['nim']) ?>&coor=1">Student Detail</a>
								</li>

							<?php else : ?>


								<li class="separator">
									<i class="flaticon-right-arrow"></i>
								</li>
								<li class="nav-item">
									<a href="index.php?page=student_management">Student Management</a>
								</li>

								<li class="separator">
									<i class="flaticon-right-arrow"></i>
								</li>
								<li class="nav-item">
									<a href="index.php?page=student_detail_intern_co&nim=<?= base64_encode($student_data['nim']) ?>">Student Detail</a>
								</li>

							<?php endif; ?>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Internship Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>

							<li class="nav-item">
								<a href="#">Assign Supervisor</a>
							</li>



						</ul>
					</div>







					<div class="row">


						<!-- Internship Under Supervisor -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-body">
									<table class="table table-striped">


										<!-- If Internship status is yes -->
										<?php
										$row = $data_internship;

										$sql2 = "SELECT company.name AS company_name FROM company WHERE company.id_company = '" . $row['id_company'] . "' ";
										$company_name = $_SERVER['KONEKSI_DB']->query($sql2)->fetch_assoc()['company_name'];



										if ($student_data['nik_dospem'] == '' || $student_data['nik_dospem'] == '-') {
											$nik_dospem = 0;
											$dospem = '<button class="btn btn-danger btn-sm btn-round">Not Set</button>';
										} else {
											$nik_dospem = $student_data['nik_dospem'];

											$sql2 = "SELECT lecturer.name AS lecturer_name FROM lecturer WHERE nim_nik_unit = '$nik_dospem' ";
											$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);
											$dospem = mysqli_fetch_assoc($exec2)['lecturer_name'];
										}


										// if ($row['nik'] == '' || $row['nik'] == '-') {
										// 	$nik_dospem = 0;
										// 	$dospem = '<button class="btn btn-danger btn-sm btn-round">Not Set</button>';
										// } else {
										// 	$nik_dospem = $row['nik'];

										// 	$sql2 = "SELECT lecturer.name AS lecturer_name FROM lecturer WHERE nim_nik_unit = '$nik_dospem' ";
										// 	$exec2 = $_SERVER['KONEKSI_DB']->query($sql2);
										// 	$dospem = mysqli_fetch_assoc($exec2)['lecturer_name'];
										// }

										if ($row['id_user_company'] == '' || $row['id_user_company'] == '-') {
											$user_company = 0;
											$supervisor_industri = '<button class="btn btn-danger btn-sm btn-round">Not Set</button>';
										} else {
											$user_company = $row['id_user_company'];
											$company = $row['id_company'];

											$sql2 = "SELECT user_company.user_fullname AS supervisor_name FROM user_company WHERE user_company.id_user_company = '$user_company' ";
											$kompeni_data = $_SERVER['KONEKSI_DB']->query($sql2)->fetch_assoc();


											$supervisor_industri = $kompeni_data['supervisor_name'];

											$sql2 = "SELECT company.name AS company_name FROM company WHERE company.id_company = '$company' ";
											$kompeni_data = $_SERVER['KONEKSI_DB']->query($sql2)->fetch_assoc();
											$company_name = $kompeni_data['company_name'];
										}

										if (in_array($row['status'], ['YES', 'Yes', 'yes']) === true) {
											$accepted = 1;
											$status = 'Intern Accepted and Ongoing';
											$internship_period = $row['internship_period'] . " Month";
										} else 

												if (in_array($row['status'], ['FINISH', 'Finish', 'finish']) === true) {
											$accepted = 1;
											$status = 'Intern Accepted and Finished';
											$internship_period = $row['internship_period'] . " Month";
										} else
												if (in_array($row['status'], ['NO', 'No', 'no']) === true) {
											$accepted = 0;
											$status = 'Intern Not Accepted';
										} else 
												if (in_array($row['status'], ['WAITING', 'Waiting', 'waiting']) === true) {
											$accepted = 0;
											$status = 'Waiting for validation';
										} else {
											$accepted = NULL;
											$status = 'Not Registered';
										}

										$start_date = $row['start_date'];
										$end_date = $row['end_date'];


										?>
										<tr>
											<th>Registered Date</th>
											<td><?= date('D, d M Y', strtotime($row['timestamp_register'])) ?></td>
										</tr>

										<tr>
											<th>Company Name</th>
											<td><?= $company_name ?></td>
										</tr>

										<tr>
											<th>Internship Status</th>
											<td><?= $status ?></td>
										</tr>

										<?php if ($accepted == 1) : ?>


											<tr>
												<th>Internship Period</th>
												<td><?= date('D, d M Y', strtotime($start_date)) ?> until <?= date('D, d M Y', strtotime($end_date)) ?> (<?= $internship_period ?>)</td>
											</tr>

											<tr>
												<th>Industry Supervisor</th>
												<td><?= $supervisor_industri ?></td>
											</tr>

											<tr>
												<th>University Supervisor</th>
												<td><?= $dospem ?></td>
											</tr>


										<?php endif; ?>


									</table>




								</div>
							</div>

						</div>


					</div>


										
                    <div class="row">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Assign Supervisor</h4>
                                </div>

                                <form action="index.php?form=internship&action=assign_supervisor" method="post" id="form_add_lecturer">
                                    <?= csrf(); ?> <?= input_hidden('id_internship', $data_internship['id_internship'])  ?>
                                    <?= input_hidden('redirect', $_GET['redirect']) ?>
                                    
                                    <div class="card-body form-row">


                                        <div class="form-group col-md-6">
                                            <label>Industry Supervisor <b class="text-danger">*</b></label>


                                            <select id="industry_supervisor" name="industry_supervisor" required style="width:inherit;" class="<?= show_error('company') ?>">
                                                <option value="" hidden>Choose one . . .</option>

												<?php foreach ( $supervisor_industry as $key => $value ) : ?>
												<option value="<?= $value['id_user_company'] ?>" <?= old('industry_supervisor', $data_internship['id_user_company'] ) == $value['id_user_company'] ? 'selected' : ''; ?>><?= $value['user_fullname'] ?></option>
                                                <?php endforeach; ?>

                                            </select>

                                            <?= form_error('company') ?>

                                            <?php if (in_array($_SESSION['role'], ['CDC', 'UPKPK'])) : ?>
                                                <label class="text-sm">User Company does not exist ? <a href="<?= base_url('index.php?page=company_detail&id='.base64_encode( $company_data['id_company'] ).'&redirect=' .base64_encode( $redirect )) ?>#add_user">Add User Company</a></label>
                                            <?php endif; ?>

                                        </div>


										
                                        <div class="form-group col-md-6">
                                            <label>University Supervisor <b class="text-danger">*</b></label>


                                            <select id="university_supervisor" name="university_supervisor" required style="width:inherit;" class="<?= show_error('company') ?>">
                                                <option value="" hidden>Choose one . . .</option>

												<?php foreach ( $supervisor_university as $key => $value ) : ?>
												<option value="<?= $value['nim_nik_unit'] ?>" <?= old('university_supervisor', $student_data['nik_dospem'] ) == $value['nim_nik_unit'] ? 'selected' : ''; ?>><?= $value['name'] ?></option>
                                                <?php endforeach; ?>
                                            </select>

                                            <?= form_error('company') ?>

                                            <?php if (in_array($_SESSION['role'], ['CDC', 'UPKPK'])) : ?>
                                                <label class="text-sm">Lecturer does not exist ? <a href="<?= base_url('index.php?page=add_lecturer&redirect=' .base64_encode( $redirect )) ?>">Add Lecturer</a></label>
                                            <?php endif; ?>

                                        </div>



                                    </div>

                                    <div class="card-footer form-row">

                                        <div class="form-group col-md-12">
                                            <button type="button" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
                                            <button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i class="fas fa-sync mr-1"></i>Reset</button>
                                            <button type="submit" class="btn btn-success" id="submit_btn"><i class="fas fa-edit mr-1"></i>Assign</button>

                                        </div>
                                    </div>
                                </form>


                            </div>
                        </div>


                    </div>



				</div>
			</div>
			<?php include './view/upkpk/template/footer.php'; ?>




		</div>
		<!-- /.tutupmodal -->





	</div>

	</div>

	<?php include "./view/upkpk/template/script.php"; ?>

	<!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>


    <!-- Select2 -->
    <script src="./assets/js/plugin/select2/select2.full.min.js"></script>

    <!-- Soloalert -->
    <script src='./core/component/soloalert.js'></script>


    <!-- AJAX Script -->
    <script>
        $(document).ready(function() {

            $('#university_supervisor').select2({
                theme: "bootstrap"
            });

            $('#industry_supervisor').select2({
                theme: "bootstrap"
            });


            <?php if (old('university_supervisor', $nim) != '') : ?>
                $('#university_supervisor').val(<?= old('university_supervisor', $nim) ?>).select2({
                    theme: "bootstrap"
                }).trigger('change');
            <?php endif; ?>

            <?php if (old('industry_supervisor') != '') : ?>
                $('#industry_supervisor').val(<?= old('industry_supervisor') ?>).select2({
                    theme: "bootstrap"
                }).trigger('change');
            <?php endif; ?>




            $("[aria-labelledby='select2-student_nim-container']").addClass('<?= show_error('student_nim') ?>');
            $("[aria-labelledby='select2-company-container']").addClass('<?= show_error('company') ?>');

        });
    </script>


    <!-- User Custom Script -->
    <script>
        function confirmation() {
            // alert('Are you sure you want to cancel ? (Your change will not be saved)');
            SoloAlert.confirm({
                title: "Are you sure you want to cancel ?",
                body: "Changes you made will not be saved !",
                useTransparency: true,
                onOk: () => {
                    window.location.href = "<?= (in_array($_SESSION['role'], ['CDC', 'UPKPK'])) ? base_url('index.php?page=student_detail_intern_co&nim=' . base64_encode($nim)) : base_url('index.php?page=student_intern_detail&nim=' . base64_encode($nim)) ?>";
                }
            });

        }

        function reset_confirmation() {

            SoloAlert.confirm({
                title: "Are you sure you want to reset ?",
                body: "Changes you made will be reset to default !",
                useTransparency: true,
                onOk: () => {
                    form_reset();
                }
            });

        }


        function form_reset() {

            $("#form_add_lecturer").trigger("reset");

            $("small").empty();
        };
    </script>





</body>

</html>