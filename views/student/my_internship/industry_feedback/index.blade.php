@extends('templates.student')

@push('head-scripts')
<style>
    .page-inner { background: #f8f9fa; min-height: 100vh; padding: 30px 20px !important; }

    .card { border-radius: 12px !important; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }

    /* Header Tabel yang Rapi */
    .table-assessment thead th.mark-header { vertical-align: middle; padding: 10px 5px; min-width: 95px; background: #f1f5f9 !important; color: #475569 !important; }
    .mark-number { display: block; font-size: 1.2rem; font-weight: 800; line-height: 1; }
    .mark-label { display: block; font-size: 0.6rem; font-weight: 700; text-transform: uppercase; margin-top: 4px; opacity: 0.8; }

    /* Full-Cell Highlight */
    .table-assessment tbody td.mark-cell { padding: 0 !important; vertical-align: middle; position: relative; border-left: 1px solid #f1f5f9; }
    .assessment-label { display: flex; align-items: center; justify-content: center; width: 100%; height: 65px; margin-bottom: 0; cursor: default; }
    
    /* Warna Solid Saat Terpilih */
    input[type="radio"]:checked + .assessment-label { background-color: #e1effe; }
    input[type="radio"]:checked + .assessment-label .custom-control-label::before {
        background-color: #1572e8 !important; border-color: #1572e8 !important;
    }

    /* Narrative Box Styling */
    .label-sharp { font-weight: 800; color: #1e293b; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 10px; display: block; }
    .comment-box { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px; min-height: 80px; color: #475569; line-height: 1.6; font-style: italic; }
</style>
@endpush

@section('main-content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title fw-bold">Industry Feedback</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="{{ base_url() }}"><i class="flaticon-home text-primary"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student.internship') }}">My Internship</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student.internship.detail', ['id' => $id_enc]) }}">Detail</a></li>
                
                <li class="separator"><i class="flaticon-right-arrow"></i></li>

                <li class="nav-item"><b>Feedback - Industry</b></li>
            </ul>
        </div>

        {{-- Banner Status --}}
        <div class="card bg-primary text-white mb-4">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <h6 class="mb-1 opacity-75">Industry Evaluation Status</h6>
                    <h4 class="fw-bold mb-0">
                        @if($row['terisi'] == 1) 
                            <i class="fas fa-check-circle mr-1"></i> Completed by Industry Supervisor
                        @else 
                            <i class="fas fa-clock mr-1"></i> Pending Industry Evaluation
                        @endif
                    </h4>
                </div>
                <div class="btn-group">
                    @if($row['terisi'] == 1)
                    <button class="btn btn-white btn-sm fw-bold px-3 shadow-sm" data-toggle="modal" data-target="#ModalKonfirmasi">
                        <i class="fas fa-download mr-1"></i> Download PDF
                    </button>
                    @endif
                </div>
            </div>
        </div>



        {{-- Tabel Evaluasi --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-primary mb-0"><i class="fas fa-poll-h mr-2"></i>Evaluation Parameters</h6>
                <button class="btn btn-link btn-sm text-info fw-bold" data-toggle="modal" data-target="#ModalRubrik">VIEW RUBRIC</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-assessment mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" width="60">NO</th>
                                <th class="text-left">CRITERIA</th>
                                <th class="text-center mark-header"><span class="mark-number">4</span><span class="mark-label">Excellent</span></th>
                                <th class="text-center mark-header"><span class="mark-number">3</span><span class="mark-label">Good</span></th>
                                <th class="text-center mark-header"><span class="mark-number">2</span><span class="mark-label">Fair</span></th>
                                <th class="text-center mark-header"><span class="mark-number">1</span><span class="mark-label">Poor</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $params = [
                                    'etika' => 'Ethics, attitude, and behavior in professional environment.',
                                    'keahlian_kompetensi' => 'Core competency skills demonstrated during work.',
                                    'keahlian_bahasa' => 'Foreign language proficiency and communication.',
                                    'penggunaan_ti' => 'Information technology literacy and application.',
                                    'komunikasi' => 'Communication skills with coworkers and clients.',
                                    'kerjasama' => 'Teamwork and collaboration ability.',
                                    'pengembangan_diri' => 'Initiative and personal development willingness.'
                                ];
                                $no = 1;
                            @endphp
                            @foreach($params as $key => $label)
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $no++ }}.</td>
                                <td class="fw-bold text-dark">{{ $label }}</td>
                                @for($v=4; $v>=1; $v--)
                                <td class="mark-cell">
                                    <input type="radio" id="{{$key.$v}}" class="d-none" {{ $row[$key] == $v ? 'checked' : '' }} disabled>
                                    <label class="assessment-label">
                                        <div class="custom-control custom-radio mr-0">
                                            <div class="custom-control-label p-0"></div>
                                        </div>
                                    </label>
                                </td>
                                @endfor
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light py-3 border-top d-flex justify-content-between align-items-center">
                @if($row['terisi'] == 1)
                    {{-- TAMPILAN JIKA SUDAH DIISI --}}
                    <span class="text-muted small">
                        <i class="fas fa-calendar-check mr-1 text-success"></i> 
                        Evaluated on: <b>{{ date('d M Y', strtotime($row['date'])) }}</b>
                    </span>
                    <div class="text-right">
                        <span class="fw-bold mr-2">FINAL SCORE:</span>
                        <span class="h2 fw-extrabold text-primary mb-0">{{ $row['nilai_akhir'] }}</span>
                    </div>
                @else
                    {{-- TAMPILAN JIKA BELUM DIISI --}}
                    <span class="text-warning small italic">
                        <i class="fas fa-info-circle mr-1"></i> 
                        Waiting for industry supervisor to complete the evaluation.
                    </span>
                    <div class="text-right">
                        <span class="badge badge-count badge-warning px-3 py-2">
                            <i class="fas fa-hourglass-half mr-1"></i> EVALUATION PENDING
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Narrative Comments --}}
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <label class="label-sharp text-info"><i class="fas fa-comment-alt mr-2"></i>Comments for Intern</label>
                        <div class="comment-box">"{{ $row['catatan_utk_mahasiswa'] ?: 'No specific comments provided.' }}"</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <label class="label-sharp text-primary"><i class="fas fa-university mr-2"></i>Comments for Polibatam</label>
                        <div class="comment-box">"{{ $row['catatan_utk_poltek'] ?: 'No specific comments provided.' }}"</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <label class="label-sharp text-success"><i class="fas fa-user-check mr-2"></i>Recruitment Eligibility</label>
                        <div class="comment-box">"{{ $row['layak_diterima'] ?: '-' }}"</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <label class="label-sharp text-warning"><i class="fas fa-door-open mr-2"></i>Direct Recruitment Intention</label>
                        <div class="comment-box">"{{ $row['langsung_diterima'] ?: '-' }}"</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Download --}}
<div id="ModalKonfirmasi" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold">Download Evaluation</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="" method="GET" id="formDownloadFeedback">
                <div class="modal-body py-4">
                    <div class="form-group">
                        <label class="fw-bold">Document Language</label>
                        <select required class="form-control" id="selectLanguage" onchange="updateDownloadUrl()">
                            <option value="EN" selected>English Language</option>
                            <option value="ID">Bahasa Indonesia</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info px-4 fw-bold">DOWNLOAD PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updateDownloadUrl() {
        const lang = document.getElementById('selectLanguage').value;
        const form = document.getElementById('formDownloadFeedback');
        // Construct URL sesuai router: /download/{language}
        const baseUrl = "{{ router('student.industry-feedback.download', ['id' => $id_enc, 'language' => 'placeholder']) }}";
        form.action = baseUrl.replace('placeholder', lang);
    }

    // Jalankan sekali saat halaman dimuat agar action terisi default (EN)
    document.addEventListener('DOMContentLoaded', updateDownloadUrl);
</script>

<div id="ModalRubrik" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white py-3">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-chalkboard-teacher mr-2"></i> Industry Assessment Rubric
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" style="font-size: 0.85rem; min-width: 1000px;">
                        <thead class="text-center">
                            <tr class="bg-light">
                                <th width="15%" class="align-middle fw-bold py-3">Parameters</th>
                                <th width="21%" class="rubric-header bg-excellent">
                                    <span class="d-block fw-bold h6 mb-1 text-success">EXCELLENT</span>
                                    <span class="badge badge-success fw-normal">Score: 76 - 100</span>
                                </th>
                                <th width="21%" class="rubric-header bg-good">
                                    <span class="d-block fw-bold h6 mb-1 text-primary">GOOD</span>
                                    <span class="badge badge-primary fw-normal">Score: 51 - 75</span>
                                </th>
                                <th width="21%" class="rubric-header bg-fair">
                                    <span class="d-block fw-bold h6 mb-1 text-warning">FAIR</span>
                                    <span class="badge badge-warning fw-normal text-white">Score: 26 - 50</span>
                                </th>
                                <th width="21%" class="rubric-header bg-poor">
                                    <span class="d-block fw-bold h6 mb-1 text-danger">POOR</span>
                                    <span class="badge badge-danger fw-normal">Score: 0 - 25</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $rubric_data = [
                                    ['Ethics', 'Attitude and behavior can be set as role model; personality meets professional ethics.', 'Attitude and behavior meet standard ethics; able to perform good personality.', 'Attitude and behavior are acceptable; no negative impact for organization.', 'Demonstrates bad attitude/behavior; negative impact on the organization.'],
                                    ['Competency', 'Able to demonstrate exceptional core competencies and provide value added.', 'Able to demonstrate core competencies well; has not provided value added yet.', 'Able to demonstrate sufficient core competencies; needs significant improvement.', 'Failed to demonstrate core competencies as required in the field.'],
                                    ['Language', 'Fluent in oral and written communication using foreign language as per best practices.', 'Able to communicate both orally and in writing using foreign language moderately.', 'Communicate orally/written using foreign language mixed with local language.', 'Unable to understand or communicate using foreign language.'],
                                    ['IT Literacy', 'Able to synthesize and use IT applications to improve performance effectively.', 'Quickly adapt with the information technology tools in the organization.', 'Does not find him/herself in trouble with IT tools in the organization.', 'Unable to use basic information technology in the organization.'],
                                    ['Communication', 'Deliver ideas effectively, actively listen, and public speaking skill.', 'Understand and deliver ideas to others orally and in writing clearly.', 'Minimal misunderstanding when delivering/receiving ideas from coworkers.', 'Unable to understand others and fail to deliver ideas properly.'],
                                    ['Teamwork', 'Able to lead teamwork, achieve goals, and resolve conflicts within the team.', 'Collaborating well with others and contributing to the team results.', 'Able to cooperate in collaborating with other team members to finish work.', 'Gives negative effects to the team or refuses to cooperate.'],
                                    ['Personal Dev.', 'Willing to learn new things and take initiative to improve independently.', 'Willing to learn new things with slight supervision from supervisor.', 'Does not show resistance to learn new things; needs encouragement.', 'Does not show signs of willingness or initiative to learn new skills.']
                                ];
                            @endphp

                            @foreach($rubric_data as $row)
                            <tr>
                                <td class="bg-light fw-bold text-dark align-middle">{{ $row[0] }}</td>
                                <td class="p-3">{{ $row[1] }}</td>
                                <td class="p-3">{{ $row[2] }}</td>
                                <td class="p-3">{{ $row[3] }}</td>
                                <td class="p-3 text-muted italic">{{ $row[4] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm px-4 fw-bold" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Styling for Rubric */
    .rubric-header { vertical-align: middle !important; padding: 15px 10px !important; }
    .bg-excellent { background-color: rgba(40, 167, 69, 0.05); }
    .bg-good { background-color: rgba(21, 114, 232, 0.05); }
    .bg-fair { background-color: rgba(255, 173, 59, 0.05); }
    .bg-poor { background-color: rgba(242, 89, 97, 0.05); }
    .italic { font-style: italic; }
</style>
@endsection