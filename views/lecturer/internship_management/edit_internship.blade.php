@extends('templates.lecturer')

@push('head-scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Styling Custom agar Select2 "Masuk" ke Tema KaiAdmin */
        .select2-container--default .select2-selection--single {
            border: 1px solid #ebedf2 !important;
            height: 45px !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 45px !important;
            font-weight: 600;
            color: #495057 !important;
            padding-left: 15px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 45px !important;
        }
        .select2-dropdown {
            border: 1px solid #ebedf2 !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
        }

        /* Profile Box Styling */
        .student-info-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #ebedf2;
        }
        .student-avatar-small {
            width: 60px; height: 60px;
            border-radius: 8px;
            background: #1572e8;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 24px;
        }
        .info-item label {
            display: block; font-size: 10px; font-weight: 800;
            text-transform: uppercase; color: #8d9498;
            margin-bottom: 2px; letter-spacing: 0.5px;
        }
        .info-item span { font-weight: 700; color: #2a2b3d; font-size: 14px; }

        .form-label-custom { font-weight: 700; font-size: 12px; color: #495057; margin-bottom: 8px; text-transform: uppercase; }
        .form-label-custom span { color: #f25961; }
        .form-control-custom { border: 1px solid #ebedf2 !important; border-radius: 8px !important; height: 45px !important; font-weight: 600; }
        .form-control-custom:focus { border-color: #1572e8 !important; box-shadow: 0 0 0 3px rgba(21, 114, 232, 0.1) !important; }
    </style>
@endpush

@section('main-content')
<div class="container">
    <div class="page-inner">
        <div class="page-header mb-4">
            <h4 class="page-title">Edit Internship</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="{{ router('dashboard') }}"><i class="flaticon-home"></i></a></li>
                
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('lecturer/my/student.index') }}">My Supervised Students</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('lecturer/my/student.detail', ['nim' => $nim]) }}">Detail</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('lecturer/my/student.internship.detail', ['nim' => $nim, 'id_internship' => $id_internship]) }}">Internship Detail</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Modify Record</b></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm border-0" style="border-radius: 12px;">
                    <form action="{{ router('lecturer/my/student.internship.update', ['nim' => $nim, 'id_internship' => $id_internship]) }}" method="POST">
                        {!! csrf() !!}
                        @method('PUT')
                        
                        <div class="card-body p-lg-5">
                            {{-- Student Profile Header --}}
                            <div class="student-info-box shadow-sm mb-4">
                                <div class="row align-items-center">
                                    <!-- Avatar/Icon -->
                                    <div class="col-auto">
                                        <div class="student-avatar-small">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                    </div>

                                    <!-- Nama Mahasiswa -->
                                    <div class="col border-right">
                                        <div class="info-item">
                                            <label>Full Name</label>
                                            <span class="text-uppercase">{{ $student['name'] }}</span>
                                        </div>
                                    </div>

                                    <!-- NIM -->
                                    <div class="col-auto px-4 border-right">
                                        <div class="info-item">
                                            <label>Student ID (NIM)</label>
                                            <span>{{ $student['nim'] }}</span>
                                        </div>
                                    </div>

                                    <!-- Detail Prodi & Jurusan -->
                                    <div class="col-md-5 px-4">
                                        <div class="row">
                                            <div class="col-12 mb-2">
                                                <div class="info-item">
                                                    <label>Study Program</label>
                                                    <span>{{ $student['jenjang'] }} {{ $student['prodi_name'] }} ({{ $student['kode_prodi'] }})</span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="info-item">
                                                    <label>Major / Department</label>
                                                    <span>{{ $student['major'] ?? 'Informatics Engineering' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h5 class="fw-bold text-dark mb-4"><i class="fas fa-edit mr-2 text-primary"></i> Internship Details</h5>
                            

                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label class="form-label-custom">Company</label>
                                    <select name="id_company" id="company-select" class="form-control" required>
                                        @foreach($companies as $co)
                                            <option value="{{ $co['id_company'] }}" {{ $co['id_company'] == $internship['id_company'] ? 'selected' : '' }}>{{ $co['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-12 mb-4">
                                    <label class="form-label-custom">Internship Position</label>
                                    <input type="text" name="internship_position" class="form-control form-control-custom" value="{{ $internship['internship_position'] }}" required>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label-custom">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control form-control-custom" value="{{ $internship['start_date'] }}" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label-custom">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control form-control-custom" value="{{ $internship['end_date'] }}" required>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label-custom">Status</label>
                                    <select name="status" class="form-control form-control-custom">
                                        <option value="Waiting" {{ $internship['status'] == 'Waiting' ? 'selected' : '' }}>Waiting</option>
                                        <option value="Yes" {{ $internship['status'] == 'Yes' ? 'selected' : '' }}>Active (Yes)</option>
                                        <option value="Finish" {{ $internship['status'] == 'Finish' ? 'selected' : '' }}>Finished</option>
                                        <option value="No" {{ $internship['status'] == 'No' ? 'selected' : '' }}>Rejected (No)</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label-custom">Period (Months)</label>
                                    <input type="number" name="internship_period" id="internship_period" class="form-control form-control-custom" value="{{ $internship['internship_period'] }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-top d-flex justify-content-end p-4 px-lg-5">
                            <a href="{{ router('lecturer/my/student.internship.detail', ['nim' => $nim, 'id_internship' => $id_internship]) }}" class="text-muted fw-bold mr-4 my-auto">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-save-custom shadow">Update Record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#company-select').select2({ width: '100%' });

            function updateDuration() {
                const startVal = $('#start_date').val();
                const endVal = $('#end_date').val();
                if (startVal && endVal) {
                    const start = new Date(startVal); const end = new Date(endVal);
                    if (end > start) {
                        let months = (end.getFullYear() - start.getFullYear()) * 12;
                        months -= start.getMonth(); months += end.getMonth();
                        $('#internship_period').val(months <= 0 ? 1 : months);
                    }
                }
            }
            $('#start_date, #end_date').on('change', updateDuration);
        });
    </script>
@endpush