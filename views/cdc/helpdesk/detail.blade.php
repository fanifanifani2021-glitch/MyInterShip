@extends('templates.cdc', ['title' => 'Review Ticket #' . $ticket->ticket_code])

@push('head-scripts')
<style>
    /* Styling Upload Zone & Preview Cards */
    .upload-zone-sm {
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .upload-zone-sm:hover {
        border-color: #005b9f;
        background: #f1f5f9;
    }
    .attachment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 12px;
        margin-top: 15px;
    }
    .preview-card-item {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px;
        background: #ffffff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .preview-image-box {
        width: 100%;
        height: 120px;
        border-radius: 4px;
        overflow: hidden;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        margin-bottom: 8px;
    }
    .preview-image-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .badge-size {
        font-size: 10px;
        padding: 3px 6px;
        border-radius: 4px;
        font-weight: 600;
    }
    .file-name-truncated {
        font-size: 11px;
        font-weight: 700;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@section('main-content')
<div class="container">
    <div class="page-inner mt-2">
        <div class="page-header">
            <h4 class="page-title">Review & Respond Ticket</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="{{ router('cdc.helpdesk.index') }}"><i class="flaticon-home"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('cdc.helpdesk.index') }}">Helpdesk</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="#">Ticket #{{ $ticket->ticket_code }}</a></li>
            </ul>
        </div>

        <div class="row">
            {{-- THREAD DISKUSI (KIRI) --}}
            <div class="col-md-8">
                {{-- Tiket Utama --}}
                <div class="card border-left border-primary">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title fw-bold text-primary mb-1">{{ $ticket->subject }}</h4>
                                <small class="text-muted"><i class="fas fa-user-graduate mr-1"></i> Submitted by: <strong>{{ $ticket->user_name }}</strong> ({{ $ticket->user_id }})</small>
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

                        {{-- Lampiran Tiket Utama --}}
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

                {{-- THREAD BALASAN --}}
                <h5 class="fw-bold my-3 text-secondary"><i class="fas fa-comments mr-1"></i> Thread History</h5>

                @php $replies = $ticket->replies(); @endphp
                @if(count($replies) === 0)
                    <div class="alert alert-light border text-center text-muted my-3">
                        <i class="fas fa-info-circle mr-1"></i> No staff replies posted yet.
                    </div>
                @else
                    @foreach($replies as $reply)
                        <div class="card mb-3 {{ $reply->user_type === 'CDC' ? 'border-success' : 'border-primary' }}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold {{ $reply->user_type === 'CDC' ? 'text-success' : 'text-primary' }}">
                                        <i class="fas {{ $reply->user_type === 'CDC' ? 'fa-user-shield' : 'fa-user-graduate' }} mr-1"></i> 
                                        {{ $reply->user_name }} ({{ $reply->user_type }})
                                    </span>
                                    <small class="text-muted">{{ date('d M Y, H:i', strtotime($reply->created_at)) }}</small>
                                </div>
                                <div class="p-2 bg-light rounded mb-2">
                                    {!! nl2br(htmlspecialchars($reply->message)) !!}
                                </div>

                                {{-- Lampiran Reply jika ada --}}
                                @php $replyAtts = $reply->attachments(); @endphp
                                @if(count($replyAtts) > 0)
                                    <div class="d-flex flex-wrap mt-2">
                                        @foreach($replyAtts as $rAtt)
                                            <a href="{{ $rAtt->file_path }}" target="_blank" class="mr-2 mb-2">
                                                <img src="{{ $rAtt->file_path }}" alt="Reply Attachment" class="img-fluid rounded border" style="max-width: 120px; max-height: 90px; object-fit: cover;">
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- FORM BALAS TIKET OLEH CDC --}}
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title fw-bold text-dark mb-0"><i class="fas fa-reply text-primary mr-2"></i>Post Staff Response</h5>
                    </div>
                    <div class="card-body">
                        <form id="form-cdc-reply" enctype="multipart/form-data">
                            <?= csrf() ?>
                            <div class="form-group p-0 mb-3">
                                <label class="fw-bold">Response Message <span class="text-danger">*</span></label>
                                <textarea name="message" id="reply-message" class="form-control" rows="5" placeholder="Type your response to the student here..." required></textarea>
                            </div>

                            <div class="form-group p-0 mb-3">
                                <label class="fw-bold">Update Ticket Status</label>
                                <select name="status" class="form-control">
                                    <option value="In Progress" {{ $ticket->status === 'In Progress' ? 'selected' : '' }}>In Progress (Needs Follow Up)</option>
                                    <option value="Resolved" {{ $ticket->status === 'Resolved' ? 'selected' : '' }}>Resolved (Issue Fixed)</option>
                                    <option value="Closed" {{ $ticket->status === 'Closed' ? 'selected' : '' }}>Closed (Finished)</option>
                                </select>
                            </div>

                            {{-- MULTIPLE ATTACHMENT FOR CDC REPLY --}}
                            <div class="form-group p-0 mb-3">
                                <label class="fw-bold">Attach Screenshots / Proofs (Optional)</label>

                                <input type="file" id="input_file_gallery" accept="image/jpeg,image/png,image/webp" multiple style="display: none;" onchange="handleMultipleImageSelection(this)">

                                <div class="upload-zone-sm" onclick="$('#input_file_gallery').click()">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-1"></i>
                                    <p class="text-muted small mb-0">Click to select proof images to send with response (Max 3MB per file)</p>
                                </div>

                                <div class="attachment-grid" id="attachment-grid"></div>
                            </div>

                            <button type="submit" class="btn btn-success fw-bold shadow-sm">
                                <i class="fas fa-paper-plane mr-1"></i> Send Response
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- SIDEBAR TIKET ACTIONS (KANAN) --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title fw-bold text-white mb-0">Ticket Information</h5>
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
                                <span class="text-muted">Assigned To</span>
                                <strong>{{ $ticket->assigned_to ?? 'Unassigned' }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Created At</span>
                                <small>{{ date('d M Y, H:i', strtotime($ticket->created_at)) }}</small>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- QUICK STATUS & PRIORITY UPDATE --}}
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h5 class="card-title fw-bold text-dark mb-0">Quick Action</h5>
                    </div>
                    <div class="card-body">
                        <form id="form-quick-update">
                            <?= csrf() ?>
                            <div class="form-group p-0 mb-3">
                                <label>Change Status</label>
                                <select name="status" class="form-control form-control-sm">
                                    <option value="Open" {{ $ticket->status === 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="In Progress" {{ $ticket->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="Resolved" {{ $ticket->status === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="Closed" {{ $ticket->status === 'Closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>

                            <div class="form-group p-0 mb-3">
                                <label>Change Priority</label>
                                <select name="priority" class="form-control form-control-sm">
                                    <option value="Low" {{ $ticket->priority === 'Low' ? 'selected' : '' }}>Low</option>
                                    <option value="Medium" {{ $ticket->priority === 'Medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="High" {{ $ticket->priority === 'High' ? 'selected' : '' }}>High</option>
                                    <option value="Urgent" {{ $ticket->priority === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm btn-block fw-bold">
                                <i class="fas fa-save mr-1"></i> Save Changes
                            </button>
                        </form>
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
    let compressedFilesList = [];

    $(document).ready(function() {
        // 1. Submit Reply CDC via AJAX
        $('#form-cdc-reply').on('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Sending Response...',
                text: 'Posting reply and updating ticket status...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            let formData = new FormData(this);

            compressedFilesList.forEach((fileBlob) => {
                formData.append('image_proofs[]', fileBlob, fileBlob.name);
            });

            $.ajax({
                url: "{{ router('cdc.helpdesk.reply', ['id' => \Hashid::encode($ticket->id_ticket)]) }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        // TAMPILKAN POPUP NOTIFIKASI SUCCESS
                        Swal.fire({
                            icon: 'success',
                            title: res.title || 'Response Sent!',
                            text: res.message,
                            confirmButtonColor: '#005b9f'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    console.error("Reply Error:", xhr.responseText);
                    Swal.fire('Server Error', 'An error occurred while posting your reply.', 'error');
                }
            });
        });

        // 2. Quick Update Status & Priority via AJAX
        $('#form-quick-update').on('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Updating Ticket Status...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ router('cdc.helpdesk.update_status', ['id' => \Hashid::encode($ticket->id_ticket)]) }}",
                type: "POST",
                data: $(this).serialize() + '&_token=' + _token,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        // TAMPILKAN POPUP NOTIFIKASI SUCCESS
                        Swal.fire({
                            icon: 'success',
                            title: res.title || 'Updated!',
                            text: res.message,
                            confirmButtonColor: '#005b9f'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    console.error("Update Error:", xhr.responseText);
                    Swal.fire('Server Error', 'An error occurred while updating status.', 'error');
                }
            });
        });
    });

    // 3. MULTIPLE IMAGE SELECTION & FRONTEND COMPRESSION
    async function handleMultipleImageSelection(input) {
        if (!input.files || input.files.length === 0) return;

        const allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
        const maxSizeBytes = 3 * 1024 * 1024; // 3MB
        let rawFiles = Array.from(input.files);
        let hasError = false;

        Swal.fire({
            title: 'Compressing Images...',
            text: `Processing ${rawFiles.length} file(s) in browser...`,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });

        for (let file of rawFiles) {
            // Validasi MIME Type Asli
            if (!allowedMimeTypes.includes(file.type)) {
                hasError = true;
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Format Skipped',
                    text: `File "${file.name}" is not a valid image (JPG, PNG, WEBP).`,
                    confirmButtonColor: '#005b9f'
                });
                continue;
            }

            // Validasi Size Max 3MB
            if (file.size > maxSizeBytes) {
                hasError = true;
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: `File "${file.name}" exceeds the maximum limit of 3MB.`,
                    confirmButtonColor: '#005b9f'
                });
                continue;
            }

            try {
                let compressedBlob = await compressSingleImage(file);
                compressedBlob.originalName = file.name;
                compressedBlob.originalSize = file.size;

                compressedFilesList.push(compressedBlob);
            } catch (err) {
                console.error("Error compressing file:", file.name, err);
                hasError = true;
                Swal.fire({
                    icon: 'error',
                    title: 'Corrupted or Fake Image',
                    text: `Could not process file "${file.name}". Please ensure the file is a real image.`,
                    confirmButtonColor: '#005b9f'
                });
            }
        }

        if (!hasError) {
            Swal.close();
        }

        $('#input_file_gallery').val('');
        renderPreviewGrid();
    }

    // PROMISE CANVAS COMPRESSION
    function compressSingleImage(file) {
        return new Promise((resolve, reject) => {
            let reader = new FileReader();
            reader.onload = function(e) {
                let img = new Image();
                img.src = e.target.result;
                img.onerror = () => reject(new Error("Corrupted Image"));

                img.onload = function() {
                    let maxWidth = 1200;
                    let width = img.width;
                    let height = img.height;

                    if (width > maxWidth) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    }

                    let canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    let ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob((blob) => {
                        if (blob) {
                            blob.name = file.name;
                            resolve(blob);
                        } else {
                            reject(new Error("Canvas export failed"));
                        }
                    }, 'image/jpeg', 0.70);
                };
            };
            reader.onerror = () => reject(new Error("FileReader error"));
            reader.readAsDataURL(file);
        });
    }

    // RENDER PREVIEW CARDS
    function renderPreviewGrid() {
        let gridContainer = $('#attachment-grid');
        gridContainer.empty();

        compressedFilesList.forEach((fileItem, index) => {
            let imgUrl = URL.createObjectURL(fileItem);
            let origSize = formatBytes(fileItem.originalSize);
            let compSize = formatBytes(fileItem.size);

            let cardHtml = `
            <div class="preview-card-item">
                <div>
                    <div class="preview-image-box">
                        <img src="${imgUrl}" alt="Attachment">
                    </div>
                    <div class="file-name-truncated" title="${fileItem.originalName}">${fileItem.originalName}</div>
                    <div class="mt-2 mb-2">
                        <span class="badge badge-secondary badge-size">${origSize}</span>
                        <i class="fas fa-arrow-right text-muted mx-1" style="font-size: 8px;"></i>
                        <span class="badge badge-success badge-size">${compSize}</span>
                    </div>
                </div>
                <div class="text-right border-top pt-2 mt-1">
                    <button type="button" class="btn btn-xs btn-outline-danger btn-block" onclick="removeSingleImage(${index})">
                        <i class="fas fa-trash-alt mr-1"></i> Remove
                    </button>
                </div>
            </div>`;

            gridContainer.append(cardHtml);
        });
    }

    function removeSingleImage(index) {
        compressedFilesList.splice(index, 1);
        renderPreviewGrid();
    }

    function formatBytes(bytes, decimals = 1) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
</script>
@endpush