<?php

$action = $_GET['action'];


if ($action == 'data') {


  $id_company = $_SESSION['id_company'];


  


  $dataTable = getDataTableData(
    "SELECT * FROM offer WHERE id_company = '$id_company'",
    function ($row) {

      if ($row['status'] == 'Open') {
        $status = "<button type='button' class='btn btn-success btn-sm'>Open</button>";
      } else {
        $status = "<button type='button' class='btn btn-danger btn-sm'>Closed</button>";
      }

      return array(
        'status' => $status
      );
    },
    function ($row) {
      $aksi = '<div class="btn-group btn-round">
        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Action
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
  
          <button onclick="window.location.href=\'index.php?page=offer_detail&id=' . base64_encode($row['id_offer']) . '\'" type="button" class="dropdown-item">
            <i class="fas fa-info-circle mr-2"></i>Offer Detail
          </button>';
  
      if ($_SESSION['user_type'] == 'HRD') {
        $aksi .= '
          <div class="dropdown-divider"></div>
          <button class="dropdown-item" data-toggle="modal" data-target="#ModalChange" onclick="modal_change(\'' . $row['id_offer'] . '\', \'' . $row['status'] . '\')"><i class="fas fa-sync mr-2"></i>Change Status</button>
          <button type="button" data-toggle="modal" data-target="#ModalHapus" onclick="modal_hapus(\'' . $row['id_offer'] . '\')" class="dropdown-item" data-original-title="Hapus"><i class="fa fa-trash mr-2"></i> Delete';
      }
  
      $aksi .= '</button>
        </div>
      </div>';
  
      return array(
        'aksi' => $aksi
      );
    }


  );


  response_json(200, $dataTable['response']);
} else {
  return view('exception/error_404');
}
