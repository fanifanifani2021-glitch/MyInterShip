@extends('templates.student')

@push('head-scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('main-content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Internship Offer Detail</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="{{ base_url() }}"><i class="flaticon-home"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student/internship_offer.list') }}">Internship Offer</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a>Detail</a></li>
            </ul>
        </div>

        <div class="row">
            {{-- Bagian Kiri: Info Utama --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">General Information</h4>
                        {!! $offer['status'] == 'Open' 
                            ? '<span class="badge badge-success">Open</span>' 
                            : '<span class="badge badge-danger">Closed</span>' !!}
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Company</th>
                                <td>{{ $offer['company_name'] }}</td>
                            </tr>
                            <tr>
                                <th>Position</th>
                                <td><strong>{{ $offer['position'] }}</strong></td>
                            </tr>
                            <tr>
                                <th>Work Period</th>
                                <td>{{ date('d M Y', strtotime($offer['start_date'])) }} - {{ date('d M Y', strtotime($offer['end_date'])) }}</td>
                            </tr>
                            <tr>
                                <th>{{ $offer['offer_type'] == 'Internship' ? 'Allowance' : 'Salary' }}</th>
                                <td>{{ $offer['salary_range'] }}</td>
                            </tr>
                            <tr>
                                <th>City / Job Type</th>
                                <td>{{ $offer['city'] }} ({{ $offer['job_type'] }})</td>
                            </tr>
                            <tr>
                                <th width="30%">Full Address <span class="float-right">:</span></th>
                                <td width="70%">
                                    <span>{{ $offer['full_address'] ?? '-' }}</span>
                                </td>
                            </tr>

                            <tr>
                                <th>Official Website <span class="float-right">:</span></th>
                                <td>
                                    @if($offer['formatted_website'])
                                        <a href="{{ $offer['formatted_website'] }}" target="_blank" class="btn btn-xs btn-outline-primary shadow-sm">
                                            <i class="fas fa-globe mr-1"></i> Visit Website
                                        </a>
                                        <small class="text-muted d-block mt-1">{{ $offer['website'] }}</small>
                                    @else
                                        <span class="text-muted small"><em>Website not provided or invalid</em></span>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <h5 class="mt-4 font-weight-bold">Description</h5>
                        <div class="p-3 bg-light rounded">{!! htmlspecialchars_decode($offer['description']) !!}</div>

                        <h5 class="mt-4 font-weight-bold">Requirements</h5>
                        <div class="p-3 bg-light rounded border-left border-primary">{!! htmlspecialchars_decode($offer['requirement']) !!}</div>
                    </div>
                </div>
            </div>

            {{-- Bagian Kanan: Aksi --}}
            <div class="col-md-4">
                <div class="card card-profile">
                    <div class="card-header" style="background-image: url('{{ base_url('assets/img/blogpost.jpg') }}')">
                        <div class="profile-picture">
                            <div class="avatar avatar-xl">
                                <img src="{{ base_url('assets/uploads/company/'.$offer['company_logo']) }}" alt="..." class="avatar-img rounded-circle border border-white">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="user-profile text-center">
                            <div class="name">{{ $offer['company_name'] }}</div>
                            <div class="job">{{ $offer['employer_type'] }}</div>
                            <div class="desc">Posted {{ time_elapsed_string($offer['timestamp_add']) }}</div>
                        </div>
                    </div>
                    <div class="card-footer">
                        @if($offer['status'] == 'Open' && ($offer['offer_type'] == 'Internship' || $offer['offer_type'] == 'Magang'))
                            <button onclick="applyOffer('{{ Encrypter::encrypt($offer['id_offer']) }}')" class="btn btn-primary btn-block">
                                <i class="fab fa-wpforms mr-2"></i> Apply Now
                            </button>
                        @else
                            <button class="btn btn-secondary btn-block" disabled>Application Closed</button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Applicant List --}}
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center py-4">
                        <h5 class="text-muted mb-2">Interest Gauge</h5>
                        <h2 class="font-weight-bold text-primary">
                            <i class="fas fa-users mr-2"></i> {{ $applicantCount }} 
                            <span style="font-size: 1rem;" class="text-muted">Students have applied to this position</span>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#applicant-table').DataTable();
    });

    function applyOffer(encryptedId) {
        Swal.fire({
            title: 'Apply for this position?',
            text: "Ensure your profile and CV are up to date.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#005b9f',
            confirmButtonText: 'Yes, Apply!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'index.php?page=register_offer&id=' + encryptedId;
            }
        });
    }
</script>
@endpush