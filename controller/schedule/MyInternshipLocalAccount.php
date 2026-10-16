<?php

$sql = "SELECT nim FROM student_internship WHERE password IS NULL LIMIT 100";
$stmt = $_SERVER['PDO']->query($sql);
$updatedCount = 0;

if ($stmt) {
    $dataAll = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($dataAll) {
        $sqlUpdate = "UPDATE student_internship 
                      SET username = :username, password = :password, local_account = :local_account
                      WHERE nim = :nim";
        $stmt_update = $_SERVER['PDO']->prepare($sqlUpdate);

        foreach ($dataAll as $data) {
            $username = $data['nim'];
            $password = password_hash($data['nim'], PASSWORD_DEFAULT);
            $local_account = 1;

            $stmt_update->execute([
                ':username' => $username,
                ':password' => $password,
                ':local_account' => $local_account,
                ':nim' => $data['nim']
            ]);

            // hitung affected row per execute
            $updatedCount += $stmt_update->rowCount();
        }
    }
}

echo "MyInternship Local Account running successfully!\n";
log_message("✅ Affected data : {$updatedCount}");
log_message("✅ MyInternship Local Account running successfully. \n");
