@extends('templates.student')

@push('head-scripts')
<style>
    .page-inner { background: #f8f9fa; min-height: 100vh; padding: 30px 20px !important; }

    .card { border-radius: 12px !important; border: none; }
    .form-label { font-weight: 700; color: #2d3748; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.03em; }
    .signature-pad { border: 2px dashed #cbd5e0; border-radius: 8px; background: #fff; cursor: crosshair; }
    
    @media (max-width: 768px) {
        .page-header { display: block !important; }
        .breadcrumbs { padding-left: 0; margin-top: 10px; overflow-x: auto; white-space: nowrap; }
    }
</style>
@endpush

@section('main-content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Add Attendance History</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="index.php"><i class="flaticon-home text-primary"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student.internship') }}">My Internship</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student.internship.detail', ['id' => $id_enc ]) }}">Detail</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student.attendance.index', ['id' => $id_enc]) }}">Attendance</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Add History</b></li>
            </ul>
        </div>

        <form action="{{ router('student.attendance.addHistory-store', ['id' => $id_enc]) }}" id="form_history" method="POST">
            <?= csrf(); ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="fw-bold text-primary mb-0"><i class="fas fa-edit mr-2"></i>Activity Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                

                                {{-- Bagian Input Tanggal di dalam Form --}}
                                <div class="form-group col-md-6">
                                    <label class="form-label">Attendance Date <span class="text-danger">*</span></label>
                                    <input type="date" name="attendance_date" id="attendance_date" 
                                        class="form-control" required 
                                        min="{{ $min_date }}" 
                                        max="{{ $max_date }}"
                                        onchange="validateSelectedDate(this.value)">
                                    
                                    <div class="mt-2">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-info-circle mr-1"></i> 
                                            Manual history is only available for past dates: 
                                            <b>{{ date('d M Y', strtotime($min_date)) }}</b> to <b>{{ date('d M Y', strtotime($max_date)) }}</b>
                                        </small>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" name="attendance_type" id="attendance_type" onchange="toggleTimeFields()" required>
                                        <option value="" hidden>-- Choose Status --</option>
                                        <option value="Present">Present (Working)</option>
                                        <option value="Sick Leave">Sick Leave</option>
                                        <option value="Permit">Permit / Personal Leave</option>
                                        <option value="National Holiday">National Holiday</option>
                                    </select>
                                </div>

                                {{-- Time Fields (Hidden by default) --}}
                                <div class="form-group col-md-6 time-fields d-none">
                                    <label class="form-label">Check In <span class="text-danger">*</span></label>
                                    <input type="time" name="check_in" id="check_in" class="form-control">
                                </div>
                                <div class="form-group col-md-6 time-fields d-none">
                                    <label class="form-label">Check Out <span class="text-danger">*</span></label>
                                    <input type="time" name="check_out" id="check_out" class="form-control">
                                </div>

                                <div class="form-group col-md-12 mt-3">
                                    <label class="form-label">Work Description <span class="text-danger">*</span></label>
                                    <textarea name="description" id="description" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="fw-bold text-secondary mb-0"><i class="fas fa-signature mr-2"></i>Validation</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="js-signature signature-pad" id="signature" data-width="600" data-height="250" data-line-color="#000" data-auto-fit="true"></div>
                            <p class="text-muted small mt-2">Sign inside the box to enable saving.</p>
                            <button type="button" class="btn btn-outline-danger btn-sm mt-2" onclick="clearSignature()">
                                <i class="fas fa-eraser mr-1"></i> Clear Signature
                            </button>
                        </div>
                    </div>

                    <button type="submit" id="submit_btn" class="btn btn-primary btn-block fw-bold py-3 shadow" disabled>
                        <i class="fas fa-save mr-2"></i>SAVE HISTORY
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('footer-scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script src="./assets/js/jq-signature.js"></script>
<script>
    let editor;
    const ALREADY_LOGGED = <?= json_encode($disabled_dates) ?>;

    $(document).ready(function() {
        // 1. Inisialisasi CKEditor 5
        ClassicEditor.create(document.querySelector('#description'), {
            toolbar: ['bold', 'italic', 'bulletedList', 'numberedList']
        }).then(newEditor => { editor = newEditor; });

        // 2. Inisialisasi Signature Pad
        const signatureCanvas = $('.js-signature').jqSignature();
        signatureCanvas.on('jq.signature.changed', () => { 
            $('#submit_btn').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary'); 
        });

        // 3. Handling Form Submit
        $('#form_history').on('submit', function(e) {
            e.preventDefault();
            
            // Ambil data tanda tangan
            const signatureData = signatureCanvas.jqSignature('getDataURL');
            $('<input>').attr({type: 'hidden', name: 'validation', value: signatureData}).appendTo(this);
            
            // Ambil data laporan harian
            $('#description').val(editor.getData());
            
            // Loading state
            $('#submit_btn').html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...').prop('disabled', true);
            
            this.submit();
        });
    });

    // Validasi Tanggal (Cek Duplikasi & Range)
    function validateSelectedDate(dateValue) {
        if (!dateValue) return;

        if (ALREADY_LOGGED.includes(dateValue)) {
            Swal.fire('Duplicate Date', 'You already have an attendance record for this date.', 'warning');
            $('#attendance_date').val('');
            return;
        }

        const selected = new Date(dateValue);
        const minDate = new Date("{{ $min_date }}");
        const maxDate = new Date("{{ $max_date }}");

        if (selected < minDate || selected > maxDate) {
            Swal.fire('Invalid Date', 'Date must be within your internship period and cannot be today.', 'error');
            $('#attendance_date').val('');
        }
    }

    function toggleTimeFields() {
        const type = $('#attendance_type').val();
        if (type === 'Present') {
            $('.time-fields').removeClass('d-none');
            $('#check_in, #check_out').attr('required', true);
        } else {
            $('.time-fields').addClass('d-none');
            $('#check_in, #check_out').removeAttr('required').val('');
        }
    }

    function clearSignature() {
        $('.js-signature').jqSignature('clearCanvas');
        $('#submit_btn').prop('disabled', true).addClass('btn-secondary');
    }
</script>
@endpush