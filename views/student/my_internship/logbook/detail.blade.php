@extends('templates.student')

@push('head-scripts')
<style>
    /* Professional Layout Styling */
    .card-detail { border-radius: 12px; border: 1px solid #ebedf2; }
    .detail-section-label { 
        font-size: 0.85rem; font-weight: 800; color: #1572e8; 
        text-transform: uppercase; margin-bottom: 20px; 
        display: flex; align-items: center;
    }
    .detail-section-label::after {
        content: ''; flex: 1; height: 1px; background: #eee; margin-left: 15px;
    }

    /* Content Styling */
    .logbook-body-text { color: #2c3e50; line-height: 1.8; font-size: 1rem; }
    .logbook-body-text img { max-width: 100%; height: auto; border-radius: 10px; margin: 15px 0; border: 1px solid #eee; }
    
    /* Info Badges */
    .info-box { background: #f8f9fa; border-radius: 10px; padding: 20px; border-left: 5px solid #1572e8; }
    
    .btn-modern {
        border-radius: 8px !important; padding: 10px 24px; font-weight: 600;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08); transition: 0.3s ease;
    }
</style>
@endpush

@section('main-content')
<div class="container">
    <div class="page-inner">
        {{-- Breadcrumbs Konsisten --}}
        <div class="page-header">
            <h4 class="page-title">Logbook Detail</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="index.php"><i class="flaticon-home text-primary"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student.internship') }}">My Internship</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student.internship.detail', ['id' => $id_enc]) }}">Detail</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student.logbook.index', ['id' => $id_enc]) }}">Logbook</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Week {{ $data['week_num'] }}</b></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-detail shadow-sm">
                    <div class="card-header bg-white py-4 border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="font-weight-bold mb-0 text-primary">Weekly Report - Week {{ $data['week_num'] }}</h3>
                                <p class="text-muted mb-0"><i class="far fa-calendar-alt mr-1"></i> Period: {{ date('d M Y', strtotime($data['start_date'])) }} - {{ date('d M Y', strtotime($data['end_date'])) }}</p>
                            </div>
                            {{-- <div class="text-right">
                                <span class="badge badge-{{ strtolower($data['approval_spv']) == 'yes' ? 'success' : 'warning' }} px-3 py-2">
                                    Status: {{ strtolower($data['approval_spv']) == 'yes' ? 'Approved by Supervisor' : 'Pending Review' }}
                                </span>
                            </div> --}}
                        </div>
                    </div>

                    <div class="card-body px-lg-5 pb-5">
                        {{-- 1. Job Description --}}
                        <div class="detail-section-label">Job Description & Progress</div>
                        <div class="logbook-body-text mb-5">
                            {!! htmlspecialchars_decode($data['description']) !!}
                        </div>

                        {{-- 2. Documentation --}}
                        <div class="detail-section-label">Documentation & Evidence</div>
                        <div class="logbook-body-text">
                            @if(empty($data['documentation']) || $data['documentation'] == '<p>&nbsp;</p>')
                                <div class="text-center py-4 bg-light rounded">
                                    <i class="fas fa-image fa-2x text-muted mb-2"></i>
                                    <p class="text-muted small">No documentation provided for this week.</p>
                                </div>
                            @else
                                {!! htmlspecialchars_decode($data['documentation']) !!}
                            @endif
                        </div>

                        {{-- 3. Approval Info --}}
                        <div class="mt-5 pt-4 border-top">
                            <div class="row">
                                {{-- Supervisor Approval Column --}}
                                <div class="col-md-6">
                                    <div class="info-box shadow-sm mb-3">
                                        <small class="text-uppercase font-weight-bold text-muted d-block mb-1">Supervisor Approval</small>
                                        <h5 class="font-weight-bold {{ strtolower($data['approval_spv']) == 'yes' ? 'text-success' : 'text-warning' }} mb-1">
                                            <i class="fas {{ strtolower($data['approval_spv']) == 'yes' ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                                            {{ strtoupper($data['approval_spv']) }}
                                        </h5>
                                        
                                        {{-- Display Timestamp SPV --}}
                                        @if(!empty($data['timestamp_spv']) && strtolower($data['approval_spv']) != 'waiting')
                                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                                <i class="fas fa-history mr-1"></i> 
                                                Reviewed on: <b>{{ date('d M Y, H:i', strtotime($data['timestamp_spv'])) }}</b>
                                            </small>
                                        @elseif(strtolower($data['approval_spv']) != 'waiting')
                                            <small class="text-muted font-italic" style="font-size: 0.7rem;">Reviewed by Company HRD.</small>
                                        @else
                                            <small class="text-muted font-italic" style="font-size: 0.7rem;">Waiting for Supervisor approval.</small>
                                        @endif
                                    </div>
                                </div>

                                {{-- Lecturer Approval Column --}}
                                <div class="col-md-6">
                                    <div class="info-box shadow-sm mb-3" style="border-left-color: #6861ce;">
                                        <small class="text-uppercase font-weight-bold text-muted d-block mb-1">Lecturer (DPM) Approval</small>
                                        <h5 class="font-weight-bold {{ strtolower($data['approval_dpm']) == 'yes' ? 'text-success' : 'text-warning' }} mb-1">
                                            <i class="fas {{ strtolower($data['approval_dpm']) == 'yes' ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                                            {{ strtoupper($data['approval_dpm']) }}
                                        </h5>

                                        {{-- Display Timestamp DPM --}}
                                        @if(!empty($data['timestamp_dpm']) && strtolower($data['approval_dpm']) != 'waiting')
                                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                                <i class="fas fa-history mr-1"></i> 
                                                Reviewed on: <b>{{ date('d M Y, H:i', strtotime($data['timestamp_dpm'])) }}</b>
                                            </small>
                                        @elseif(strtolower($data['approval_dpm']) != 'waiting')
                                            <small class="text-muted font-italic" style="font-size: 0.7rem;">Reviewed by University Internship Coordinator.</small>
                                        @else
                                            <small class="text-muted font-italic" style="font-size: 0.7rem;">Waiting for University Lecturer approval.</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                    <div class="card-footer bg-white d-flex justify-content-between py-4 border-0">
                        <button type="button" class="btn btn-light btn-modern border" onclick="history.back()">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Logbook
                        </button>
                        <div>
                            {{-- Hanya bisa edit jika belum di-approve --}}
                            @if(strtolower($data['approval_spv']) != 'yes')
                                <a href="{{ router('student.logbook.edit', ['id' => $id_enc, 'logbook_id' => Encrypter::encrypt($data['id_logbook'])]) }}" class="btn btn-warning btn-modern shadow px-4">
                                    <i class="fas fa-edit mr-2"></i> Edit Report
                                </a>
                            @endif
                            {{-- <button type="button" class="btn btn-primary btn-modern shadow px-4 ml-2" onclick="exportLogbookPDF()">
                                <i class="fas fa-file-pdf mr-2"></i> Export PDF
                            </button> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection