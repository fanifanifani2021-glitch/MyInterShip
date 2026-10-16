@extends('templates.student')

@push('head-scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">

<style>
    /* Square-Rounded Button Style */
    .btn-modern { border-radius: 6px !important; padding: 8px 20px; font-weight: 600; transition: 0.3s; }
    
    /* Caution Banner Styling */
    .caution-banner {
        background-color: #fff9db; /* Kuning Lembut */
        border-left: 5px solid #fcc419; /* Garis Penegas */
        padding: 15px 20px;
        border-radius: 6px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .caution-icon { font-size: 1.5rem; color: #f08c00; }
    .caution-text h6 { margin: 0 0 3px 0; font-weight: 800; color: #856404; }
    .caution-text p { margin: 0; font-size: 0.85rem; color: #856404; line-height: 1.4; }

    /* CKEditor & Form Fixes */
    .ck-editor__editable { min-height: 200px; border-radius: 0 0 6px 6px !important; }
    .form-control[readonly] { background-color: #f8f9fa !important; }
</style>
@endpush

@section('main-content')
<div class="container">
    <div class="page-inner">
        {{-- Breadcrumbs --}}
        <div class="page-header">
            <h4 class="page-title">Edit Logbook</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="index.php"><i class="flaticon-home text-primary"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student.internship') }}">My Internship</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student.internship.detail', ['id' => $id_enc]) }}">Detail</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student.logbook.index', ['id' => $id_enc]) }}">Logbook</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Edit</b></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><div class="card-title">Weekly Report Revision</div></div>
                    
                    <form action="{{ router('student.logbook.update', ['id' => $id_enc, 'logbook_id' => $logbook_id ]) }}" id="form_logbook" method="POST">
                        <?= csrf(); ?> @method('PUT')
                        <?= input_hidden('id_logbook', $data['id_logbook']) ?>
                        <?= input_hidden('id_internship', $id_plain) ?>
                        <input type="hidden" name="old_week" value="{{ $data['week_num'] }}">

                        <div class="card-body">
                            {{-- 1. Caution Banner --}}
                            <div class="caution-banner shadow-sm">
                                <div class="caution-icon"><i class="fas fa-exclamation-triangle"></i></div>
                                <div class="caution-text">
                                    <h6>Notice: Approval Reset</h6>
                                    <p>Saving changes to this report will <b>automatically reset</b> your existing Supervisor and Lecturer approvals back to <b>"Waiting"</b> status.</p>
                                </div>
                            </div>

                            <div class="form-row">
                                {{-- Week Selection --}}
                                <div class="form-group col-md-4">
                                    <label>Week Num <span class="text-danger">*</span></label>
                                    <select name="week_num" id="week_num_select" class="form-control select2">
                                        @foreach($weekSchedule as $sched)
                                            @php
                                                $isDisabled = ($sched['week'] < $data['week_num'] || ($sched['isFilled'] && !$sched['isCurrent'])) ? 'disabled' : '';
                                                $status = $sched['isCurrent'] ? "(Current)" : ($sched['isFilled'] ? "(Completed)" : "");
                                            @endphp
                                            <option value="{{ $sched['week'] }}" data-start="{{ $sched['start'] }}" data-end="{{ $sched['end'] }}" {{ $sched['isCurrent'] ? 'selected' : '' }} {{ $isDisabled }}>
                                                M{{ $sched['week'] }} {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $data['start_date'] }}">
                                </div>

                                <div class="form-group col-md-4">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $data['end_date'] }}">
                                </div>

                                {{-- Rich Text Editors --}}
                                <div class="form-group col-md-12">
                                    <label>Job Description <span class="text-danger">*</span></label>
                                    <div id="editor-description">{!! htmlspecialchars_decode($data['description']) !!}</div>
                                    <textarea name="description" id="hidden-description" style="display:none;"></textarea>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Documentation & Evidence <span class="text-danger">*</span></label>
                                    <div id="editor-documentation">{!! htmlspecialchars_decode($data['documentation']) !!}</div>
                                    <textarea name="documentation" id="hidden-documentation" style="display:none;"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="button" class="btn btn-danger" onclick="confirmation()"><i class="fas fa-times mr-1"></i>Cancel</button>
                            <button type="button" class="btn btn-warning" onclick="reset_confirmation()"><i class="fas fa-sync mr-1"></i>Reset</button>
                            <button type="submit" class="btn btn-success" id="submit_btn"><i class="fas fa-edit mr-1"></i>Edit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-scripts')
{{-- Scripts tetap sama dengan sinkronisasi CKEditor --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
    let descEditor, docEditor;

    ClassicEditor.create(document.querySelector('#editor-description'), {
        toolbar: ['bold', 'italic', 'bulletedList', 'numberedList', 'undo', 'redo'],
    }).then(editor => { descEditor = editor; });

    ClassicEditor.create(document.querySelector('#editor-documentation'), {
        toolbar: ['bold', 'italic', 'link', 'imageUpload', 'insertTable', 'undo', 'redo'],
    }).then(editor => { docEditor = editor; });

    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap4' });
        
        $('#form_logbook').on('submit', function() {
            $('#hidden-description').val(descEditor.getData());
            $('#hidden-documentation').val(docEditor.getData());
            $('#submit_btn').html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...').prop('disabled', true);
        });
    });

    function confirmation() {
        SoloAlert.confirm({
            title: "Cancel Edit?",
            useTransparency: true,
            body: "Changes you made will be lost.",
            onOk: () => { window.location.href = "{{ router('student.logbook.index', ['id' => $id_enc]) }}"; }
        });
    }

    function form_reset() {

        $("#form_logbook").trigger("reset");

        $("small").empty();

        $(".form-control").removeClass('is-invalid border-success border-danger is-valid');
        $('#cke_description').removeClass('<?= show_error('description')?>');
        $('#cke_documentation').removeClass('<?= show_error('documentation')?>');

    };
</script>
@endpush