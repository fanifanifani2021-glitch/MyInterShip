@extends('templates.student')

@section('main-content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Final Report Submission</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="index.php"><i class="flaticon-home text-primary"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student.internship') }}">My Internship</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student.internship.detail', ['id' => $id_enc ]) }}">Detail</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Final Report</b></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                {{-- Warning jika belum selesai magang --}}
                @if(strtotime($internship['end_date']) > time())
                <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle mr-3 fa-lg"></i>
                    <div>
                        <span class="fw-bold">Early Submission Advice</span><br>
                        <small>It is highly recommended to upload your final report <b>after</b> your internship period officially ends on {{ date('d M Y', strtotime($internship['end_date'])) }}.</small>
                    </div>
                </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold text-primary mb-0"><i class="fas fa-file-upload mr-2"></i>Submission Form</h6>
                    </div>
                    
                    <form action="{{ router('student.final_report.store', ['id' => $id_enc]) }}" method="POST" enctype="multipart/form-data">
                        <?= csrf(); ?>
                        <div class="card-body">
                            {{-- Status File Saat Ini --}}
                            <div class="form-group mb-4">
                                <label class="fw-bold text-uppercase small text-muted">Current Uploaded File</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light"><i class="fas fa-file-pdf text-danger"></i></span>
                                    </div>
                                    <input class="form-control bg-white" value="{{ $internship['final_report'] == '-' ? 'No files uploaded yet' : $internship['final_report'] }}" readonly />
                                    @if($internship['final_report'] !== '-')
                                    <div class="input-group-append">
                                        <a class="btn btn-outline-primary" href="index.php?viewer=final_report&id={{ $id_enc }}&file={{ base64_encode($internship['final_report']) }}" target="_blank">
                                            <i class="fas fa-eye mr-1"></i> View File
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Input Upload Baru --}}
                            <div class="form-group">
                                <label class="fw-bold">Upload New Report <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" name="final_report" class="form-control-file border p-2 w-100 rounded" required>
                                </div>
                                <div class="mt-2">
                                    <span class="badge badge-soft-danger px-0 font-italic">
                                        <i class="fas fa-info-circle mr-1"></i> Allowed: PDF format only | Maximum size: 5MB
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-light d-flex justify-content-between">
                            <button type="button" class="btn btn-link text-muted fw-bold" onclick="history.back()">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                <i class="fas fa-cloud-upload-alt mr-2"></i>SUBMIT REPORT
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection