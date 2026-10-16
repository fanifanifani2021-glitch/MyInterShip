<?php
$id_company = $_SESSION['id_company'];

// Cek Company
$sql = "SELECT * FROM company WHERE id_company = :id_company";
$company_data = $pdo->prepare($sql);

$company_data->execute( [':id_company' => $id_company] );

$company_data = $company_data->fetch( PDO::FETCH_ASSOC );

if (!in_array($company_data['status'], ['VERIFIED', 'Verified', 'verified', 1])) {
	make_swal2('error', 'Not Allowed', 'Your company not Verified yet !');
	redirect_referrer(1);
	exit();
}


if (!in_array($company_data['access_type'], [1])) {
	make_swal2('error', 'Not Allowed', 'Your company do not have access to this page !');
	redirect_referrer(1);
	exit();
}


renderView('industry.job_offer.index', ['title' => 'Job Offers', 'routes' => 'industry_offer', 'route' => 'industry_offer']);
