@php
$extends_template = (in_array($_SESSION['role'], ['UPKPK', 'CDC'])) ? 'templates.cdc' : 'templates.lecturer';
$routes = (in_array($_SESSION['role'], ['UPKPK', 'CDC'])) ? 'intern_student_list' : 'student_intern_management';
$redirectPage = (in_array($_SESSION['role'], ['UPKPK', 'CDC'])) ? 'intern_student_list' : 'student_intern_management';
@endphp
@extends( $extends_template , ['title' => 'Add New Student', 'routes' => $routes, 'route' => $routes ])

@push('head-scripts')
<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

@php
$id_kampus = (in_array($_SESSION['role'], ['UPKPK', 'CDC'])) ? $_SESSION['CDC-Data'] ['id_kampus'] : $_SESSION['Lecturer-Data'] ['id_kampus'];

// Menyiapkan koneksi PDO (pastikan PDO sudah diinisialisasi sebelumnya)
$pdo = $_SERVER['PDO'];

// Menyiapkan query dengan PDO prepared statement, dengan limit 1
$sql = "SELECT * FROM tb_kampus WHERE id_kampus = :id_kampus LIMIT 1";
$stmt = $pdo->prepare($sql);

// Mengikat parameter :id_kampus ke nilai $id_kampus
$stmt->bindParam(':id_kampus', $id_kampus, PDO::PARAM_STR);

// Eksekusi statement
$stmt->execute();

// Mendapatkan hasil sebagai array asosiatif
$exec = $stmt->fetch(PDO::FETCH_ASSOC);

// Mengambil nama kampus dari hasil query
$nama_kampus = $exec['nama_kampus'];

// Menyiapkan query untuk mendapatkan distinct major dari program_study berdasarkan id_kampus
$sql_major = "SELECT DISTINCT major FROM program_study WHERE id_kampus = :id_kampus";
$stmt_major = $pdo->prepare($sql_major);

// Mengikat parameter :id_kampus ke nilai $id_kampus (gunakan kembali variabel yang sudah ada)
$stmt_major->bindParam(':id_kampus', $id_kampus, PDO::PARAM_STR);

// Eksekusi statement untuk mendapatkan hasil
$stmt_major->execute();

// Mengumpulkan hasil major sebagai array
$majors = $stmt_major->fetchAll(PDO::FETCH_COLUMN);

// Ambil daftar jurusan (major)
$sql = "SELECT DISTINCT(major) FROM program_study WHERE id_kampus = :id_kampus ORDER BY major";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_kampus', $id_kampus, PDO::PARAM_STR);
$stmt->execute();

$daftar_major = [];

while ($data_jurusan = $stmt->fetch(PDO::FETCH_ASSOC)) {
$jurusan = $data_jurusan['major'];

// Ambil daftar program studi untuk setiap jurusan
$sql2 = "SELECT * FROM program_study WHERE major = :jurusan AND id_kampus = :id_kampus ORDER BY jenjang";
$stmt2 = $pdo->prepare($sql2);
$stmt2->bindParam(':jurusan', $jurusan, PDO::PARAM_STR);
$stmt2->bindParam(':id_kampus', $id_kampus, PDO::PARAM_STR);
$stmt2->execute();

$daftar_prodi = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Simpan ke array besar
$daftar_major[] = [
'major' => $jurusan,
'programs' => $daftar_prodi
];
}


$sql4 = "SELECT * FROM lecturer WHERE id_kampus = :id_kampus";
$stmt4 = $pdo->prepare($sql4);
$stmt4->execute(['id_kampus' => $id_kampus]);

$daftar_dosen = $stmt4->fetchAll(PDO::FETCH_ASSOC);

@endphp

@endpush

@section('main-content')
<div class="container">

    <div class="page-inner">


        <div class="page-header">
            <h4 class="page-title">Add New Student</h4>
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
                    <a href="index.php?page={{ $redirectPage }}">Student Management</a>
                </li>

                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Add Student</a>
                </li>
            </ul>
        </div>




        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add Student Form</h4>
                    </div>

                    <form action="index.php?form=dkm&aksi=add_student_intern" method="post" id="form_add"><?= csrf(); ?>
                        <div class="card-body form-row">

                            <div class="form-group col-md-6">
                                <label>Student NIM <b class="text-danger">*</b></label>
                                <input type="text" class="form-control <?= show_error('student_nim') ?>" name="student_nim" id="student_nim" value="<?= old('student_nim') ?>" onkeyup="search_student( this.value )" placeholder="Example : 33118010xx" />

                                <?= form_error('student_nim') ?>

                            </div>


                            <div class="form-group col-md-6">
                                <label>Student Name <b class="text-danger">*</b></label>
                                <input type="text" class="form-control <?= show_error('student_name') ?>" name="student_name" id="student_name" placeholder="Example : Fulan Ramadhan" />

                                <?= form_error('student_name') ?>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Student E-Mail <small class="text-sm">Opsional</small></label>
                                <input type="text" class="form-control <?= show_error('student_email') ?>" name="student_email" id="student_email" placeholder="Example : fulan@gmail.com" />

                                <?= form_error('student_email') ?>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Program Study <b class="text-danger">*</b></label>


                                <select id="kode_program_study" name="kode_program_study" class="form-control <?= show_error('kode_program_study') ?>">
                                    <option value="">- Select Study Program -</option>
                                    @foreach ($daftar_major as $group)
                                    <optgroup label="{{ htmlspecialchars($group['major']) }}" id="{{ 'group-' . htmlspecialchars($group['major']) }}">
                                        @foreach ($group['programs'] as $prodi)
                                        <option value="{{ htmlspecialchars($prodi['kode_prodi']) }}" {{ ( old('kode_program_study') == $prodi['kode_prodi'] ) ? 'selected' : ''; }}>
                                            {{ htmlspecialchars($prodi['jenjang']) }} {{ htmlspecialchars($prodi['prodi']) }} -
                                            ({{ htmlspecialchars($prodi['jenjang']) }} {{ htmlspecialchars($prodi['study_program']) }})
                                        </option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>



                                <?= form_error('kode_program_study') ?>
                            </div>


                            <div class="form-group col-md-12">
                                <label for="nik_dospem">Choose Lecturer</label>
                                <select id="nik_dospem" name="nik_dospem" class="form-control" required>
                                    <option value="" hidden>- Select Lecturer -</option>
                                    <option value="Unassigned">Unassigned</option>
                                    @foreach($daftar_dosen as $index => $value)
                                    <option value="{{ $value['nim_nik_unit'] }}">{{ $value['nim_nik_unit'] }} | {{ $value['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>


                            {{-- <div class="form-group col-md-12" hidden>
                                <label>Search Status <b class="text-danger">*</b></label>
                                <input type="text" class="form-control" name="search_status" id="search_status" value="Success" />

                                <?= form_error('search_status') ?>

                            </div> --}}



                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-danger" onclick="confirmation()"><i class="fas fa-times mr-1"></i>Cancel</button>
                            <button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i class="fas fa-sync mr-1"></i>Reset</button>
                            <button type="submit" class="btn btn-success"><i class="fas fa-plus mr-1"></i>Add</button>

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

<script>
    $(document).ready(function() {

        $('#nik_dospem').select2({
            theme: 'bootstrap4'
            , width: '100%'
        });
        
        $('#kode_program_study').select2({
            theme: 'bootstrap4'
            , width: '100%'
        });
    });


    function confirmation() {
        // alert('Are you sure you want to cancel ? (Your change will not be saved)');
        SoloAlert.confirm({
            title: "Are you sure you want to cancel ?"
            , body: "Changes you made will not be saved !"
            , useTransparency: true
            , onOk: () => {
                window.location.href = "index.php?page={{ $redirectPage }}";
            }
        });

    }

    function reset_confirmation() {

        SoloAlert.confirm({
            title: "Are you sure you want to reset ?"
            , body: "Changes you made will be reset to default !"
            , useTransparency: true
            , onOk: () => {
                form_reset();
            }
        });

    }


    function form_reset() {

        $("#form_add").trigger("reset");

        $("small").empty();
    };

</script>

@endpush
