@extends('templates.lecturer')

@push('head-scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
<style>
    .page-inner { background: #f4f7f6; min-height: 100vh; padding: 25px !important; }
    .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .select2-container--bootstrap4 .select2-selection { border-radius: 8px; border: 1px solid #ebedf2; min-height: 45px; display: flex; align-items: center; }
    .mode-badge { font-size: 10px; padding: 4px 10px; border-radius: 20px; font-weight: 800; text-transform: uppercase; }
    .avatar-xl { width: 110px; height: 110px; border: 5px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .form-section-title { font-size: 12px; font-weight: 800; color: #1572e8; letter-spacing: 1px; border-bottom: 2px solid #f0f3f8; padding-bottom: 8px; margin-bottom: 20px; }
</style>
@endpush

@section('main-content')
<div class="container">
    <div class="page-inner">
        <div class="page-header mb-4">
            <h4 class="page-title fw-bold">Add Supervised Student</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="#"><i class="flaticon-home text-primary"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('lecturer/my/student.index') }}">My Supervised Students</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Add New</b></li>
            </ul>
        </div>

        <div class="row">
            {{-- Step 1: Selection Card --}}
            <div class="col-md-12 mb-4">
                <div class="card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="fw-bold mb-0">Search Student in Database <span class="text-danger">*</span></label>
                        <button type="button" id="btn-manual-mode" class="btn btn-outline-primary btn-sm fw-bold">
                            <i class="fas fa-plus-circle mr-1"></i> Student not listed? Add Manually
                        </button>
                    </div>
                    
                    <div id="selection-wrapper">
                        <select id="supervised-student" class="form-control select2-basic">
                            <option value="">-- Start typing Student Name or NIM --</option>
                            @foreach ($groupedStudents as $programStudy => $students)
                                <optgroup label="{{ $programStudy }}">
                                    @foreach ($students as $student)
                                        <option value="{{ $student['nim'] }}">{{ $student['nim'] }} | {{ $student['name'] }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    
                    <div id="manual-mode-alert" class="alert alert-info mt-3 mb-0 d-none">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-info-circle mr-2"></i> <b>Manual Mode:</b> Fill only the 3 main data fields.</span>
                            <button type="button" id="btn-back-to-search" class="btn btn-sm btn-info fw-bold">Back to Search</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 2: Form Detail --}}
            <div class="col-md-12">
                <form action="{{ router('lecturer/my/student.store') }}" method="POST" id="main-form">
                    {!! csrf(); !!}
                    <input type="hidden" name="mode" id="form-mode" value="auto">
                    <input type="hidden" name="nik_dosen" value="{{ Session::get('nim_nik_unit') }}">
                    
                    <div id="student-details-wrapper" class="d-none">
                        <div class="row">
                            {{-- Left Column: Form Inputs (3 Main Data) --}}
                            <div class="col-lg-8">
                                <div class="card h-100 p-4">
                                    <h6 class="form-section-title">MAIN STUDENT DATA</h6>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="fw-bold">NIM (Student ID)</label>
                                            <input type="text" name="nim" id="in-nim" class="form-control" required readonly>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="fw-bold">Full Name</label>
                                            <input type="text" name="name" id="in-name" class="form-control" required readonly>
                                        </div>
                                        
                                        <div class="form-group col-md-12">
                                            <label class="fw-bold">Study Program</label>
                                            {{-- Auto Mode: Tampilan Text --}}
                                            <div id="prodi-auto-wrapper">
                                                <input type="text" id="in-prodi-text" class="form-control" readonly>
                                            </div>

                                            {{-- Manual Mode: Dropdown dengan Optgroup --}}
                                            <div id="prodi-manual-wrapper" class="d-none">
                                                <select name="program_study" id="in-prodi-select" class="form-control select2-basic">
                                                    <option value="">-- Choose Program Study --</option>
                                                    @foreach($groupedProdis as $jurusan => $listProdi)
                                                        <optgroup label="JURUSAN {{ strtoupper($jurusan) }}">
                                                            @foreach($listProdi as $p)
                                                                <option value="{{ $p['kode_prodi'] }}">
                                                                    {{ $p['jenjang'] }} {{ $p['study_program'] }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Konfirmasi & Submit --}}
                                    <div class="mt-4 pt-3 border-top">
                                        <div class="custom-control custom-checkbox mb-3">
                                            <input type="checkbox" class="custom-control-input" id="confirm" name="confirm" required>
                                            <label class="custom-control-label small text-muted" for="confirm">
                                                I confirm that I will supervise this student according to the rules.
                                            </label>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold shadow-sm">
                                            <i class="fas fa-save mr-2"></i> ASSIGN STUDENT
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Column: Preview --}}
                            <div class="col-lg-4">
                                <div class="card p-4 text-center">
                                    <div class="mb-3">
                                        <span id="badge-mode" class="mode-badge badge-primary">Database Student</span>
                                    </div>
                                    <div class="avatar avatar-xl mx-auto mb-3">
                                        <img id="preview-photo" src="{{ router('viewer.student.avatar', ['filename' => base64_encode('default.jpg')]) }}" alt="..." class="avatar-img rounded-circle">
                                    </div>
                                    <h4 id="preview-name" class="fw-bold text-dark mb-1">-</h4>
                                    <p id="preview-nim" class="text-muted small mb-0">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        const select2 = $('#supervised-student').select2({ theme: 'bootstrap4' });
        const select2Prodi = $('#in-prodi-select').select2({ theme: 'bootstrap4' }); // Select2 untuk prodi manual
        
        const formMode = $('#form-mode');
        const details = $('#student-details-wrapper');
        const prodiAuto = $('#prodi-auto-wrapper');
        const prodiManual = $('#prodi-manual-wrapper');

        // --- Logic 1: Search Change ---
        select2.on('change', function() {
            const nim = $(this).val();

            if (!nim) {
                details.addClass('d-none');
                return;
            }

            // Tampilkan Loader yang Lebih Visual
            Swal.fire({
                title: 'Loading...',
                text: 'Retrieving student data',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading(); // Ini akan memunculkan spinner bawaan Swal yang sangat bersih
                }
            });

            $.ajax({
                url: "{{ router('lecturer/my/student.details') }}",
                type: 'POST',
                data: {
                    studentNIM: nim,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    Swal.close(); // Tutup loader saat sukses

                    try {
                        const json = (typeof res === 'object') ? res : JSON.parse(res);
                        if (json.error) return Swal.fire('Error', json.error, 'error');

                        fillForm(json.data);
                        details.removeClass('d-none');
                        
                    } catch (e) {
                        Swal.fire('Error', 'Invalid data format from server', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    Swal.fire('Error', 'Failed to connect to server (' + status + ')', 'error');
                }
            });
        });

        // --- Logic 2: Toggle Manual Mode ---
        $('#btn-manual-mode').on('click', function() {
            formMode.val('manual');
            $('#selection-wrapper').addClass('d-none');
            $('#manual-mode-alert').removeClass('d-none');
            $(this).addClass('d-none');

            // UI Changes
            resetForm();
            $('#in-nim, #in-name').prop('readonly', false);
            prodiAuto.addClass('d-none');
            prodiManual.removeClass('d-none');
            details.removeClass('d-none');
            $('#badge-mode').text('Manual Entry').removeClass('badge-primary').addClass('badge-warning');
        });

        $('#btn-back-to-search').on('click', function() {
            formMode.val('auto');
            $('#selection-wrapper').removeClass('d-none');
            $('#manual-mode-alert').addClass('d-none');
            $('#btn-manual-mode').removeClass('d-none');
            
            resetForm();
            $('#in-nim, #in-name').prop('readonly', true);
            prodiAuto.removeClass('d-none');
            prodiManual.addClass('d-none');
            details.addClass('d-none');
            select2.val(null).trigger('change');
            $('#badge-mode').text('Database Student').removeClass('badge-warning').addClass('badge-primary');
        });

        function fillForm(data) {
            $('#in-nim').val(data.nim);
            $('#in-name').val(data.name);
            $('#in-prodi-text').val(data.jenjang + ' ' + data.study_program);
            
            $('#preview-name').text(data.name);
            $('#preview-nim').text(data.nim);
            if (data.avatar_url) $('#preview-photo').attr('src', data.avatar_url);
        }

        function resetForm() {
            $('#main-form')[0].reset();
            
            // Reset Select2 Prodi Manual
            $('#in-prodi-select').val(null).trigger('change');
            
            $('#preview-name').text('-');
            $('#preview-nim').text('-');
            $('#preview-photo').attr('src', "{{ router('viewer.student.avatar', ['filename' => base64_encode('default.jpg')]) }}");
        }
    });
</script>
@endpush