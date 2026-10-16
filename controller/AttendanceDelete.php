<?php
header('Content-Type: application/json');


try {
  // Mulai transaksi
  $pdo = $_SERVER['PDO'];
  $pdo->beginTransaction();

  // Dekripsi ID
  $id = decrypt($_POST['id_attendance']);

  if ($id === false) {
    throw new Exception('(01) Failed to remove Attendance, please try again later');
  }

  // Cek apakah ada data di tabel attendance
  $sql = "SELECT * FROM attendance WHERE id_attendance = :id LIMIT 1";
  $statement = $pdo->prepare($sql);
  $statement->bindParam(':id', $id);
  $statement->execute();

  if ($statement->rowCount() != 1) {
    throw new Exception('(02) Failed to remove Attendance, please try again later');
  }

  // Cek apakah ada signature di tabel attendance-signature
  $sql_signature = "SELECT `signature` FROM `attendance-signature` WHERE id_attendance = :id";
  $statement_signature = $pdo->prepare($sql_signature);
  $statement_signature->bindParam(':id', $id);
  $statement_signature->execute();

  // Jika ada signature, hapus file nya
  if ($statement_signature->rowCount() > 0) {
    $result_signature = $statement_signature->fetch(PDO::FETCH_ASSOC);
    $signature_file = $result_signature['signature'];

    if (is_file($signature_file)) {
      if (!unlink($signature_file)) {
        throw new Exception('(03) Failed to remove Signature file');
      }
    }

    // Hapus record dari tabel attendance-signature
    $sql_delete_signature = "DELETE FROM `attendance-signature` WHERE id_attendance = :id";
    $statement_delete_signature = $pdo->prepare($sql_delete_signature);
    $statement_delete_signature->bindParam(':id', $id);
    $statement_delete_signature->execute();
  }

  // Hapus record dari tabel attendance
  $sql_delete_attendance = "DELETE FROM attendance WHERE id_attendance = :id";
  $statement_delete_attendance = $pdo->prepare($sql_delete_attendance);
  $statement_delete_attendance->bindParam(':id', $id);
  $statement_delete_attendance->execute();

  // Commit transaksi
  $pdo->commit();

  make_snackbar('(04) Attendance has been removed');
  redirect_back(1);
  exit;
} catch (Exception $e) {
  // Rollback transaksi jika terjadi kesalahan
  $pdo->rollBack();

  make_snackbar($e->getMessage());
  redirect_back(1);
  exit;
}
