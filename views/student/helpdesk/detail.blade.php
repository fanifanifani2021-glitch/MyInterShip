@extends('templates.student', ['title' => 'Ticket Detail #' . $ticket->ticket_code])

@section('main-content')
<div class="container">
    <div class="page-inner mt-2">
        <div class="page-header">
            <h4 class="page-title">Support Ticket Detail</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="{{ router('landing') }}"><i class="flaticon-home"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student.helpdesk.index') }}">Helpdesk</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="#">Ticket #{{ $ticket->ticket_code }}</a></li>
            </ul>
        </div>

        <div class="row">
            {{-- KOLOM UTAMA: ISSUES & DISKUSI THREAD --}}
            <div class="col-md-8">
                {{-- Tiket Utama --}}
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title fw-bold text-primary mb-1">{{ $ticket->subject }}</h4>
                                <small class="text-muted"><i class="fas fa-user mr-1"></i> Submitted by: <strong>{{ $ticket->user_name }}</strong></small>
                            </div>
                            <div>
                                {!! $ticket->getStatusBadge() !!}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="p-3 bg-white rounded border mb-3" style="line-height: 1.7;">
                            {!! html_entity_decode(htmlspecialchars_decode($ticket->description ?? '')) !!}
                        </div>

                        {{-- Lampiran Gambar Tiket Utama --}}
                        @php $attachments = $ticket->attachments(); @endphp
                        @if(count($attachments) > 0)
                            <h6 class="fw-bold mt-4 mb-2"><i class="fas fa-paperclip mr-1 text-primary"></i> Attachments ({{ count($attachments) }}):</h6>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                @foreach($attachments as $att)
                                    <div class="p-1 border rounded bg-white shadow-sm mr-2 mb-2">
                                        <a href="{{ $att->file_path }}" target="_blank" title="{{ $att->file_name }}">
                                            <img src="{{ $att->file_path }}" alt="Attachment" class="img-fluid rounded" style="max-width: 180px; max-height: 130px; object-fit: cover;">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- REPLIES / THREAD PERCAKAPAN --}}
                <h5 class="fw-bold my-3 text-secondary"><i class="fas fa-comments mr-1"></i> Conversation Thread</h5>

                @php $replies = $ticket->replies(); @endphp
                @if(count($replies) === 0)
                    <div class="alert alert-light border text-center text-muted my-3">
                        <i class="fas fa-info-circle mr-1"></i> No replies yet. The CDC support team will respond to your ticket soon.
                    </div>
                @else
                    @foreach($replies as $reply)
                        <div class="card mb-3 {{ $reply->user_type === 'Student' ? 'border-primary' : 'border-success' }}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold {{ $reply->user_type === 'Student' ? 'text-primary' : 'text-success' }}">
                                        <i class="fas {{ $reply->user_type === 'Student' ? 'fa-user-graduate' : 'fa-user-shield' }} mr-1"></i> 
                                        {{ $reply->user_name }} ({{ $reply->user_type }})
                                    </span>
                                    <small class="text-muted">{{ date('d M Y, H:i', strtotime($reply->created_at)) }}</small>
                                </div>
                                <div class="p-2 bg-light rounded">
                                    {!! nl2br(htmlspecialchars($reply->message)) !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- FORM BALAS TIKET --}}
                @if(strtolower($ticket->status) !== 'closed')
                    <div class="card mt-4">
                        <div class="card-header"><h5 class="card-title fw-bold">Post a Reply</h5></div>
                        <div class="card-body">
                            <form id="form-reply-ticket">
                                <?= csrf() ?>
                                <input type="hidden" name="id_ticket" value="{{ $ticket->id_ticket }}">
                                <div class="form-group p-0 mb-3">
                                    <textarea name="message" id="reply-message" class="form-control" rows="4" placeholder="Write your response or follow-up question here..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary fw-bold"><i class="fas fa-reply mr-1"></i> Send Reply</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning border text-center my-3">
                        <i class="fas fa-lock mr-1"></i> This ticket is closed. Re-opening or replying is disabled.
                    </div>
                @endif
            </div>

            {{-- KOLOM SIDEBAR METADATA TIKET --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title fw-bold text-white mb-0">Ticket Details</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Ticket Code</span>
                                <strong class="text-primary">{{ $ticket->ticket_code }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Category</span>
                                <strong>{{ $ticket->category ? $ticket->category->category_name : 'General' }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Priority</span>
                                {!! $ticket->getPriorityBadge() !!}
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Status</span>
                                {!! $ticket->getStatusBadge() !!}
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Created Date</span>
                                <small>{{ date('d M Y, H:i', strtotime($ticket->created_at)) }}</small>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <a href="{{ router('student.helpdesk.index') }}" class="btn btn-light btn-block">
                            <i class="fas fa-arrow-left mr-1"></i> Back to List
                        </a>
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
    let _token = $('meta[name="csrf-token"]').attr('content');

    $(document).ready(function() {
        $('#form-reply-ticket').on('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Posting reply...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ router('student.helpdesk.reply') }}",
                type: "POST",
                data: $(this).serialize() + '&_token=' + _token,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        location.reload();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Internal Server Error occurred.', 'error');
                }
            });
        });
    });
</script>
@endpush