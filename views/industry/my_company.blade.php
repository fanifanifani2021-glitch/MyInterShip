@extends('templates.industry', ['title' => 'My Company'])

@push('head-scripts')
<style>
    .company-logo-avatar {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 4px solid #ffffff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    .profile-cover-header {
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        height: 220px;
        position: relative;
        border-radius: 8px 8px 0 0;
    }
</style>
@endpush

@section('main-content')
<div class="container">
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row justify-content-between">
                <div>
                    <h2 class="text-white pb-2 fw-bold">My Company</h2>
                    <h5 class="text-white op-7 mb-2">Company identity details and profile settings</h5>
                </div>
                @if(($_SESSION['user_type'] ?? '') === 'HRD')
                    <div class="mt-3 mt-md-0">
                        <button class="btn btn-secondary btn-round btn-sm fw-bold" data-toggle="modal" data-target="#ModalEdit">
                            <i class="fas fa-edit mr-1"></i> Edit Company Profile
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">
                <div class="card card-profile shadow-sm">
                    {{-- Cover Banner & Logo --}}
                    <div class="profile-cover-header" style="background-image: url('{{ router('user.profile-picture') }}');">
                        <div class="profile-picture">
                            <div class="avatar avatar-xxl">
                                @php
                                    $logoUrl = !empty($company_data['logo']) ? $company_data['logo'] : 'assets/img/building_default.png';
                                @endphp
                                <img src="{{ $logoUrl }}" alt="Company Logo" class="avatar-img rounded-circle company-logo-avatar">
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-5">
                        <div class="user-profile text-center mb-4 mt-3">
                            <h3 class="fw-bold text-dark mb-1">{{ $company_data['name'] ?? '-' }}</h3>
                            <p class="text-muted small mb-2">Engaged in {{ $company_data['type'] ?? 'General Industry' }}</p>
                            
                            {{-- Status Badge --}}
                            @php
                                $statusStr = strtolower($company_data['status'] ?? '');
                                if (in_array($statusStr, [1, 'verified'])) {
                                    $statusBadge = '<span class="badge badge-success px-3 py-2">Verified</span>';
                                } elseif (in_array($statusStr, [2, 'blacklist'])) {
                                    $statusBadge = '<span class="badge badge-dark px-3 py-2">Blacklisted</span>';
                                } else {
                                    $statusBadge = '<span class="badge badge-danger px-3 py-2">Not Verified</span>';
                                }
                            @endphp
                            <div>
                                {!! $statusBadge !!}
                                <i class="fas fa-info-circle text-info ml-2" data-toggle="tooltip" data-placement="top" 
                                   title="Verified: Registered & verified for Polibatam internships. Not Verified: Under review. Blacklist: Restricted."></i>
                            </div>
                        </div>

                        {{-- Details Table --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <tbody>
                                    <tr>
                                        <th width="30%" class="bg-light">Company Name</th>
                                        <td width="70%"><strong>{{ $company_data['name'] ?? '-' }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th width="30%" class="bg-light">Industry Type / Sector</th>
                                        <td width="70%">{{ $company_data['type'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%" class="bg-light">Contact Phone</th>
                                        <td width="70%">{{ $company_data['phone'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%" class="bg-light">Official Email</th>
                                        <td width="70%">{{ $company_data['email'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%" class="bg-light">Website</th>
                                        <td width="70%">
                                            @if(!empty($company_data['website']))
                                                <a href="{{ $company_data['website'] }}" target="_blank">{{ $company_data['website'] }}</a>
                                            @else - @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="30%" class="bg-light">Social Media</th>
                                        <td width="70%">
                                            <span class="mr-3"><i class="fab fa-linkedin text-primary mr-1"></i> {{ $company_data['linkedin'] ?: '-' }}</span>
                                            <span class="mr-3"><i class="fab fa-facebook text-info mr-1"></i> {{ $company_data['facebook'] ?: '-' }}</span>
                                            <span class="mr-3"><i class="fab fa-instagram text-danger mr-1"></i> {{ $company_data['instagram'] ?: '-' }}</span>
                                            <span><i class="fab fa-twitter text-primary mr-1"></i> {{ $company_data['twitter'] ?: '-' }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="30%" class="bg-light">Address</th>
                                        <td width="70%">{{ $company_data['address'] ?? '-' }}, {{ $company_data['city'] ?? '-' }}, {{ $company_data['province'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th width="30%" class="bg-light">Offer Access Type</th>
                                        <td width="70%">
                                            @if(($company_data['access_type'] ?? 0) == 1)
                                                <span class="badge badge-info">1 - Authorized to publish Job Offers</span>
                                            @else
                                                <span class="badge badge-secondary">2 - Standard Access</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        @if(($_SESSION['user_type'] ?? '') === 'HRD')
                            <div class="mt-3">
                                <button class="btn btn-primary btn-sm fw-bold" data-toggle="modal" data-target="#ModalEdit">
                                    <i class="fas fa-edit mr-1"></i> Edit Company Profile
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Company Description Card --}}
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-light">
                        <h4 class="card-title fw-bold text-primary mb-0"><i class="fas fa-align-left mr-2"></i> Company Description</h4>
                    </div>
                    <div class="card-body">
                        {!! $company_data['description'] ?? '<p class="text-muted">No description provided yet.</p>' !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT COMPANY PROFILE (HRD ONLY) --}}
@if(($_SESSION['user_type'] ?? '') === 'HRD')
<div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-building mr-1"></i> Edit Company Profile</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ router('industry.my_company.update') }}" method="POST">
                <?= csrf() ?>

                <div class="modal-body form-row">
                    <div class="form-group col-md-12 mb-3">
                        <label class="fw-bold">Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= show_error('name'); ?>" name="name" value="<?= old('name', $company_data['name'] ?? ''); ?>" placeholder="Enter Company Name" required>
                        <?= form_error('name'); ?>
                    </div>

                    <div class="form-group col-md-12 mb-3">
                        <label class="fw-bold">Company Type / Sector <span class="text-danger">*</span></label>
                        <select class="form-control <?= show_error('company_type'); ?>" name="company_type" id="company_type" onchange="toggle_company()">
                            <option value="" hidden>- Choose Company Type -</option>

                            @php $currType = old('company_type', $company_data['type'] ?? ''); @endphp

                            <optgroup label="Types of Companies">
                                <option value="Agency" {{ $currType === 'Agency' ? 'selected' : '' }}>Agency</option>
                                <option value="Design" {{ $currType === 'Design' ? 'selected' : '' }}>Design</option>
                                <option value="Education" {{ $currType === 'Education' ? 'selected' : '' }}>Education</option>
                                <option value="Engineering" {{ $currType === 'Engineering' ? 'selected' : '' }}>Engineering</option>
                                <option value="Finance" {{ $currType === 'Finance' ? 'selected' : '' }}>Finance</option>
                                <option value="Government" {{ $currType === 'Government' ? 'selected' : '' }}>Government</option>
                                <option value="Health" {{ $currType === 'Health' ? 'selected' : '' }}>Health</option>
                                <option value="IT and Telecommunication" {{ $currType === 'IT and Telecommunication' ? 'selected' : '' }}>IT and Telecommunication</option>
                                <option value="Logistic" {{ $currType === 'Logistic' ? 'selected' : '' }}>Logistic</option>
                                <option value="Marketing" {{ $currType === 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="Media" {{ $currType === 'Media' ? 'selected' : '' }}>Media</option>
                                <option value="Other" {{ $currType === 'Other' ? 'selected' : '' }}>Other</option>
                            </optgroup>

                            <optgroup label="Industry Specific Companies">
                                <option value="Advertising Agency" {{ $currType === 'Advertising Agency' ? 'selected' : '' }}>Advertising Agency</option>
                                <option value="Talent Agency" {{ $currType === 'Talent Agency' ? 'selected' : '' }}>Talent Agency</option>
                                <option value="Employment Agency" {{ $currType === 'Employment Agency' ? 'selected' : '' }}>Employment Agency</option>
                                <option value="Travel Agency" {{ $currType === 'Travel Agency' ? 'selected' : '' }}>Travel Agency</option>
                                <option value="Engineering Firm" {{ $currType === 'Engineering Firm' ? 'selected' : '' }}>Engineering Firm</option>
                                <option value="Civil Engineering Company" {{ $currType === 'Civil Engineering Company' ? 'selected' : '' }}>Civil Engineering Company</option>
                                <option value="Mechanical Engineering Company" {{ $currType === 'Mechanical Engineering Company' ? 'selected' : '' }}>Mechanical Engineering Company</option>
                                <option value="Electrical Engineering Company" {{ $currType === 'Electrical Engineering Company' ? 'selected' : '' }}>Electrical Engineering Company</option>
                                <option value="Bank" {{ $currType === 'Bank' ? 'selected' : '' }}>Bank</option>
                                <option value="Insurance Company" {{ $currType === 'Insurance Company' ? 'selected' : '' }}>Insurance Company</option>
                                <option value="Investment Company" {{ $currType === 'Investment Company' ? 'selected' : '' }}>Investment Company</option>
                                <option value="Asset Management Company" {{ $currType === 'Asset Management Company' ? 'selected' : '' }}>Asset Management Company</option>
                                <option value="Electronic Manufacturing Company" {{ $currType === 'Electronic Manufacturing Company' ? 'selected' : '' }}>Electronic Manufacturing Company</option>
                                <option value="Automotive Manufacturing Company" {{ $currType === 'Automotive Manufacturing Company' ? 'selected' : '' }}>Automotive Manufacturing Company</option>
                                <option value="Food and Beverage Manufacturing Company" {{ $currType === 'Food and Beverage Manufacturing Company' ? 'selected' : '' }}>Food and Beverage Manufacturing Company</option>
                                <option value="Software Development Company" {{ $currType === 'Software Development Company' ? 'selected' : '' }}>Software Development Company</option>
                                <option value="Cloud Technology Company" {{ $currType === 'Cloud Technology Company' ? 'selected' : '' }}>Cloud Technology Company</option>
                                <option value="Cybersecurity Company" {{ $currType === 'Cybersecurity Company' ? 'selected' : '' }}>Cybersecurity Company</option>
                                <option value="Hospital" {{ $currType === 'Hospital' ? 'selected' : '' }}>Hospital</option>
                                <option value="Health Clinic" {{ $currType === 'Health Clinic' ? 'selected' : '' }}>Health Clinic</option>
                                <option value="Pharmaceutical Company" {{ $currType === 'Pharmaceutical Company' ? 'selected' : '' }}>Pharmaceutical Company</option>
                                <option value="Building Construction Company" {{ $currType === 'Building Construction Company' ? 'selected' : '' }}>Building Construction Company</option>
                                <option value="Infrastructure Construction Company" {{ $currType === 'Infrastructure Construction Company' ? 'selected' : '' }}>Infrastructure Construction Company</option>
                                <option value="School or Educational Institution" {{ $currType === 'School or Educational Institution' ? 'selected' : '' }}>School or Educational Institution</option>
                                <option value="Educational Book Publishing Company" {{ $currType === 'Educational Book Publishing Company' ? 'selected' : '' }}>Educational Book Publishing Company</option>
                                <option value="E-commerce Company" {{ $currType === 'E-commerce Company' ? 'selected' : '' }}>E-commerce Company</option>
                                <option value="Retail Store" {{ $currType === 'Retail Store' ? 'selected' : '' }}>Retail Store</option>
                                <option value="Distribution Company" {{ $currType === 'Distribution Company' ? 'selected' : '' }}>Distribution Company</option>
                                <option value="Film and Television Production Company" {{ $currType === 'Film and Television Production Company' ? 'selected' : '' }}>Film and Television Production Company</option>
                                <option value="Music Company" {{ $currType === 'Music Company' ? 'selected' : '' }}>Music Company</option>
                                <option value="Gaming Company" {{ $currType === 'Gaming Company' ? 'selected' : '' }}>Gaming Company</option>
                                <option value="Management Consulting" {{ $currType === 'Management Consulting' ? 'selected' : '' }}>Management Consulting</option>
                                <option value="Financial Consulting" {{ $currType === 'Financial Consulting' ? 'selected' : '' }}>Financial Consulting</option>
                                <option value="Human Resources Consulting" {{ $currType === 'Human Resources Consulting' ? 'selected' : '' }}>Human Resources Consulting</option>
                                <option value="Agricultural Company" {{ $currType === 'Agricultural Company' ? 'selected' : '' }}>Agricultural Company</option>
                                <option value="Plantation Company" {{ $currType === 'Plantation Company' ? 'selected' : '' }}>Plantation Company</option>
                            </optgroup>
                        </select>
                        <?= form_error('company_type'); ?>
                    </div>

                    <div class="form-group col-md-12 mb-3" id="div_company_other" style="display: none;">
                        <label class="fw-bold">Specify Other Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= show_error('company_type_other'); ?>" name="company_type_other" id="company_type_other" value="<?= old('company_type_other', $company_data['type'] ?? ''); ?>" placeholder="Fill Sector/Type">
                        <?= form_error('company_type_other'); ?>
                    </div>

                    <div class="form-group col-md-6 mb-3">
                        <label class="fw-bold">Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= show_error('phone'); ?>" name="phone" value="<?= old('phone', $company_data['phone'] ?? ''); ?>" required>
                        <?= form_error('phone'); ?>
                    </div>

                    <div class="form-group col-md-6 mb-3">
                        <label class="fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control <?= show_error('email'); ?>" name="email" value="<?= old('email', $company_data['email'] ?? ''); ?>" required>
                        <?= form_error('email'); ?>
                    </div>

                    <div class="form-group col-md-6 mb-3">
                        <label class="fw-bold">LinkedIn</label>
                        <input type="text" class="form-control" name="linkedin" value="<?= old('linkedin', $company_data['linkedin'] ?? ''); ?>">
                    </div>

                    <div class="form-group col-md-6 mb-3">
                        <label class="fw-bold">Website</label>
                        <input type="text" class="form-control" name="website" value="<?= old('website', $company_data['website'] ?? ''); ?>">
                    </div>

                    <div class="form-group col-md-6 mb-3">
                        <label class="fw-bold">Facebook</label>
                        <input type="text" class="form-control" name="facebook" value="<?= old('facebook', $company_data['facebook'] ?? ''); ?>">
                    </div>

                    <div class="form-group col-md-6 mb-3">
                        <label class="fw-bold">Twitter</label>
                        <input type="text" class="form-control" name="twitter" value="<?= old('twitter', $company_data['twitter'] ?? ''); ?>">
                    </div>

                    <div class="form-group col-md-6 mb-3">
                        <label class="fw-bold">Instagram</label>
                        <input type="text" class="form-control" name="instagram" value="<?= old('instagram', $company_data['instagram'] ?? ''); ?>">
                    </div>

                    <div class="form-group col-md-6 mb-3">
                        <label class="fw-bold">Logo Image Link / Path</label>
                        <input type="text" class="form-control" name="logo" value="<?= old('logo', $company_data['logo'] ?? ''); ?>">
                    </div>

                    <div class="form-group col-md-12 mb-3">
                        <label class="fw-bold">Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= show_error('address'); ?>" name="address" value="<?= old('address', $company_data['address'] ?? ''); ?>" required>
                        <?= form_error('address'); ?>
                    </div>

                    <div class="form-group col-md-6 mb-3">
                        <label class="fw-bold">Province <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= show_error('province'); ?>" name="province" value="<?= old('province', $company_data['province'] ?? ''); ?>" required>
                        <?= form_error('province'); ?>
                    </div>

                    <div class="form-group col-md-6 mb-3">
                        <label class="fw-bold">City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= show_error('city'); ?>" name="city" value="<?= old('city', $company_data['city'] ?? ''); ?>" required>
                        <?= form_error('city'); ?>
                    </div>

                    <div class="form-group col-md-12 mb-3">
                        <label class="fw-bold">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control <?= show_error('description'); ?>" id="description_editor" name="description" rows="5" required><?= old('description', $company_data['description'] ?? ''); ?></textarea>
                        <?= form_error('description'); ?>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i class="fas fa-times mr-1"></i> Close</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold"><i class="fas fa-save mr-1"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('footer-scripts')
<script>
    function toggle_company() {
        var company_type = document.getElementById('company_type');
        var div_company_other = document.getElementById('div_company_other');

        if (company_type && div_company_other) {
            if (company_type.value === 'Other') {
                div_company_other.style.display = 'block';
            } else {
                div_company_other.style.display = 'none';
            }
        }
    }

    $(document).ready(function() {
        toggle_company();

        // Inisialisasi CKEditor untuk Deskripsi Perusahaan
        if (typeof CKEDITOR !== 'undefined' && $('#description_editor').length) {
            CKEDITOR.replace('description_editor', {
                removeButtons: 'PasteFromWord,Styles,Format,Font,FontSize'
            });
        }

        // Tampilkan modal secara otomatis jika validasi gagal
        @if(Session::get('ModalEdit') || old('ModalEdit'))
            $('#ModalEdit').modal('show');
        @endif
    });
</script>
@endpush