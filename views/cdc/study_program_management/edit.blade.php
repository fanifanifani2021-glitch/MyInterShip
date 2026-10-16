@extends('templates.cdc')

@push('head-scripts')
@php
$pdo = $_SERVER['PDO'];
try {
    // Validasi input
    if (empty($params['id'])) {
        throw new Exception("Data not found");
    }

    // Decode base64 dan JSON
    $decoded = json_decode(base64_decode($params['id']), true);
    if (!is_array($decoded) || empty($decoded['kode_prodi']) || empty($decoded['id_kampus'])) {
        throw new Exception("Invalid data");
    }

    $kode_prodi = $decoded['kode_prodi'];
    $id_kampus = $decoded['id_kampus'];

    // Query untuk ambil data
    $stmt = $_SERVER['PDO']->prepare("SELECT * FROM program_study WHERE kode_prodi = :kode_prodi AND id_kampus = :id_kampus LIMIT 1");
    $stmt->execute([
        ':kode_prodi' => $kode_prodi,
        ':id_kampus' => $id_kampus
    ]);

    if ($stmt->rowCount() === 0) {
        make_swal2('error', 'Not Found', 'Study Program to edit is not found!');
        redirect_back(1);
        exit();
    }

    // Data ditemukan, ambil satu baris
    $prodi = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    make_swal2('error', 'Error', $e->getMessage());
    redirect_back(1);
    exit();
}

// Ambil daftar jurusan (major)
$sql = "SELECT * FROM tb_kampus ORDER BY id_kampus";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$daftar_kampus = $stmt->fetchAll(PDO::FETCH_ASSOC);
@endphp

@endpush

@section('main-content')


<div class="container">

    <div class="page-inner">


        <div class="page-header">
            <h4 class="page-title">Edit Study Program</h4>
            <ul class="breadcrumbs">
                <li class="nav-home">
                    <a href="{{ base_url()}}">
                        <i class="flaticon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="{{ base_url('cdc/study_program/management') }}">Study Program Management</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Edit Study Program</a>
                </li>
            </ul>
        </div>


        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Study Program Form</h4>
                    </div>


                    <form action="index.php?form=study_program&aksi=edit" method="POST" id="form_edit">
                        <?= csrf(); ?> <?= input_hidden('old_id_kampus', $prodi['id_kampus']) ?>
                        <?= input_hidden('kode_prodi_lama', $prodi['kode_prodi']) ?>
                        <div class="card-body form-row">

                            <div class="form-group col-md-12">
                                <label>University <b class="text-danger">*</b></label>
                                <select id="id_kampus" name="id_kampus" class="form-control">
                                    @foreach ($daftar_kampus as $data)
                                    <option value="{{ $data['id_kampus'] }}" @if( old('id_kampus', $prodi['id_kampus'])==$data['id_kampus']) {{ 'selected' }} @endif>{{ htmlspecialchars($data['nama_kampus']) }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group col-md-6">
                                <label for="kode_prodi">Study Program Code <label class="text-danger">*</label></label>
                                <input type="text" placeholder="Example : AB" class="form-control <?= show_error('kode_prodi') ?>" 
                                name="kode_prodi" value="<?= old('kode_prodi', $prodi['kode_prodi'] ?? '') ?>">
                                <?= form_error('kode_prodi'); ?>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="type">Level (Jenjang) <label class="text-danger">*</label></label>
                                <select class="form-control <?= show_error('jenjang') ?>" name="jenjang">
                                    <option value="" hidden>- Choose Stage -</option>
                                    <?php
                                    $jenjang_options = ['D2', 'D3', 'D4', 'S1', 'S2', 'S3', 'Profesi'];
                                    $selected_jenjang = old('jenjang', $prodi['jenjang'] ?? '');
                                    foreach ($jenjang_options as $jenjang) {
                                        $selected = $jenjang === $selected_jenjang ? 'selected' : '';
                                        echo "<option value=\"$jenjang\" $selected>$jenjang</option>";
                                    }
                                    ?>
                                </select>
                                <?= form_error('jenjang'); ?>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="prodi_en">Study Program (English) <label class="text-danger">*</label></label>
                                <input type="text" placeholder="Example : Informatics Engineering" class="form-control <?= show_error('prodi_en') ?>" 
                                name="prodi_en" id="prodi_en" value="<?= old('prodi_en', $prodi['study_program'] ?? '') ?>">
                                <?= form_error('prodi_en'); ?>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="prodi_id">Study Program (Bahasa Indonesia) <label class="text-danger">*</label></label>
                                <input type="text" placeholder="Example : Teknik Informatika" class="form-control <?= show_error('prodi_id') ?>" 
                                name="prodi_id" id="prodi_id" value="<?= old('prodi_id', $prodi['prodi'] ?? '') ?>">
                                <?= form_error('prodi_id'); ?>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="major_en">Major (English) <label class="text-danger">*</label></label>
                                <input type="text" placeholder="Example : Informatics Engineering" class="form-control <?= show_error('major_en') ?>" 
                                name="major_en" id="major_en" value="<?= old('major_en', $prodi['major'] ?? '') ?>">
                                <?= form_error('major_en'); ?>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="major_id">Major (Bahasa Indonesia) <label class="text-danger">*</label></label>
                                <input type="text" placeholder="Example : Teknik Informatika" class="form-control <?= show_error('major_id') ?>" 
                                name="major_id" id="major_id" value="<?= old('major_id', $prodi['jurusan'] ?? '') ?>">
                                <?= form_error('major_id'); ?>
                            </div>
                            





                        </div>
                        <div class="modal-footer justify-content-start">
                            <button onclick="cancel_confirmation('{{ base_url('cdc/study_program/management') }}')" type="button" class="btn btn-danger"><i class="fas fa-times mr-1"></i>Cancel</button>
                            <button onclick="reset_confirmation('form_edit')" type="button" class="btn btn-warning"><i class="fas fa-sync mr-1"></i>Reset</button>
                            <button type="submit" class="btn btn-success"><i class="fas fa-edit mr-1"></i>Edit</button>
                        </div>
                    </form>



                </div>
            </div>


        </div>


    </div>
</div>


@endsection

@push('footer-scripts')

<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>


<!-- Soloalert -->
<script src='./core/component/soloalert.js'></script>


<!-- User Custom Script -->
<script>
    // Untuk Inisiasi Konfirmasi Cancel
    function cancel_confirmation(location) {
        // alert('Are you sure you want to cancel ? (Your change will not be saved)');
        SoloAlert.confirm({
            title: "Are you sure you want to cancel ?"
            , body: "Changes you made will not be saved !"
            , useTransparency: true
            , onOk: () => {
                window.location.href = location;
            }
        });

    }


    // Untuk Inisiasi Konfirmasi Reset
    function reset_confirmation(form) {

        SoloAlert.confirm({
            title: "Are you sure you want to reset ?"
            , body: "Changes you made will be reset to default !"
            , useTransparency: true
            , onOk: () => {
                $("#" + form).trigger("reset");
            }
        });

    }

</script>

@endpush
