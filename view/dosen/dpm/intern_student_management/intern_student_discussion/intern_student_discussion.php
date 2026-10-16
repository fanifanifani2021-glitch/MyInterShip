<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php 
error_reporting( false ); session_start();

$title = "Discussion"; $route = 'intern_student_management'; 
include "./view/dosen/template/head.php";

// Pengecekan Login Dosen Pembimbing
include './view/dosen/template/cek_login_dpm.php';

?>

<?php
$id_internship = base64_decode($_GET['id_internship']);
$nim = base64_decode($_GET['nim']);

// Cek Internship
$sql = "SELECT * FROM internship WHERE id_internship = :id_internship AND nim = :nim LIMIT 1";
$exec = $_SERVER['PDO']->prepare($sql);
$exec->execute([':nim' => $nim, ':id_internship' => $id_internship]);

if ($exec->rowCount() != 1) {
	make_swal2('error', 'Not Allowed', 'Internship Data is not found !');
	redirect_back(1);
	exit();
}

$internship_data = $exec->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM discussion WHERE id_internship = :id_internship";
$exec = $_SERVER['PDO']->prepare($sql);
$exec->execute([':id_internship' => $id_internship]);



?>

<body>
	<div class="wrapper">

        <?php include "./view/dosen/template/main-header.php";?>
        <?php include "./view/dosen/template/sidebar.php";?>
		
		<div class="main-panel">
			<div class="container">
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title">Discussion Internship</h4>
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
								<a href="index.php?page=intern_student_management">Students Management</a>
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
								<a href="#">Discussion</a>
							</li>

							


							
						</ul>
					</div>

					<div class="row">


						<!-- Internship History -->
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<div class="card-title">Discussion Topic

										<div class="float-right justify-content-beetwen">

											<button data-toggle="modal" data-target="#ModalAdd" class="btn btn-info btn-sm btn-round mr-1"><i class="fas fa-plus mr-1"></i>Add Data</button>
										</div>
                                    </div>
								</div>



								<div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel">Add Data</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
										</button>
									</div>
									
									<form action="index.php?form=discussion&action=add_topic" method="POST"> 
										<?= csrf(); ?> <?= input_hidden('id_internship' , $id_internship); ?> 
										<?= input_hidden('user_type' , 'dpm') ?> <?= input_hidden('user_id' , $_SESSION['nim_nik_unit'])?>
										<?= input_hidden('user_name' , $_SESSION['name'])?>
										<?= input_hidden('discussion_status' , 'Open')?>
									<div class="modal-body form-row">

										<div class="form-group col-md-12">
											<label class="label-control">Discussion Topic<b class="text-danger">*</b></label>
											<textarea class="form-control <?= show_error('discussion_topic')?>" name="discussion_topic"
											rows="5" placeholder="Type Discussion Topic"></textarea>
											<?= form_error('discussion_topic')?>
										</div>
									</div>
									<div class="modal-footer justify-content-between">
										<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-1"></i>Close</button>
										<button type="submit" class="btn btn-primary" ><i class="fas fa-save mr-1"></i>Save</button>
									</div>
									</form>


									</div>
								</div>
								</div>

								<div class="card-body">
									<div class="table-responsive">
										<table id="basic-datatables" class="display table table-striped table-hover" >

										<thead>
											<tr>
												<th>No.</th>
												<th>User Type</th>
												<th>User Name</th>
												<th>Total Comment</th>
												<th>Status</th>
												<!-- <th>Discussion Topic</th> -->
												<th>Action</th>
											</tr>
										</thead>

										<tbody>
											<?php


												foreach ( $exec as $i => $data ):

													$id_discussion = $data['id_discussion'];

													$sql = "SELECT COUNT(id_discussion) AS count_comment FROM discussion_comment WHERE id_discussion = :id_discussion";
													$stmt = $_SERVER['PDO']->prepare($sql);
													$stmt->execute([':id_discussion' => $id_discussion]);

													if ($stmt->rowCount() === 1) {
														$count_comment = $stmt->fetch(PDO::FETCH_ASSOC)['count_comment'];
													} else {
														$count_comment = 0;
													}


													if ($data['user_type'] == 'spv') {
														$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $data['user_type'] ];
													} else if ($data['user_type'] == 'hrd') {
														$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $data['user_type'] ];
													} else if ($data['user_type'] == 'dpm') {
														$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $data['user_type'] ];
													} else if ($data['user_type'] == 'dkm') {
														$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $data['user_type'] ];
													} else if ($data['user_type'] == 'mhs') {
														$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $data['user_type'] ];
													} else if ($data['user_type'] == 'cdc') {
														$user_type = $_SERVER['DISCUSSION_ACTOR'] [ $data['user_type'] ];
													} else {
														$user_type = $_SERVER['DISCUSSION_ACTOR'] [ 'anon' ];
													}



													if ( $data['discussion_status'] == 'Open' ) {
														$discussion_status = "<button class='btn btn-success btn-sm'>Open</button>";
													} else if ( $data['discussion_status'] == 'Pending' ) {
														$discussion_status = "<button class='btn btn-warning btn-sm'>Pending</button>";
													} else if ( $data['discussion_status'] == 'Closed' ) {
														$discussion_status = "<button class='btn btn-danger btn-sm'>Closed</button>";
													} else {
														$discussion_status = "<button class='btn btn-dark btn-sm'>" .$data['discussion_status'] . "</button>";
													}

											?>

											<tr>
												<td><?= $i + 1 ?>.</td>
												<td><?= $user_type ?></td>
												<td><?= $data['user_name'] ?></td>
												<td><?= $count_comment ?> Comment</td>
												<td><?= $discussion_status ?></td>
												<!-- <td><?= $data['discussion_topic'] ?></td> -->
												<td>
													<div class="btn-group btn-round">
														<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
															Action
														</button>
														<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

															<button class="dropdown-item" onclick="window.location.href = 'index.php?page=intern_student_discussion_join&nim=<?= base64_encode( $nim )?>&id_internship=<?= base64_encode($data['id_internship']) ?>&id_discussion=<?= base64_encode($data['id_discussion']) ?>'"><i class="fas fa-comments mr-2"></i>Join Discussion</button>


															<?php if ( $data['user_id'] == $_SESSION['nim_nik_unit']) :?>
															<div class="dropdown-divider"></div>

															<button class="dropdown-item" data-toggle="modal" data-target="#ModalEdit<?= $i ?>"><i class="fas fa-edit mr-2"></i>Edit Data</button>

															<button class="dropdown-item text-danger" onclick="confirm_delete( '<?= $data['id_discussion'] ?>' , '<?= $konfirmasi ?>')" ><i class="fas fa-trash-alt mr-2"></i>Delete Data</button>

															<?php endif; ?>


														</div>
													</div>
												</td>
											</tr>


											<?php if ( $data['user_id'] == $_SESSION['nim_nik_unit']) :?>

											<div class="modal fade" id="ModalEdit<?= $i ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
											<div class="modal-dialog modal-lg" role="document">
												<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title" id="exampleModalLabel">Edit Data</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
													</button>
												</div>
												
												<form action="index.php?form=discussion&action=edit_topic" method="POST"> 
													<?= csrf(); ?> <?= input_hidden('id_discussion' , $data['id_discussion']); ?> <?= input_hidden('loop' , $i); ?> 
												<div class="modal-body form-row">

													<div class="form-group col-md-12">
														<label class="label-control">Discussion Status<b class="text-danger">*</b></label>
														<select class="form-control <?= show_error('discussion_status')?>" name="discussion_status">
															<option hidden><?= $data['discussion_status']?></option>
															<option>Open</option>
															<option>Pending</option>
															<option>Closed</option>
														</select>
														
														<?= form_error('discussion_status')?>
													</div>

													<div class="form-group col-md-12">
														<label class="label-control">Discussion Topic<b class="text-danger">*</b></label>
														<textarea class="form-control <?= show_error('discussion_topic')?>" name="discussion_topic"
														rows="5" placeholder="Type Discussion Topic"><?= $data['discussion_topic']?></textarea>
														<?= form_error('discussion_topic')?>
													</div>
												</div>
												<div class="modal-footer justify-content-between">
													<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-1"></i>Close</button>
													<button type="submit" class="btn btn-primary" ><i class="fas fa-edit mr-1"></i>Edit</button>
												</div>
												</form>


												</div>
											</div>
											</div>
											<?php endif;?>



											<?php 
											endforeach;
											?>
										</tbody>



										</table>
									</div>
								</div>
							</div>
						</div>




					</div>
				</div>
			</div>
			<?php include './view/dosen/template/footer.php';?>




		</div>
		
	</div>

	<?php include "./view/dosen/template/script.php"; ?>
	

    <!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>

	<!-- SWAL 2 -->
	<link rel='stylesheet' href='./assets/sweetalert2.css'>
    <script src='./assets/sweetalert2.all.js'></script>

    <!-- AJAX Script -->
    <script>

        $(document).ready(function() { 

			$('#basic-datatables').DataTable({
			});

			<?php if ( old('ModalAdd') !== NULL ) : 
				echo "$('#ModalAdd').modal('show')";
			endif;?>

			<?php if ( old('ModalEdit') !== NULL ) : 
				echo "$('#ModalEdit".old('LoopEdit')."').modal('show')";
			endif;?>
        
        });

		let _token = $('meta[name="csrf-token"]').attr('content');

		function confirm_delete( id, week_num ) {
			swal.fire({
				title               : 'Delete Confirmation',
				text                :  'Are you sure you want to delete current discussion topic ?',
				icon                : 'question',
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
			}).then((result) => {
				/* Read more about isConfirmed, isDenied below */
				if (result.isConfirmed) {


					// AJAX
					$.ajax({
						// url: "./ajax/delDiscussion.php",

						url: "index.php?request=disscussion_delete",

						type: "POST",
						data: {
							'token': _token,
							'_method': 'DELETE',
							'id_discussion': id,
						},

						dataType: 'json',
					success: function ( response ) {
						window.location.reload(1);
					},
					error: function ( response ) {
						window.location.reload(1);
					}
					});
					
				}
			})

		}

    </script>





</body>
</html>