<?php
header('Access-Control-Allow-Origin: *');

error_reporting(false);
session_start();
include 'ajax-init.php';


$nim = $_POST['nim'] ?? $_GET['nim'];

$sql = $_SERVER['PDO']->prepare("SELECT * FROM student_internship WHERE nim = :nim");
$sql->execute([':nim' => $nim]);

if ($sql->rowCount() != 1) {
    http_response_code(404);
    exit();
}

$student = $sql->fetch();




$sql2 = $_SERVER['PDO']->prepare("SELECT * FROM lecturer WHERE id_kampus = '" .$_SESSION['Lecturer-Data']['id_kampus']. "'");
$sql2->execute();

?>
<input type="hidden" name="student_nim" value="<?= $student['nim'] ?>" />

<div class="modal fade" id="ModalAssign" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Assign Supervisor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>



            <div class="card-body form-row">

                                        
                <?php if (in_array($student['nik_dospem'], [NULL, '', '-'])) : ?>
                    <div class="form-group text-center">
                        <div class="btn btn-warning btn-block">Current Student does not have Supervisor yet !</div>
                    </div>
                <?php endif; ?>


                <div class="form-group col-md-12">
                    <label>Lecturer</label>

                    <select class="col-12 select2-input form-control " name="lecturer_nik" id="lecturer_nik" required>
                        <option value="" hidden>Choose one . . .</option>

                        <?php foreach ($sql2->fetchAll() as $row) : ?>
                            <option value="<?= $row['nim_nik_unit'] ?>"><?= $row['nim_nik_unit'] ?> - <?= $row['name'] ?> </option>
                        <?php endforeach;?>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Close</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-user-secret mr-2"></i>Assign Supervisor</button>
            </div>
        </div>
    </div>
</div>