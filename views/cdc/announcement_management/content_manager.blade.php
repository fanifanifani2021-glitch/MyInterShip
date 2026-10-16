@extends('templates.cdc', ['title' => 'Content Manager'])

@push('head-scripts')
<!-- CKEditor -->
<script src="./library/ckeditor/ckeditor.js"></script>
@endpush

@section('main-content')
<div class="container">
    <div class="page-inner mt-2">
        <div class="page-header">
            <h4 class="page-title">Content Manager</h4>
            <ul class="breadcrumbs">
                <li class="nav-home">
                    <a href="{{ router('landing') }}"><i class="flaticon-home"></i></a>
                </li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item">
                    <a href="{{ router('cdc.announcement.management') }}">Announcement Management</a>
                </li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="#">Content Manager</a></li>
            </ul>
        </div>

        <div class="row mt-2">
            <div class="col-md-12">
                <div class="card full-height">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">{{ $announcement ? 'Edit Announcement' : 'Create New Announcement' }}</h4>
                        </div>
                    </div>

                    <form id="form_content_manager">
                        <?= csrf(); ?>
                        <input type="hidden" name="id" value="{{ $announcement->id_announcement ?? '' }}">

                        <div class="card-body form-row">
                            {{-- Title --}}
                            <div class="form-group col-md-6">
                                <label>Title <b class="text-danger">*</b></label>
                                <input type="text" class="form-control" name="title" id="title" value="{{ $announcement->title ?? '' }}" placeholder="Enter announcement title" required>
                                <div class="invalid-feedback" id="error-title"></div>
                            </div>

                            {{-- Category --}}
                            <div class="form-group col-md-3">
                                <label>Category <b class="text-danger">*</b></label>
                                <select class="form-control" name="category" id="category">
                                    @php $cat = $announcement->category ?? 'Information'; @endphp
                                    <option value="Information" {{ $cat == 'Information' ? 'selected' : '' }}>Information</option>
                                    <option value="General" {{ $cat == 'General' ? 'selected' : '' }}>General</option>
                                    <option value="Internship" {{ $cat == 'Internship' ? 'selected' : '' }}>Internship</option>
                                    <option value="Job Offer" {{ $cat == 'Job Offer' ? 'selected' : '' }}>Job Offer</option>
                                </select>
                            </div>

                            {{-- Status --}}
                            <div class="form-group col-md-3">
                                <label>Status <b class="text-danger">*</b></label>
                                <select class="form-control" name="status" id="status">
                                    @php $st = $announcement->status ?? 'Draft'; @endphp
                                    <option value="Draft" {{ $st == 'Draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="Publish" {{ $st == 'Publish' ? 'selected' : '' }}>Publish</option>
                                </select>
                            </div>

                            {{-- Content CKEditor --}}
                            <div class="form-group col-md-12">
                                <label>Content Body <b class="text-danger">*</b></label>
                                <textarea class="form-control" name="content" id="content" placeholder="Content Manager">{{ $announcement->content ?? '' }}</textarea>
                                <div class="invalid-feedback" id="error-content"></div>
                            </div>
                        </div>

                        <div class="modal-footer justify-content-start">
                            <button onclick="cancel_confirmation('{{ router('cdc.announcement.management') }}')" type="button" class="btn btn-danger">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </button>
                            <button onclick="reset_confirmation('form_content_manager')" type="button" class="btn btn-warning">
                                <i class="fas fa-sync mr-1"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save mr-1"></i>Save Announcement
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let editorInstance;
    let _token = $('meta[name="csrf-token"]').attr('content');

    $(document).ready(function() {
        // Init CKEditor
        if (typeof CKEDITOR !== 'undefined') {
            editorInstance = CKEDITOR.replace('content', {
                language: 'en',
                extraPlugins: 'editorplaceholder',
                editorplaceholder: 'Enter announcement content here...',
                removeButtons: 'PasteFromWord,Styles,Format,Font,FontSize',
                height: '25em',
            });
        }

        // Save Form via AJAX
        $('#form_content_manager').on('submit', function(e) {
            e.preventDefault();

            // Sync CKEditor data back to textarea
            if (editorInstance) {
                editorInstance.updateElement();
            }

            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            Swal.fire({
                title: 'Saving changes...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            let formData = new FormData(this);
            formData.append('_token', _token);

            $.ajax({
                url: "{{ router('ajax.cdc.announcement.save') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ router('cdc.announcement.management') }}";
                        });
                    } else if (res.status === 'validation_error') {
                        Swal.close();
                        $.each(res.errors, function(field, message) {
                            $('#' + field).addClass('is-invalid');
                            $('#error-' + field).text(message);
                        });
                    } else {
                        Swal.fire('Failed', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Internal Server Error occurred.', 'error');
                }
            });
        });
    });

    function cancel_confirmation(location) {
        Swal.fire({
            title: "Are you sure you want to cancel?",
            text: "Changes you made will not be saved!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            confirmButtonText: "Yes, Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = location;
            }
        });
    }

    function reset_confirmation(form) {
        Swal.fire({
            title: "Are you sure you want to reset?",
            text: "Changes you made will be reset!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Reset"
        }).then((result) => {
            if (result.isConfirmed) {
                $("#" + form)[0].reset();
                if (editorInstance) {
                    editorInstance.setData('');
                }
            }
        });
    }
</script>
@endpush