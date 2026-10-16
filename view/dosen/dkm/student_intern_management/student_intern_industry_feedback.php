<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(false);
session_start();

$title = 'Industry Feedback';
$route = 'student_intern_management';
include "./view/dosen/template/head.php";


$id_internship = base64_decode($_GET['id_internship']);

// CEK INTERNSHIP
$sql = "SELECT * FROM internship WHERE id_internship = '$id_internship'";
$exec = $_SERVER['KONEKSI_DB']->query($sql);

if ($exec->num_rows != 1) {
	make_swal2('error', 'Not Allowed', 'Internship Data is not found !');
	redirect_back(1);
	exit;
}

$internship_data = $exec->fetch_assoc();
$nim = $internship_data['nim'];

$sql = mysqli_query($koneksi_db, "SELECT * FROM industry_feedback WHERE id_internship = $id_internship");
$row = mysqli_fetch_assoc($sql);


?>

<body>
	<div class="wrapper">

		<?php include "./view/dosen/template/main-header.php"; ?>
		<?php include "./view/dosen/template/sidebar.php"; ?>


		<div class="main-panel">

			<div class="container">
				<div class="page-inner">

					<div class="page-header">
						<h4 class="page-title">Industry Feedback</h4>
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
								<a href="index.php?page=student_intern_management">Students Management</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=student_intern_detail&nim=<?= base64_encode($internship_data['nim']) ?>">Student Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="index.php?page=student_internship_detail&id_internship=<?= base64_encode($id_internship) ?>">Internship Detail</a>
							</li>

							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">Industry Feedback</a>
							</li>
						</ul>
					</div>


					<!-- Form -->
					<div class="row" id="feedback_form">

						<div class="col-md-12">
							<div class="card full-height">
							<div class="card-header">
									<h4 class="card-title">Industry Feedback Form
										<div class="float-right">
											<button type="button" data-toggle="modal" data-target="#ModalRubrik" class="btn btn-sm btn-info"><i class="fas fa-info-circle mr-1"></i>Rubric</button>
										</div>

									</h4>
								</div>

								<div id="ModalRubrik" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" style="display: none;" aria-hidden="true">
									<div class="modal-dialog modal-lg" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="exampleModalLongTitle">Rubric</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">×</span>
												</button>
											</div>
											<div class="modal-body">

												<table class="table table-bordered">
													<thead>
														<td width='20%' valign=top>
															<p align=center style='text-align:center'><b><span style='font-size:9.0pt;font-family:"Cambria",serif'>Parameters</span></b></p>
														</td>
														<td width='30%' valign=top style='width:92.1pt;border:solid windowtext 1.0pt; border-left:none;padding:0mm 5.4pt 0mm 5.4pt'>
															<p align=center style='text-align:center'><b><span style='font-size:9.0pt;font-family:"Cambria",serif'>Excellent</span></b></p>
															<p align=center style='text-align:center'><b><span style='font-size:9.0pt;font-family:"Cambria",serif'>(76-100)</span></b></p>
														</td>
														<td width=133 valign=top style='width:92.15pt;border:solid windowtext 1.0pt; border-left:none;padding:0mm 5.4pt 0mm 5.4pt'>
															<p align=center style='text-align:center'><b><span style='font-size:9.0pt;font-family:"Cambria",serif'>Good</span></b></p>
															<p align=center style='text-align:center'><b><span style='font-size:9.0pt;font-family:"Cambria",serif'>(51-75)</span></b></p>
														</td>
														<td width=132 valign=top style='width:91.8pt;border:solid windowtext 1.0pt; border-left:none;padding:0mm 5.4pt 0mm 5.4pt'>
															<p align=center style='text-align:center'><b><span style='font-size:9.0pt;font-family:"Cambria",serif'>Fair</span></b></p>
															<p align=center style='text-align:center'><b><span style='font-size:9.0pt;font-family:"Cambria",serif'>(26-50)</span></b></p>
														</td>
														<td valign=top style='width:84.7pt;border:solid windowtext 1.0pt; border-left:none;padding:0mm 5.4pt 0mm 5.4pt'>
															<p align=center style='text-align:center'><b><span style='font-size:9.0pt;font-family:"Cambria",serif'>Poor</span></b></p>
															<p align=center style='text-align:center'><b><span style='font-size:9.0pt;font-family:"Cambria",serif'>(0-25)</span></b></p>
														</td>
													</thead>
													<tbody>

														<tr>
															<td width=142 valign=top style='width:106.35pt;border:solid windowtext 1.0pt; border-top:none;padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif'>Ethics</span></p>
															</td>
															<td width=123 valign=top style='width:92.1pt;border-top:none;border-left: none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt; padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif'>Attitude
																		and behavior can be <span style='color:black'>set as role model</span></span><span style='font-size:9.0pt;font-family:"Cambria",serif'> </span><span style='font-size:9.0pt;font-family:"Cambria",serif'>for others, personality
																		that meets professional ethics</span></p>
															</td>
															<td width=123 valign=top style='width:92.15pt;border-top:none;border-left: none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt; padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif'>Attitude
																		and behavior<span style='color:black'> </span></span><span lang=EN-US style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>meet</span><span lang=EN-US style='font-size:9.0pt;font-family:"Cambria",serif'> </span><span style='font-size:9.0pt;font-family:"Cambria",serif'>the standard ethics and
																		able to peform good personality</span></p>
															</td>
															<td width=122 valign=top style='width:91.8pt;border-top:none;border-left: none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt; padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif'>Attitude
																		and behavior are acceptable, and do not have negative impact for the
																		organization</span></p>
															</td>
															<td valign=top>
																<p><span lang=EN-US style='font-size:9.0pt;font-family:"Cambria",serif;
																	color:black'>Demontrates</span><s><span lang=EN-US style='font-size:9.0pt;
																	font-family:"Cambria",serif'> </span></s><span style='font-size:9.0pt;
																	font-family:"Cambria",serif'>bad attitude and behavior, and</span><span style='font-size:9.0pt;font-family:"Cambria",serif'> <span lang=EN-US style='color:black'>has</span></span><span lang=EN-US style='font-size:9.0pt;
																	font-family:"Cambria",serif;color:black'> </span><span style='font-size:9.0pt;
																	font-family:"Cambria",serif;color:black'>negative impact on the organization</span></p>
															</td>
														</tr>
														<tr>
															<td width=142 valign=top style='width:106.35pt;border:solid windowtext 1.0pt;
																border-top:none;padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span lang=EN-US style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>C</span><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>ore competency skills</span></p>
															</td>
															<td width=123 valign=top style='width:92.1pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>Able to</span><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'> <span lang=EN-US>demonstrate </span></span><span lang=EN-US style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>exceptional core </span><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>competenc</span><span lang=EN-US style='font-size:9.0pt;
																font-family:"Cambria",serif;color:black'>ies</span><span lang=EN-US style='font-size:9.0pt;font-family:"Cambria",serif;color:black'> </span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>and provide
																		value added for the organization</span></p>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>&nbsp;</span></p>
															</td>
															<td width=123 valign=top style='width:92.15pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>Able to </span><span lang=EN-US style='font-size:9.0pt;
																font-family:"Cambria",serif;color:black'>demonstrate core </span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>competenc</span><span lang=EN-US style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>ies
																		well</span><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>, </span><span lang=EN-US style='font-size:9.0pt;font-family:
																"Cambria",serif;color:black'>however</span><span lang=EN-US style='font-size:
																9.0pt;font-family:"Cambria",serif;color:black'> </span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>ha</span><span lang=EN-US style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>s</span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'> not
																		contributed to provide value added for the organization</span></p>
															</td>
															<td width=122 valign=top style='width:91.8pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>Able to </span><span lang=EN-US style='font-size:9.0pt;
																font-family:"Cambria",serif;color:black'>demonstrate</span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'> sufficient
																		core competencies, </span><span lang=EN-US style='font-size:9.0pt;font-family:
																"Cambria",serif;color:black'>but</span><span style='font-size:9.0pt;
																font-family:"Cambria",serif;color:black'> still need significant improvement</span></p>
															</td>
															<td valign=top>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>Failed to demonstrate core competencies as required in the field</span></p>
															</td>
														</tr>
														<tr>
															<td width=142 valign=top style='width:106.35pt;border:solid windowtext 1.0pt;
																border-top:none;padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span lang=SV style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>Foreign language</span><span lang=SV style='font-size:9.0pt;
																font-family:"Cambria",serif;color:black'> </span><span style='font-size:9.0pt;
																font-family:"Cambria",serif;color:black'>proficiency </span></p>
															</td>
															<td width=123 valign=top style='width:92.1pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>Able to communicate both </span><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>orally</span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'> and </span><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>in
																		writing</span><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'> using one </span><span lang=EN-US style='font-size:9.0pt;
																font-family:"Cambria",serif;color:black'>of </span><span style='font-size:
																9.0pt;font-family:"Cambria",serif;color:black'>foreign language</span><span lang=EN-US style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>s</span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'> fluently in
																		accordance with best practices</span></p>
															</td>
															<td width=123 valign=top style='width:92.15pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>Able to communicate </span><span lang=EN-US style='font-size:
																9.0pt;font-family:"Cambria",serif;color:black'>both </span><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>orally</span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'> and </span><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>in
																		writing </span><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>using one </span><span lang=EN-US style='font-size:9.0pt;
																font-family:"Cambria",serif;color:black'>of </span><span style='font-size:
																9.0pt;font-family:"Cambria",serif;color:black'>foreign language</span><span lang=EN-US style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>s moderately</span></p>
															</td>
															<td width=122 valign=top style='width:91.8pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>Able to communicate both </span><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>orally</span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'> and in
																		writing using one</span><span lang=EN-US style='font-size:9.0pt;font-family:
																"Cambria",serif;color:black'> of</span><span style='font-size:9.0pt;
																font-family:"Cambria",serif;color:black'> foreign language mixed with a local
																		language</span></p>
															</td>
															<td valign=top>
																<p><span lang=EN-US style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>Unable </span><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>to communicate both </span><span lang=EN-GB style='font-size:
																9.0pt;font-family:"Cambria",serif;color:black'>orally</span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'> and in
																		writing using one</span><span lang=EN-US style='font-size:9.0pt;font-family:
																"Cambria",serif;color:black'> of</span><span style='font-size:9.0pt;
																font-family:"Cambria",serif;color:black'> foreign language </span></p>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>&nbsp;</span></p>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>&nbsp;</span></p>
															</td>
														</tr>
														<tr>
															<td width=142 valign=top style='width:106.35pt;border:solid windowtext 1.0pt;
																border-top:none;padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>Information&nbsp;&nbsp;</span><span lang=EN-US style='font-size:
																9.0pt;font-family:"Cambria",serif;color:black'>t</span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>echnology literacy</span></p>
															</td>
															<td width=123 valign=top style='width:92.1pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>Able t</span><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>o recognize, access</span><span lang=EN-US style='font-size:
																9.0pt;font-family:"Cambria",serif;color:black'>, </span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>evaluate</span><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>, </span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>synthesize</span><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>, and
																		use information technology application to improve job performance</span></p>
															</td>
															<td width=123 valign=top style='width:92.15pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif'>Quickly
																		adapt with the information technology in the organization</span></p>
															</td>
															<td width=122 valign=top style='width:91.8pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif'>Do
																		not find him/herself in trouble with the information technology in the organization</span></p>
															</td>
															<td valign=top>
																<p><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>Unable </span><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>to use information technology in the organization</span></p>
															</td>
														</tr>
														<tr>
															<td width=142 valign=top style='width:106.35pt;border:solid windowtext 1.0pt;
																border-top:none;padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span lang=SV style='font-size:9.0pt;font-family:"Cambria",serif'>Comunication
																		skill</span><span style='font-size:9.0pt;font-family:"Cambria",serif'> </span></p>
															</td>
															<td width=123 valign=top style='width:92.1pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>A</span><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>ble to effectively </span><span lang=EN-US style='font-size:
																9.0pt;font-family:"Cambria",serif;color:black'>deliver</span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'> ideas to
																		others</span><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'> both orally and in writing</span><span style='font-size:9.0pt;
																font-family:"Cambria",serif;color:black'>, actively listen in conversation,
																		give and receive critical feedback and speak in public.</span></p>
															</td>
															<td width=123 valign=top style='width:92.15pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif'>Able
																		to understand and deliver ideas to others bot<span style='color:black'>h </span></span><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>orally
																		and in writing</span></p>
																<p><span lang=SV style='font-size:9.0pt;font-family:"Cambria",serif'>&nbsp;</span></p>
															</td>
															<td width=122 valign=top style='width:91.8pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>There is no misunderstanding when delivering and receiving ideas
																		to/from coworker</span></p>
																<p><s><span style='font-size:9.0pt;font-family:"Cambria",serif'><span style='text-decoration:none'>&nbsp;</span></span></s></p>
															</td>
															<td valign=top>
																<p><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>Unable </span><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>to understand others and fail to deliver ideas</span></p>
															</td>
														</tr>
														<tr>
															<td width=142 valign=top style='width:106.35pt;border:solid windowtext 1.0pt;
																border-top:none;padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif'>Teamwork</span></p>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif'>&nbsp;</span></p>
															</td>
															<td width=123 valign=top style='width:92.1pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif'>Able
																		to lead a teamwork, achieve the goals, receive support from the team member<span style='color:black'>,</span></span><span style='font-size:9.0pt;font-family:
																"Cambria",serif;color:black'> <span lang=EN-GB>and</span></span><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif'> </span><span style='font-size:9.0pt;font-family:"Cambria",serif'>resolve conflict</span></p>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif'>&nbsp;</span></p>
															</td>
															<td width=123 valign=top style='width:92.15pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif'>Able
																		to provide evidence in </span><span lang=EN-GB style='font-size:9.0pt;
																font-family:"Cambria",serif;color:black'>collaborating </span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>with</span><span style='font-size:9.0pt;font-family:"Cambria",serif'> other team member, <span style='color:black'>accomplish </span></span><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>assigned works</span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>, and </span><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>contribute
																		to the team</span></p>
															</td>
															<td width=122 valign=top style='width:91.8pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>Able to provide evidence </span><span lang=EN-US style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>in </span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>collaborating
																		with other team members, </span><span lang=EN-GB style='font-size:9.0pt;
																font-family:"Cambria",serif;color:black'>accomplish</span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'> assigned
																		work</span></p>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>&nbsp;</span></p>
																<p><s><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'><span style='text-decoration:none'>&nbsp;</span></span></s></p>
															</td>
															<td valign=top>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>Gives negative effects to the team</span></p>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>&nbsp;</span></p>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>&nbsp;</span></p>
																<p><s><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'><span style='text-decoration:none'>&nbsp;</span></span></s></p>
															</td>
														</tr>
														<tr>
															<td width=142 valign=top style='width:106.35pt;border:solid windowtext 1.0pt;
																border-top:none;padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif'>Personal
																		development</span></p>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif'>&nbsp;</span></p>
															</td>
															<td width=123 valign=top style='width:92.1pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif'>Willing
																		to learn new things, <span style='color:black'>take </span></span><span lang=EN-US style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>an</span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'> initiative to</span><span style='font-size:9.0pt;font-family:"Cambria",serif'> improve themselves</span></p>
															</td>
															<td width=123 valign=top style='width:92.15pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span style='font-size:9.0pt;font-family:"Cambria",serif'>Willing
																		to learn new things with a slight supervision</span></p>
															</td>
															<td width=122 valign=top style='width:91.8pt;border-top:none;border-left:
																none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
																padding:0mm 5.4pt 0mm 5.4pt'>
																<p><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>Does </span><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>not show resistance to learn new things, however needs to be
																		encouraged</span></p>
															</td>
															<td valign=top>
																<p><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>Does </span><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>not show sign</span><span lang=EN-GB style='font-size:9.0pt;
																font-family:"Cambria",serif;color:black'>s</span><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;color:black'> </span><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>of
																	</span><span style='font-size:9.0pt;font-family:"Cambria",serif;color:black'>willingness
																		and </span><span lang=EN-GB style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>initiative </span><span style='font-size:9.0pt;font-family:"Cambria",serif;
																color:black'>to learn new things</span></p>
															</td>
														</tr>
														</thead>
												</table>

											</div>
											<div class="modal-footer">
												<button type="button" data-dismiss="modal" class="btn btn-danger"><i class="fas fa-times mr-2"></i>Close</button>
											</div>
										</div>
									</div>
								</div>



								<form method="POST" id="form_feedback"><?= csrf(); ?>
									<input type="hidden" name="id_feedback" id="id_feedback" value="<?= $row['id_industry_feedback'] ?>">
									<input type="hidden" name="id_internship" id="id_internship" value="<?= $id ?>">
									
									
									<div class="card-body form-row">

										<div class="form-group col-md-12">
											<h4 class="font-weight-bold"><b>B.</b> Overall Comments</h4>
										</div>

										<div class="form-group col-md-12">
											<label>Overall Comments for the Intern <b class="text-danger">*</b></label>
											<textarea class="form-control <?= show_error('catatan_utk_mahasiswa') ?>" rows="4" name="catatan_utk_mahasiswa" disabled><?= $row['catatan_utk_mahasiswa'] ?></textarea>
											<?= form_error('catatan_utk_mahasiswa') ?>

										</div>

										<div class="form-group col-md-12">
											<label>Overall Comments for the Politeknik Negeri Batam <b class="text-danger">*</b></label>

											<textarea class="form-control <?= show_error('catatan_utk_poltek') ?>" rows="4" name="catatan_utk_poltek" disabled><?= $row['catatan_utk_poltek'] ?></textarea>

											<?= form_error('catatan_utk_poltek') ?>
										</div>

										<div class="form-group col-md-12">
											<label>Does the student’s internship performance meet the requirement for being new employee in your company/institution? <b class="text-danger">*</b></label>
											<textarea type="text" class="form-control <?= show_error('layak_diterima') ?>" rows="4" name="layak_diterima" id="layak_diterima" disabled><?= $row['layak_diterima'] ?></textarea>
											<?= form_error('layak_diterima') ?>
										</div>

										<div class="form-group col-md-12">
											<label>Does your company intend to recruit the intern immediately after he/she finished their internship? <b class="text-danger">*</b></label>
											<textarea type="text" class="form-control <?= show_error('langsung_diterima') ?>" rows="4" name="langsung_diterima" id="langsung_diterima" disabled><?= $row['langsung_diterima'] ?></textarea>
											<?= form_error('langsung_diterima') ?>
										</div>

									</div>

									<div class="separator-solid"></div>

									<div class="card-body form-row">

										<div class="form-group col-md-12">
											<h4 class="font-weight-bold"><b>C.</b> Evaluation Parameter</h4>
										</div>

										<div class="form-group col-md-12">
											<label>Final grade for student based on overall internship process (range 1 – 100) : <b class="text-danger">*</b></label>
											<input type="number" min="1" max="100" class="form-control <?= show_error('nilai_akhir') ?>" name="nilai_akhir" id="nilai_akhir" value="<?= $row['nilai_akhir'] ?>" disabled placeholder="1 - 100" />
											<?= form_error('nilai_akhir') ?>
										</div>


										<div class="form-group col-md-12">
											<button type="button" data-toggle="modal" data-target="#ModalRubrik" class="btn btn-sm btn-info"><i class="fas fa-info-circle mr-1"></i>Rubric</button>
										</div>

										<div class="form-group col-md-12 text-center">

											<div class="table-responsive">
												<table class="table table-bordered">
													<thead>
														<tr>
															<th class="text-center bg-info" width="5%" rowspan="2">No.</th>
															<th class="text-center bg-info" rowspan="2">Criteria</th>
															<th class="text-center bg-info" colspan="4">Mark</th>
														</tr>

														<tr>
															<th class="text-center bg-info">1 - Strongly Disagree</th>
															<th class="text-center bg-info">2 - Disagree</th>
															<th class="text-center bg-info">3 - Agree</th>
															<th class="text-center bg-info">4 - Strongly Agree</th>
														</tr>
													</thead>
													<tbody>

														<tr>
															<th>1.</th>


															<th>Ethics</th>
															<td class="text-center">
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="etika1" name="etika" value="1" <?php if ($row['etika'] == '1') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="etika1">1</label>
																	</div>
																</div>
															</td>

															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="etika2" name="etika" value="2" <?php if ($row['etika'] == '2') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="etika2">2</label>
																	</div>

															</td>

															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="etika3" name="etika" value="3" <?php if ($row['etika'] == '3') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="etika3">3</label>
																	</div>

																</div>
															</td>


															<td class="text-center">
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="etika4" name="etika" value="4" <?php if ($row['etika'] == '4') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="etika4">4</label>
																	</div>
																</div>
															</td>



														</tr>

														<tr>
															<th>2.</th>

															<th>Competency</th>
															<td class="text-center">
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="kompetensi1" name="kompetensi" value="1" <?php if ($row['keahlian_kompetensi'] == '1') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="kompetensi1">1</label>
																	</div>
																</div>
															</td>


															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="kompetensi2" name="kompetensi" value="2" <?php if ($row['keahlian_kompetensi'] == '2') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="kompetensi2">2</label>
																	</div>

																</div>
															</td>


															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="kompetensi3" name="kompetensi" value="3" <?php if ($row['keahlian_kompetensi'] == '3') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="kompetensi3">3</label>
																	</div>

																</div>
															</td>


															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="kompetensi4" name="kompetensi" value="4" <?php if ($row['keahlian_kompetensi'] == '4') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="kompetensi4">4</label>
																	</div>
																</div>
															</td>
														</tr>

														<tr>
															<th>3.</th>

															<th>Language</th>
															<td class="text-center">
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="bahasa1" name="bahasa" value="1" <?php if ($row['keahlian_bahasa'] == '1') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="bahasa1">1</label>
																	</div>
																</div>
															</td>


															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="bahasa2" name="bahasa" value="2" <?php if ($row['keahlian_bahasa'] == '2') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="bahasa2">2</label>
																	</div>

																</div>
															</td>


															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="bahasa3" name="bahasa" value="3" <?php if ($row['keahlian_bahasa'] == '3') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="bahasa3">3</label>
																	</div>

																</div>
															</td>

															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="bahasa4" name="bahasa" value="4" <?php if ($row['keahlian_bahasa'] == '4') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="bahasa4">4</label>
																	</div>
																</div>
															</td>
														</tr>

														<tr>
															<th>4.</th>
															<th>IT Usage</th>
															<td class="text-center">
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="it1" name="it" value="1" <?php if ($row['penggunaan_ti'] == '1') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="it1">1</label>
																	</div>

																</div>
															</td>

															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="it2" name="it" value="2" <?php if ($row['penggunaan_ti'] == '2') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="it2">2</label>
																	</div>

																</div>
															</td>

															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="it3" name="it" value="3" <?php if ($row['penggunaan_ti'] == '3') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="it3">3</label>
																	</div>

																</div>
															</td>

															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="it4" name="it" value="4" <?php if ($row['penggunaan_ti'] == '4') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="it4">4</label>
																	</div>
																</div>
															</td>
														</tr>

														<tr>
															<th>5.</th>
															<th width="30%">Communication</th>
															<td class="text-center">
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="komunikasi1" name="komunikasi" value="1" <?php if ($row['komunikasi'] == '1') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="komunikasi1">1</label>
																	</div>

																</div>
															</td>

															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="komunikasi2" name="komunikasi" value="2" <?php if ($row['komunikasi'] == '2') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="komunikasi2">2</label>
																	</div>

																</div>
															</td>

															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="komunikasi3" name="komunikasi" value="3" <?php if ($row['komunikasi'] == '3') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="komunikasi3">3</label>
																	</div>

																</div>
															</td>

															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="komunikasi4" name="komunikasi" value="4" <?php if ($row['komunikasi'] == '4') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="komunikasi4">4</label>
																	</div>
																</div>
															</td>
														</tr>

														<tr>
															<th>6.</th>
															<th width="30%">Team Work</th>
															<td class="text-center">
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="kerjasama1" name="kerjasama" value="1" <?php if ($row['kerjasama'] == '1') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="kerjasama1">1</label>
																	</div>

																</div>
															</td>

															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="kerjasama2" name="kerjasama" value="2" <?php if ($row['kerjasama'] == '2') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="kerjasama2">2</label>
																	</div>

																</div>
															</td>

															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="kerjasama3" name="kerjasama" value="3" <?php if ($row['kerjasama'] == '3') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="kerjasama3">3</label>
																	</div>

																</div>
															</td>

															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="kerjasama4" name="kerjasama" value="4" <?php if ($row['kerjasama'] == '4') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="kerjasama4">4</label>
																	</div>
																</div>
															</td>
														</tr>

														<tr>
															<th>7.</th>
															<th width="30%">Improvement</th>
															<td class="text-center">
																<div class="form-check form-check-inline">
																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="pengembangan1" name="pengembangan" value="1" <?php if ($row['pengembangan_diri'] == '1') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="pengembangan1">1</label>
																	</div>

																</div>
															</td>

															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="pengembangan2" name="pengembangan" value="2" <?php if ($row['pengembangan_diri'] == '2') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="pengembangan2">2</label>
																	</div>

																</div>
															</td>

															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="pengembangan3" name="pengembangan" value="3" <?php if ($row['pengembangan_diri'] == '3') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="pengembangan3">3</label>
																	</div>

																</div>
															</td>


															<td class="text-center">
																<div class="form-check form-check-inline">

																	<div class="custom-control custom-radio col-3">
																		<input type="radio" id="pengembangan4" name="pengembangan" value="4" <?php if ($row['pengembangan_diri'] == '4') echo 'checked' ?> class="custom-control-input" disabled>
																		<label class="custom-control-label" for="pengembangan4">4</label>
																	</div>
																</div>
															</td>


														</tr>

													</tbody>
												</table>
											</div>
										</div>

									</div>

									
								</form>


							</div>
						</div>


					</div>


				</div>
			</div>

			<?php include "./view/dosen/template/footer.php"; ?>

		</div>

	</div>

	<?php include "./view/dosen/template/script.php"; ?>

	<!-- Soloalert -->
	<script src='./core/component/soloalert.js'></script>

	<!-- User Custom Script -->
	<script>
		function confirmation() {
			// alert('Are you sure you want to cancel ? (Your change will not be saved)');
			SoloAlert.confirm({
				title: "Are you sure you want to cancel ?",
				body: "Changes you made will not be saved !",
				useTransparency: true,
				onOk: () => {
					window.location.href = "index.php";
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

			$("#form_register").trigger("reset");
		};
	</script>

</body>

</html>