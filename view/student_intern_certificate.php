<?php defined('BASE_PATH') or header('Location: /'); ?>

<!DOCTYPE html>

<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Student Certificate | <?= $_SERVER['APP_NAME'] ?></title>


	<!-- <link rel="stylesheet" href="./assets/css/bootstrap.min.css"> -->
	<!-- Template Main CSS File -->
	<!-- <link href="./appland-assets/css/style.css" rel="stylesheet"> -->

	<script src="./assets/js/plugin/webfont/webfont.min.js"></script>
	<script>
		WebFont.load({
			google: {
				"families": ["Lato:300,400,700,900"]
			},
			custom: {
				"families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
				urls: ['./assets/css/fonts.min.css']
			},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>


</head>

<?php

$bulan = array('1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April', '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus', '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember');
$today = date('d') . ' ' . $bulan[date('n')] . ' ' . date('Y');

$id_internship = base64_decode($_GET['id_internship']);

// Get Internship Data
$query = $_SERVER['KONEKSI_DB']->query("SELECT * FROM internship WHERE id_internship = '$id_internship' ");

if ($query->num_rows != 1) {
	make_swal2('error', 'Not Allowed', 'Internship data not found !');
	redirect_referrer(1);
	exit;
}

// Internship Data
$internship_data = $query->fetch_assoc();

$nim = $internship_data['nim'];
$query = $_SERVER['KONEKSI_DB']->query("SELECT * FROM student_internship WHERE nim = '$nim' ");

if ($query->num_rows != 1) {
	make_swal2('error', 'Not Allowed', 'Internship data not found !');
	redirect_referrer(1);
	exit;
}

// Internship Data
$student_data = $query->fetch_assoc();


$id_company = $internship_data['id_company'];
$query = $_SERVER['KONEKSI_DB']->query("SELECT * FROM company WHERE id_company = '$id_company' ");

if ($query->num_rows != 1) {
	make_swal2('error', 'Not Allowed', 'Internship data not found !');
	redirect_referrer(1);
	exit;
}

// Internship Data
$company_data = $query->fetch_assoc();


// Get Prodi
$kode_prodi = $student_data['program_study'];

$sql = "SELECT * FROM `profile_jobdesc` WHERE kode_prodi = '$kode_prodi'";
$profile_jobdesc = $_SERVER['KONEKSI_DB']->query($sql);
if ($profile_jobdesc->num_rows < 1) {
	make_swal2('error', 'Not Allowed', 'Internship data not found !');
	redirect_referrer(1);
	exit;
}


// Student Competency
$sql = "SELECT * FROM `profile_student_jobdesc` WHERE id_internship = '$id_internship'";
$student_competency = $_SERVER['KONEKSI_DB']->query($sql);
// if ($student_competency->num_rows < 1) {
// 	make_swal2('error', 'Not Allowed', 'Competency data not found !');
// 	redirect_referrer(1);
// 	exit;
// }



?>


<!-- Bagian depan sertifikat -->

<body>

	<style type="text/css">
		.totals-row td {
			border-right: none !important;
			border-left: none !important;
		}


		td {
			white-space: normal;
		}

		.items-table td,
		#notes {
			white-space: normal;
		}

		.totals-row td strong,
		.items-table th {
			white-space: normal;
		}
	</style>
	<style type="text/css">
		.is_logo {
			display: none;
		}
	</style>


	<div id="editor" class="edit-mode-wrap">
		<style type="text/css">
			.is_logo {
				display: none;
			}
		</style>
		<style type="text/css">
			* {
				margin: 0;
				padding: 0;
			}

			body {
				background: #fff;
				font-family: Arial, Helvetica, sans-serif;
				font-size: 12px;
				line-height: 20px;

			}

			#extra {
				text-align: right;
				font-size: 22px;
				font-weight: 700
			}

			.invoice-wrap {


				width: 1200px;
				margin: 0 auto;
				background: #FFF;
				color: #000
			}

			.invoice-inner {
				border: 10px solid transparent;
				padding: 15px;
				border-image: url('./assets/border.png') 30 round;


				/* margin: 0 15px; */
				/* padding: 20px 0 */
			}

			.invoice-address {
				border-top: 3px double #000000;
				margin: 25px 0;
				padding-top: 25px;
			}

			.bussines-name {
				font-size: 18px;
				font-weight: 100
			}

			.invoice-name {
				font-size: 22px;
				font-weight: 700
			}

			.listing-table th {
				background-color: #e5e5e5;
				border-bottom: 1px solid #555555;
				border-top: 1px solid #555555;
				font-weight: bold;
				text-align: left;
				padding: 6px 4px
			}

			.listing-table td {
				border-bottom: 1px solid #555555;
				text-align: left;
				padding: 5px 6px;
				vertical-align: top
			}

			.total-table td {
				border-left: 1px solid #555555;
			}

			.total-row {
				background-color: #e5e5e5;
				border-bottom: 1px solid #555555;
				border-top: 1px solid #555555;
				font-weight: bold;
			}

			.row-items {
				margin: 5px 0;
				display: block
			}

			.notes-block {
				margin: 50px 0 0 0
			}


			.items-table {
				border: 1px solid #555555;
				border-collapse: collapse;
				width: 100%
			}

			.items-table td,
			.items-table th {
				border: 1px solid #555555;
				padding: 4px 5px;
				text-align: left
			}

			.items-table th {
				background: #f5f5f5;
			}

			.totals-row .wide-cell {
				border: 1px solid #fff;
				border-right: 1px solid #555555;
				border-top: 1px solid #555555
			}
		</style>


		<div class="invoice-wrap">
			<div class="invoice-inner">

				<table border="0" cellpadding="0" cellspacing="10" width="100%">
					<tbody>



						<tr>
							<td align="center" valign="center">
								<!-- <h1 style="font-size: 15pt;">LOGO POLITEKNIK NEGERI BATAM</h1> -->
								<img src="./assets/poltek_panjang.png" style="transform:(0.5)">
							</td>
						</tr>


						<tr>
							<td align="center" valign="center">
								<p style="font-size: 10pt;">MAGANG INDUSTRI TERSTRUKTUR Tahun <?= date('Y') ?> <br> PIN : 1234567890</p>
							</td>
						</tr>


						<tr></tr>
						<tr></tr>

					</tbody>
				</table>


				<table border="0" cellpadding="0" cellspacing="10" width="100%">
					<tbody>
						<tr>
							<td align="center" valign="center">
								<h3 style="font-size: 20pt;">SERTIFIKAT MAGANG</h3>
							</td>
						</tr>

						<tr>
							<td align="center" valign="center">
								<p style="font-size: 12pt;">diberikan kepada</p>
							</td>
						</tr>

						<tr></tr>
						<tr></tr>

					</tbody>
				</table>


				<table border="0" cellpadding="0" cellspacing="10" width="100%">
					<tbody>
						<tr>
							<td align="center" valign="center">
								<h4 style="font-size: 28pt;"><?= strtoupper($student_data['name']) ?></h4>
							</td>
						</tr>

						<tr>
							<td align="center" valign="center">
								<p style="font-size: 12pt;">NIM : <?= $nim ?></p>
							</td>
						</tr>

					</tbody>
				</table>

				<table border="0" cellpadding="0" cellspacing="10" width="100%">
					<tbody>
						<tr>
							<td style="word-wrap:break-word" align="center" valign="center">
								<p style="font-size: 13pt;">
									Dengan ini dinyatakan telah menyelesaikan program <b>MAGANG INDUSTRI TERSTRUKTUR</b> selama 1 (satu) tahun
								</p>

							</td>
						</tr>
						<tr>
							<td style="word-wrap:break-word" align="center" valign="center">
								<p style="font-size: 13pt;">
									dari 17 Januari 2021 sampai 17 Juni 2021 di perusahaan <b><?= strtoupper($company_data['name']) ?></b>
								</p>
							</td>
						</tr>

						<tr>
							<td style="word-wrap:break-word" align="center" valign="center">
								<p style="font-size: 13pt;">
									sesuai dengan persyaratan Capaian Pembelajaran Umum Matakuliah (CPUMK) Program Studi Teknik Informatika.</p>

								</p>

							</td>

						</tr>

						<tr></tr>
						<tr></tr>
						<tr></tr>

					</tbody>
				</table>

				<table border="0" cellpadding="0" cellspacing="10" width="100%">
					<tbody>

						<tr>
							<td align="center" valign="center">
								<p style="font-size: 13pt;">
									Batam, <?= $today ?>
								</p>
							</td>
						</tr>

						<tr></tr>
						<tr></tr>
						<tr></tr>

					</tbody>
				</table>


				<table border="0" cellpadding="0" cellspacing="10" width="100%">
					<tbody>

						<tr>
							<td align="center" valign="center">
								<p style="font-size: 13pt;">
									Ketua Jurusan,
								</p>
							</td>

							<td align="left" valign="center">
								<p style="font-size: 13pt;">
									<!-- Mahasiswa, -->
								</p>
							</td>

							<td align="left" valign="center">
								<p style="font-size: 13pt;">
									Direktur,
								</p>
							</td>
						</tr>

						<tr></tr>
						<tr></tr>
						<tr></tr>
						<tr></tr>
						<tr></tr>
						<tr></tr>
						<tr></tr>
						<tr></tr>
						<tr></tr>
						<tr></tr>
						<tr></tr>
						<tr></tr>


					</tbody>
				</table>

				<table border="0" cellpadding="0" cellspacing="10" width="100%">
					<tbody>

						<tr>
							<td align="center" valign="center">
								<p style="font-size: 15pt;">
									Muhammad Zainuddin, S.Tr.AB
								</p>
							</td>

							<td align="center" valign="center">
								<p style="font-size: 15pt;">
									<!-- <?= strtoupper($student_data['name']) ?> -->
								</p>
							</td>

							<td align="center" valign="center">
								<p style="font-size: 15pt;">
									Uuf Brajawidagda, ST., MT., Ph.D
								</p>
							</td>
						</tr>






					</tbody>
				</table>


			</div>
		</div>


	</div>
	<style>
		body {
			background: #EBEBEB;
		}

		.invoice-wrap {
			box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
			/* margin-bottom: 20px; */
		}

		#mobile-preview-close a {
			position: fixed;
			left: 20px;
			/* bottom: 30px; */
			background-color: #27c24c;
			font-weight: 600;
			outline: 0 !important;
			line-height: 1.5;
			border-radius: 3px;
			font-size: 14px;
			padding: 7px 10px;
			border: 1px solid #27c24c;
			text-decoration: none;
		}

		#mobile-preview-close img {
			width: 20px;
			height: auto;
		}

		#mobile-preview-close a:nth-child(2) {
			left: 190px;
			background: #f5f5f5;
			border: 1px solid #9f9f9f;
			color: #555 !important;
		}

		#mobile-preview-close a:nth-child(2) img {
			height: auto;
			position: relative;
			top: 2px;
		}

		.invoice-wrap {
			padding: 20px;
		}


		@media print {
			.page-break {
				height: 0;
				page-break-before: always;
				margin: 0;
				border-top: no;
			}

			#mobile-preview-close a {
				display: none;
			}

			/* .invoice-wrap {
				0
			} */

			body {
				background: none;
				height: 80mm;
				width: 180mm;
			}

			.invoice-wrap {
				box-shadow: none;
				margin-bottom: 0px;
			}

		}
	</style>

	<div id="mobile-preview-close">
		<a style="color: #fff !important;" onclick="window.location = document.referrer"> <i class="fas fa-arrow-left fa-1x"></i> Kembali</a>
		<a href="javascript:window.print();"> <i class="fas fa-print fa-1x"></i> Cetak </a>

	</div>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>


	<script type="text/javascript">
		document.ready = window.print();
		var beforePrint = function() {};


		function onClick() {
			var pdf = new jsPDF('p', 'pt', 'letter');
			pdf.canvas.height = 72 * 11;
			pdf.canvas.width = 72 * 8.5;

			pdf.fromHTML(document.body);

			pdf.save('test.pdf');
		};

		var element = document.getElementById("clickbind");
		element.addEventListener("click", onClick);

		//var afterPrint = function() {
		//	document.location.href='/invoices/thankyou/0';
		//};

		if (window.matchMedia) {
			var mediaQueryList = window.matchMedia('print');
			mediaQueryList.addListener(function(mql) {
				if (mql.matches) {
					beforePrint();
				} else {
					afterPrint();
				}
			});
		}
		window.onbeforeprint = beforePrint;
		window.onafterprint = afterPrint;

		(function(i, s, o, g, r, a, m) {
			i['GoogleAnalyticsObject'] = r;
			i[r] = i[r] || function() {
				(i[r].q = i[r].q || []).push(arguments)
			}, i[r].l = 1 * new Date();
			a = s.createElement(o),
				m = s.getElementsByTagName(o)[0];
			a.async = 1;
			a.src = g;
			m.parentNode.insertBefore(a, m)
		})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

		ga('create', 'UA-73809851-1', 'auto');
		ga('send', 'pageview');
	</script>
</body>


<!-- Bagian Belakang -->
<?php if ($student_competency->num_rows > 0) { ?>
	<br>

	<body class="mt-2">
		<div id="editor" class="edit-mode-wrap">
			<style type="text/css">
				.is_logo {
					display: none;
				}
			</style>
			<style type="text/css">
				* {
					margin: 0;
					padding: 0;
				}

				body {
					background: #fff;
					font-family: Arial, Helvetica, sans-serif;
					font-size: 12px;
					line-height: 20px;

				}

				#extra {
					text-align: right;
					font-size: 22px;
					font-weight: 700
				}

				.invoice-wrap {
					border-style: solid;

					width: 1200px;
					margin: 0 auto;
					background: #FFF;
					color: #000
				}

				.invoice-inner {
					border: 10px solid transparent;
					padding: 15px;
					border-image: url('./assets/border.png') 30 round;


					/* margin: 0 15px; */
					/* padding: 20px 0 */
				}

				.invoice-address {
					border-top: 3px double #000000;
					margin: 25px 0;
					padding-top: 25px;
				}

				.bussines-name {
					font-size: 18px;
					font-weight: 100
				}

				.invoice-name {
					font-size: 22px;
					font-weight: 700
				}

				.listing-table th {
					background-color: #e5e5e5;
					border-bottom: 1px solid #555555;
					border-top: 1px solid #555555;
					font-weight: bold;
					text-align: left;
					padding: 6px 4px
				}

				.listing-table td {
					border-bottom: 1px solid #555555;
					text-align: left;
					padding: 5px 6px;
					vertical-align: top
				}

				.total-table td {
					border-left: 1px solid #555555;
				}

				.total-row {
					background-color: #e5e5e5;
					border-bottom: 1px solid #555555;
					border-top: 1px solid #555555;
					font-weight: bold;
				}

				.row-items {
					margin: 5px 0;
					display: block
				}

				.notes-block {
					margin: 50px 0 0 0
				}

				/*tables*/


				.items-table {
					border: 1px solid #555555;
					border-collapse: collapse;
					width: 100%
				}

				.items-table td,
				.items-table th {
					border: 1px solid #555555;
					padding: 4px 5px;
					text-align: left
				}

				.items-table th {
					background: #f5f5f5;
				}

				.totals-row .wide-cell {
					border: 1px solid #fff;
					border-right: 1px solid #555555;
					border-top: 1px solid #555555
				}


				.table {
					width: 100%;
					margin-bottom: 1rem;
					background-color: transparent
				}

				.table td,
				.table th {
					padding: .75rem;
					vertical-align: top;
					border-top: 1px solid #dee2e6
				}

				.table thead th {
					vertical-align: bottom;
					border-bottom: 2px solid #dee2e6
				}

				.table tbody+tbody {
					border-top: 2px solid #dee2e6
				}

				.table .table {
					background-color: #fff
				}

				.table-sm td,
				.table-sm th {
					padding: .3rem
				}

				.table-bordered {
					border: 1px solid #dee2e6
				}

				.table-bordered td,
				.table-bordered th {
					border: 1px solid #dee2e6
				}

				.table-bordered thead td,
				.table-bordered thead th {
					border-bottom-width: 2px
				}
			</style>




			<!-- Cek Penilaian -->
			<div class="invoice-wrap">
				<div class="invoice-inner">



					<table border="0" cellpadding="0" cellspacing="10" width="100%">
						<tbody>
							<tr>
								<td align="center" valign="center">
									<h2 style="font-size: 20pt;">Daftar Unit Kompetensi</h2>
								</td>
							</tr>

							<tr>
								<td align="center" valign="center">
									<p style="font-size: 18pt;">List of unit(s) of Competency</p>
								</td>
							</tr>

							<tr></tr>
							<tr></tr>

						</tbody>
					</table>


					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tbody>
							<tr>
								<td align="left" valign="center">
									<p style="font-size: 14pt;">Unit Kompetensi : COMPETENCE OF LOGISTICS AND INTERNATIONAL TRADE</p>
								</td>
							</tr>

						</tbody>
					</table>


					<table class="table table-bordered" style="border-collapse: collapse" width="100%">
						<thead>
							<tr style="font-size: 12pt;">
								<th style="height:40px;width:50px;">
									No.
								</th>

								<th style="font-size: 15pt;">
									Job Profile
								</th>

								<th style="font-size: 15pt;">
									Duration
								</th>

								<th style="height:40px;width:90px;font-size: 15pt;">
									Score
								</th>
							</tr>
						</thead>
						<tbody>

							<?php

							foreach ($profile_jobdesc as $i => $data) :

								$hitung_nilai = 'A';

							?>



								<tr style="font-size: 12pt;font-weight:light;">
									<!-- <td align="center" valign="center"> -->
									<th align="center">
										<?= $i + 1 ?>.
									</th>

									<th align="center" valign="center">

										<?= $data['nama_profile'] ?>
									</th>

									<th align="center" valign="center">
										<?= $data['nama_profile'] ?>
									</th>

									<th align="center" valign="center">
										<?= $hitung_nilai ?>
									</th>

								</tr>

							<?php endforeach; ?>

						</tbody>


						<br>
						<br>

					</table>










				</div>
			</div>



		</div>
		<style>
			body {
				background: #EBEBEB;
			}

			.invoice-wrap {
				box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
				/* margin-bottom: 20px; */
			}

			#mobile-preview-close a {
				position: fixed;
				left: 20px;
				/* bottom: 30px; */
				background-color: #27c24c;
				font-weight: 600;
				outline: 0 !important;
				line-height: 1.5;
				border-radius: 3px;
				font-size: 14px;
				padding: 7px 10px;
				border: 1px solid #27c24c;
				text-decoration: none;
			}

			#mobile-preview-close img {
				width: 20px;
				height: auto;
			}

			#mobile-preview-close a:nth-child(2) {
				left: 190px;
				background: #f5f5f5;
				border: 1px solid #9f9f9f;
				color: #555 !important;
			}

			#mobile-preview-close a:nth-child(2) img {
				height: auto;
				position: relative;
				top: 2px;
			}

			.invoice-wrap {
				padding: 20px;
			}


			@media print {
				.page-break {
					height: 0;
					page-break-before: always;
					margin: 0;
					border-top: no;
				}

				#mobile-preview-close a {
					display: none;
				}

				/* .invoice-wrap {
				0
			} */

				body {
					background: none;
					height: 80mm;
					width: 180mm;
				}

				.invoice-wrap {
					box-shadow: none;
					margin-bottom: 0px;
				}

			}
		</style>

		<div id="mobile-preview-close">
			<a style="color: #fff !important;" onclick="window.location = document.referrer"> <i class="fas fa-arrow-left fa-1x"></i> Kembali</a>
			<a href="javascript:window.print();"> <i class="fas fa-print fa-1x"></i> Cetak </a>

		</div>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>


		<script type="text/javascript">
			document.ready = window.print();
			var beforePrint = function() {};


			function onClick() {
				var pdf = new jsPDF('p', 'pt', 'letter');
				pdf.canvas.height = 72 * 11;
				pdf.canvas.width = 72 * 8.5;

				pdf.fromHTML(document.body);

				pdf.save('test.pdf');
			};

			var element = document.getElementById("clickbind");
			element.addEventListener("click", onClick);

			//var afterPrint = function() {
			//	document.location.href='/invoices/thankyou/0';
			//};

			if (window.matchMedia) {
				var mediaQueryList = window.matchMedia('print');
				mediaQueryList.addListener(function(mql) {
					if (mql.matches) {
						beforePrint();
					} else {
						afterPrint();
					}
				});
			}
			window.onbeforeprint = beforePrint;
			window.onafterprint = afterPrint;

			(function(i, s, o, g, r, a, m) {
				i['GoogleAnalyticsObject'] = r;
				i[r] = i[r] || function() {
					(i[r].q = i[r].q || []).push(arguments)
				}, i[r].l = 1 * new Date();
				a = s.createElement(o),
					m = s.getElementsByTagName(o)[0];
				a.async = 1;
				a.src = g;
				m.parentNode.insertBefore(a, m)
			})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

			ga('create', 'UA-73809851-1', 'auto');
			ga('send', 'pageview');
		</script>
	</body>
<?php } ?>


</html>