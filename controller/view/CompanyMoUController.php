<?php

$id_kampus = $_SESSION['CDC-Data']['id_kampus'];

$sql = "SELECT * FROM tb_kampus WHERE id_kampus = :id_kampus LIMIT 1";
$stmt = $_SERVER['PDO']->prepare($sql);
$stmt->bindParam(':id_kampus', $id_kampus, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() != 1) {
    make_swal2('error', 'Error', 'University data not found !');
    redirect_back(1);
    exit;
}

$univerity_data = $stmt->fetch(PDO::FETCH_ASSOC);

switch ($method):
    default:
        view('exception/error_404');

        break;



    case 'index':

        $sql = "SELECT company.name AS company_name, company_mou.* FROM company_mou 
        INNER JOIN company ON company_mou.id_company = company.id_company
        WHERE company_mou.id_kampus = :id_kampus ORDER BY id_mou DESC";

        $stmt = $_SERVER['PDO']->prepare($sql);
        $stmt->bindParam(':id_kampus', $id_kampus, PDO::PARAM_INT);
        $stmt->execute();

        $mou_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        view('upkpk/company_mou/company_mou_management', [
            'title' => 'Company MoU Management',
            'route' => 'company_mou_management',
            'data' => $mou_data, 'university_data' => $univerity_data
        ]);

        break;

    case 'detail':

        $id_mou = base64_decode($_GET['id_mou']);

        // Get Data
        $sql = "SELECT * FROM company_mou WHERE id_mou = :id_mou AND id_kampus = :id_kampus LIMIT 1";
        $stmt = $_SERVER['PDO']->prepare($sql);


        $stmt->bindParam('id_mou', $id_mou, PDO::PARAM_INT);
        $stmt->bindParam('id_kampus', $id_kampus, PDO::PARAM_INT);

        $stmt->execute();

        if ($stmt->rowCount() != 1) {
            make_snackbar('(1) MoU not found !');
            redirect_back(1);
            exit;
        }

        $data_mou = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql = "SELECT * FROM company WHERE id_company = :id_company LIMIT 1";
        $stmt = $_SERVER['PDO']->prepare($sql);
        $stmt->bindParam('id_company', $data_mou['id_company'], PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() != 1) {
            make_snackbar('(2) Company not found !');
            redirect_back(1);
            exit;
        }

        $data_company = $stmt->fetch( PDO::FETCH_ASSOC );



        view('upkpk/company_mou/detail_mou', [
            'title' => 'Detail MoU', 'route' => 'company_mou_management',
            'mou' => $data_mou,
            'company' => $data_company,
        ]);
        break;
    case 'add':

        $sql = "SELECT * FROM company";
        $stmt = $_SERVER['PDO']->query($sql);
        $stmt->execute();


        view('upkpk/company_mou/add_mou', [
            'title' => 'Add MoU', 'route' => 'company_mou_management',
            'company' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
        break;

    case 'edit':

        $id_mou = base64_decode($_GET['id_mou']);

        // Get Data
        $sql = "SELECT * FROM company_mou WHERE id_mou = :id_mou AND id_kampus = :id_kampus LIMIT 1";
        $stmt = $_SERVER['PDO']->prepare($sql);


        $stmt->bindParam('id_mou', $id_mou, PDO::PARAM_INT);
        $stmt->bindParam('id_kampus', $id_kampus, PDO::PARAM_INT);

        $stmt->execute();

        if ($stmt->rowCount() != 1) {
            make_snackbar('(1) MoU not found !');
            redirect_back(1);
            exit;
        }

        echo "hello";
        exit;

        $sql = "SELECT * FROM company";
        $stmt = $_SERVER['PDO']->query($sql);
        $stmt->execute();


        view('upkpk/company_mou/add_mou', [
            'title' => 'Edit MoU', 'route' => 'company_mou_management',
            'company' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'mou_data' => $mou_data
        ]);
        break;

endswitch;
