@extends('templates.cdc')

@push('head-scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>
    /* === UI ENHANCEMENT === */
    .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); transition: all 0.3s ease; }
    .card-header { 
        background: transparent !important; 
        border-bottom: 1px solid rgba(0,0,0,0.05) !important; 
        padding: 1.25rem 1.5rem !important;
        display: flex;
        align-items: center; 
        justify-content: space-between;
        min-height: 70px;
    }

    /* === STATUS BADGE === */
    .status-container { display: flex; align-items: center; justify-content: center; }
    .badge-status {
        padding: 0.5rem 1rem; border-radius: 50px; font-weight: 700; font-size: 0.75rem;
        letter-spacing: 0.5px; text-transform: uppercase; display: inline-flex; align-items: center; line-height: 1;
    }

    /* === TIMELINE REFINEMENT === */
    .timeline-wrapper { position: relative; margin-left: 10px; padding-left: 30px; border-left: 2px solid #e9ecef; }
    .timeline-item { position: relative; padding-bottom: 25px; }
    .timeline-marker { 
        position: absolute; left: -39px; top: 5px; width: 16px; height: 16px; 
        background: #ced4da; border: 3px solid #fff; border-radius: 50%; z-index: 1;
    }
    /* Highlight status terakhir */
    .timeline-item.is-latest .timeline-marker { 
        background: #1572E8; transform: scale(1.3); box-shadow: 0 0 0 4px rgba(21, 114, 232, 0.2);
    }
    .timeline-item.is-latest .timeline-text { font-weight: 700; color: #1a2035; font-size: 0.95rem; }
    .timeline-date { font-size: 0.8rem; color: #8d94ad; margin-bottom: 4px; display: block; }
    .timeline-text { font-size: 0.9rem; color: #495057; line-height: 1.5; }

    /* === DECISION TILES === */
    .decision-wrapper { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 10px; }
    .decision-tile {
        cursor: pointer; border: 2px solid #ebedf2; border-radius: 12px; padding: 20px 10px;
        text-align: center; transition: all 0.25s ease; background: #fff;
    }
    .decision-tile i { font-size: 1.8rem; display: block; margin-bottom: 10px; opacity: 0.5; }
    .decision-tile.active-approve { border-color: #31ce36; background: #f0fff1; color: #31ce36; }
    .decision-tile.active-approve i { opacity: 1; }
    .decision-tile.active-reject { border-color: #f3545d; background: #fff5f6; color: #f3545d; }
    .decision-tile.active-reject i { opacity: 1; }

    #letter_number_container { display: none; margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 10px; border-left: 4px solid #31ce36; }
    .badge-breathing { animation: breathing 1.8s infinite ease-in-out; }
    @keyframes breathing { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }

    /* Preview Frame Styling */
    .preview-frame-container { background: #525659; padding: 10px; border-radius: 8px; box-shadow: inset 0 0 10px rgba(0,0,0,0.2); }
</style>
@endpush

@section('main-content')
<div class="container">
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div>
                    <h2 class="text-white fw-bold mb-1"><i class="fas fa-file-signature mr-2"></i> CDC Internship Letter Review</h2>
                    <h5 class="text-white op-7">Verification and document management system.</h5>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="{{ base_url('index.php?page=cdc/internship_letter') }}" class="btn btn-white btn-border btn-round btn-sm">
                        <i class="fas fa-chevron-left mr-2"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row align-items-start"> {{-- Penting untuk keselarasan atas --}}
            
            {{-- KOLOM KIRI: DATA & DOCUMENT --}}
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title fw-bold text-primary">Student & Letter Details</div>
                        <div class="status-container">
                            @php
                                $statusMap = ['WAITING' => 'warning', 'APPROVED' => 'success', 'REJECTED' => 'danger'];
                                $badgeColor = $statusMap[strtoupper($letter['status'] ?? 'WAITING')] ?? 'secondary';
                            @endphp
                            <span class="badge-status bg-{{ $badgeColor }} text-white">
                                {{ strtoupper($letter['status']) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 mb-4">
                                <label class="text-muted small uppercase fw-bold mb-1">Student Name</label>
                                <p class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">{{ $letter['student_name'] }}</p>
                                <p class="text-muted mb-0">NIM: {{ $letter['student_nim'] }}</p>
                            </div>
                            <div class="col-sm-6 mb-4">
                                <label class="text-muted small uppercase fw-bold mb-1">Program Study</label>
                                <p class="fw-bold mb-0">{{ $letter['jenjang'] }} {{ $letter['study_program'] }}</p>
                                <p class="text-muted mb-0">{{ $letter['jurusan'] }}</p>
                            </div>
                            <div class="col-sm-6 mb-4">
                                <label class="text-muted small uppercase fw-bold mb-1">Language</label>
                                <div>
                                    <span class="badge {{ $letter['letter_languange'] == 'EN' ? 'bg-info' : 'bg-secondary' }} text-white px-3">
                                        <i class="fas fa-language mr-1"></i> {{ $letter['letter_languange'] == 'EN' ? 'English' : 'Indonesia' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-4">
                                <label class="text-muted small uppercase fw-bold mb-1">Internship Period</label>
                                <p class="fw-bold text-primary mb-0">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    {{ date('d M Y', strtotime($letter['expected_start'])) }} - {{ date('d M Y', strtotime($letter['expected_end'])) }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="bg-light p-3 rounded-lg border">
                            <label class="text-muted small uppercase fw-bold mb-2 d-block">Destination Company</label>
                            <div class="d-flex align-items-start">
                                <div class="mr-3"><i class="fas fa-building fa-2x text-muted"></i></div>
                                <div>
                                    <p class="fw-bold mb-1 text-dark">{{ $letter['company_name'] }}</p>
                                    <p class="text-muted small mb-0">{{ $letter['company_address'] }}, {{ $letter['company_city'] }}</p>
                                    @if($letter['is_new_company'])
                                        <div class="mt-2">
                                            <span class="badge badge-warning badge-breathing"><i class="fas fa-star mr-1"></i> Suggested New Company</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- INTERNSHIP LETTER DOCUMENT (Tampil hanya jika sudah APPROVED) --}}
                @if(strtoupper($letter['status']) == 'APPROVED')
                <div class="card mt-4 border-top border-success">
                    <div class="card-header">
                        <div class="card-title fw-bold text-success">
                            <i class="fas fa-file-pdf mr-2"></i> Internship Letter Document
                        </div>
                        <button onclick="document.getElementById('previewLetter').contentWindow.print()" class="btn btn-success btn-round btn-sm">
                            <i class="fas fa-print mr-1"></i> Print / PDF
                        </button>
                    </div>
                    <div class="card-body">
                        @php
                            $token = $_SESSION['PRINT_TOKEN'] ?? '';
                            $issuedDate = !empty($letter['letter_date']) ? date('F d, Y', strtotime($letter['letter_date'])) : '-';
                        @endphp
                        <div class="d-flex justify-content-between mb-3 small">
                            <span><strong>Issued on:</strong> {{ $issuedDate }}</span>
                            <span><strong>No:</strong> {{ $letter['full_letter_number'] ?? '-' }}</span>
                        </div>
                        <div class="preview-frame-container">
                            <iframe id="previewLetter"
                                src="{{ base_url('index.php?page=print_internship_letter&id=' . $letter['id_internship_letter'] . '&token=' . $token) }}"
                                width="100%" height="500px" style="border:none; background: white; border-radius: 4px;">
                            </iframe>
                        </div>
                    </div>
                </div>
                @endif

                {{-- APPROVAL LOGS --}}
                <div class="card mt-4">
                    <div class="card-header"><div class="card-title fw-bold text-primary">Approval Logs</div></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0">Role</th>
                                        <th class="border-0">User</th>
                                        <th class="border-0 text-center">Result</th>
                                        <th class="border-0">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($approvals as $a)
                                        <tr>
                                            <td><span class="badge badge-{{ $a['role'] == 'cdc' ? 'info' : 'primary' }}">{{ strtoupper($a['role']) }}</span></td>
                                            <td><span class="fw-bold text-dark">{{ $a['role_name'] }}</span></td>
                                            <td class="text-center">
                                                @if($a['status_approval'] == 'approved')
                                                    <span class="text-success"><i class="fas fa-check-circle mr-1"></i> Approved</span>
                                                @else
                                                    <span class="text-danger"><i class="fas fa-times-circle mr-1"></i> Rejected</span>
                                                @endif
                                            </td>
                                            <td class="text-muted small">{{ date('d M Y, H:i', strtotime($a['created_at'])) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-4 text-muted">No approval logs yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: AKSI & STATUS --}}
            <div class="col-lg-5">
                {{-- TAKE ACTION CARD (Hanya jika WAITING) --}}
                @if(strtoupper($letter['cdc_approval']) == 'WAITING')
                <div class="card border-top border-primary">
                    <div class="card-header"><div class="card-title fw-bold">CDC Action Center</div></div>
                    <form method="POST" action="{{ base_url('index.php?form=cdc/letter_approval') }}" id="approvalForm">
                        {{ csrf() }}
                        <input type="hidden" name="id_internship_letter" value="{{ $letter['id_internship_letter'] }}">
                        
                        <div class="card-body">
                            <label class="form-label d-block mb-3 fw-bold text-muted small uppercase">Decision</label>
                            <div class="decision-wrapper">
                                <label class="decision-tile" id="tile-approve">
                                    <input type="radio" name="action" value="approved" onclick="handleDecision('approve')">
                                    <i class="fas fa-check-circle"></i>
                                    <span class="fw-bold">APPROVE</span>
                                </label>
                                <label class="decision-tile" id="tile-reject">
                                    <input type="radio" name="action" value="rejected" onclick="handleDecision('reject')">
                                    <i class="fas fa-times-circle"></i>
                                    <span class="fw-bold">REJECT</span>
                                </label>
                            </div>

                            <div id="letter_number_container">
                                <label class="fw-bold mb-1 small text-dark">Assign Letter Number <span class="text-danger">*</span></label>
                                <input type="text" name="letter_number" id="letter_number" class="form-control" placeholder="E.g. 123/PL29/XII/2026">
                                <div class="mt-2 p-2 rounded bg-white border small text-success">
                                    <i class="fas fa-history mr-1"></i> Reference Last: <strong>{{ $last_letter_number }}</strong>
                                </div>
                            </div>

                            <div class="form-group p-0 mt-4">
                                <label class="fw-bold text-muted small uppercase">Notes (Optional)</label>
                                <textarea name="remark" class="form-control" rows="3" placeholder="Add details for the student..."></textarea>
                            </div>
                        </div>
                        <div class="card-footer p-3">
                            <button type="submit" class="btn btn-primary btn-round btn-block fw-bold py-2">
                                <i class="fas fa-save mr-2"></i> Save Decision
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                {{-- STATUS HISTORY --}}
                {{-- Gunakan logic mt-4 hanya jika Action Card ada di atasnya --}}
                <div class="card @if(strtoupper($letter['cdc_approval']) == 'WAITING') mt-4 @endif">
                    <div class="card-header"><div class="card-title fw-bold text-dark">Status History</div></div>
                    <div class="card-body">
                        <div class="timeline-wrapper">
                            @forelse($histories as $h)
                                @php $isLatest = $loop->first; @endphp
                                <div class="timeline-item {{ $isLatest ? 'is-latest' : '' }}">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <span class="timeline-date {{ $isLatest ? 'text-primary fw-bold' : '' }}">
                                            {{ date('j F Y, H:i', strtotime($h['created_at'])) }}
                                            @if($isLatest) <span class="badge badge-primary badge-pill ml-2" style="font-size: 9px;">LATEST</span> @endif
                                        </span>
                                        <p class="timeline-text">{{ $h['status'] }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted small">No tracks found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function handleDecision(type) {
        const approveTile = $('#tile-approve');
        const rejectTile = $('#tile-reject');
        const numContainer = $('#letter_number_container');
        const numInput = $('#letter_number');

        approveTile.removeClass('active-approve');
        rejectTile.removeClass('active-reject');

        if (type === 'approve') {
            approveTile.addClass('active-approve');
            numContainer.slideDown(200);
            numInput.attr('required', 'required');
        } else {
            rejectTile.addClass('active-reject');
            numContainer.slideUp(200);
            numInput.removeAttr('required').val('');
        }
    }

    $('#approvalForm').on('submit', function(e) {
        if (!$('input[name="action"]:checked').val()) {
            e.preventDefault();
            swal("Attention", "Please select Approve or Reject decision.", "warning");
        }
    });
</script>
@endsection