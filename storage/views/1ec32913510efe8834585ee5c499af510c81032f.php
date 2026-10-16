

<?php $__env->startPush('head-scripts'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<?php $__env->stopPush(); ?>
<?php $__env->startSection('main-content'); ?>

<div class="container">

    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Attendance Location</h2>
                    <h5 class="text-white op-7 mb-2">
                        Configure allowed attendance locations for active internships
                    </h5>

                </div>

                <div class="ml-md-auto py-2 py-md-0">
                    <button class="btn btn-primary btn-round"
                        data-toggle="modal"
                        data-target="#modalAddLocation">
                        <i class="fas fa-plus mr-1"></i> Add Location
                    </button>
				</div>
            </div>
        </div>
    </div>

    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">

                <div class="card full-height">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Attendance Location</h4>

                        <div class="btn-group">
                            <a href="<?php echo e(router('manual/industry.attendance_location')); ?>"
                            class="btn btn-info btn-sm">
                                <i class="fas fa-info-circle mr-1"></i> Manual
                            </a>

                            <a href="<?php echo e(router('industry/attendance_location.recycle')); ?>"
                            class="btn btn-warning btn-sm">
                                <i class="fas fa-recycle mr-1"></i> Recycle
                            </a>

                        </div>
                    </div>


                    <div class="card-body">
                        
                        <table id="basic-datatables" class="table table-striped table-hover w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Location</th>
                                    <th>Latitude</th>

                                    <th>Longitude</th>
                                    <th>Attendance Radius</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>


    <div class="modal fade" id="modalAddLocation" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="formAddLocation">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Add Attendance Location</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" name="id_location" id="id_location">
                        <input type="hidden" name="mode" id="form_mode" value="add">

                        <div class="form-group">
                            <label>Location Name</label>
                            <input type="text" name="location" id="location" class="form-control" required placeholder="Eg: Tower A - Politeknik Negeri Batam">
                        </div>
                        <div class="form-group mt-3 d-flex justify-content-between">
                            <small class="text-muted">
                                Click on map to select location or drag marker
                            </small>
                            <button type="button" class="btn btn-sm btn-info" id="btnCurrentLocation">
                                <i class="fas fa-crosshairs mr-1"></i> Use Current Location
                            </button>
                        </div>
                        <div class="form-group">

                        <div id="map" style="height: 400px;" class="mt-2 rounded border"></div>
                        </div>


                        <div class="form-group form-row">
                            <div class="col-md-4">
                                <label>Latitude</label>
                                <input type="text" name="latitude" id="latitude" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Longitude</label>
                                <input type="text" name="longitude" id="longitude" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Radius (meters)</label>
                                <input type="number" name="radius_meters" class="form-control" value="100" min="10" step="1" required>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="reset" class="btn btn-warning btn-sm">Reset</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            Save Location
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>


    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('footer-scripts'); ?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    let map, marker, circle;
    const defaultRadius = 100;
    let mapInitialized = false;

    function initMap() {

        if (mapInitialized) return;
        mapInitialized = true;

        const batam = [1.0456, 104.0305];

        map = L.map('map', {
            minZoom: 12
        }).setView(batam, 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        marker = L.marker(batam, { draggable: true }).addTo(map);

        updateLatLng(batam[0], batam[1], defaultRadius);

        // Klik map
        map.on('click', function (e) {
            setMarker(e.latlng.lat, e.latlng.lng);
        });

        // Drag marker
        marker.on('dragend', function (e) {
            const pos = e.target.getLatLng();
            updateLatLng(pos.lat, pos.lng);
        });
    }

    function setMarker(lat, lng) {
        marker.setLatLng([lat, lng]);
        map.panTo([lat, lng]);

        const radius = getRadius();
        updateLatLng(lat, lng, radius);
    }

    function updateLatLng(lat, lng, radius) {
        $('#latitude').val(lat.toFixed(7));
        $('#longitude').val(lng.toFixed(7));

        drawRadius(lat, lng, radius);
    }

    function getRadius() {
        const val = parseInt($('[name="radius_meters"]').val());
        return isNaN(val) ? defaultRadius : val;
    }

    // Draw / update circle
    function drawRadius(lat, lng, radius) {

        if (circle) {
            circle.setLatLng([lat, lng]);
            circle.setRadius(radius);
        } else {
            circle = L.circle([lat, lng], {
                radius: radius,
                color: 'blue',
                fillOpacity: 0.2
            }).addTo(map);
        }

        circle.bindTooltip(radius + ' meters');
    }

    // Current Location Button Handler
    $('#btnCurrentLocation').on('click', function () {
        
        let $btn = $(this);
        // Store original button state
        let defaultText = '<i class="fas fa-crosshairs mr-1"></i> Use Current Location'; 

        // 1. Change Button State to "Loading"
        $btn.html('<i class="fas fa-spin fa-spinner mr-1"></i> Locating...').prop('disabled', true);

        // Check Browser Support
        if (!navigator.geolocation) {
            Swal.fire('Error', 'Geolocation is not supported by this browser.', 'error');
            $btn.html(defaultText).prop('disabled', false);
            return;
        }

        // Request Location
        navigator.geolocation.getCurrentPosition(
            // === SUCCESS CALLBACK ===
            function (pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                const accuracy = pos.coords.accuracy;

                // 2. Update Map & Marker (Center the map)
                // Assuming setMarker() updates a global variable (e.g., 'currentMarker')
                setMarker(lat, lng); 
                map.setView([lat, lng], 18); // Zoom in close for better visibility

                // 3. UX Improvement: Show Popup on Marker
                // This confirms to the user exactly where the point is on the map
                if (typeof currentMarker !== 'undefined') {
                    currentMarker.bindPopup(`
                        <div class='text-center'>
                            <b>Location Found!</b><br>
                            Accuracy: ${Math.round(accuracy)} meters
                        </div>
                    `).openPopup();
                }

                // 4. UX Improvement: Button Feedback (Green/Success)
                $btn.removeClass('btn-info').addClass('btn-success')
                    .html('<i class="fas fa-check mr-1"></i> Success!');

                // Revert button to original state after 2 seconds
                setTimeout(function() {
                    $btn.removeClass('btn-success').addClass('btn-info')
                        .html(defaultText).prop('disabled', false);
                }, 2000);
            },
            // === ERROR CALLBACK ===
            function (error) {
                let msg = 'Failed to retrieve location.';
                if (error.code === 1) msg = 'Location permission denied. Please enable GPS.';
                if (error.code === 2) msg = 'Location information is unavailable.';
                if (error.code === 3) msg = 'The request to get user location timed out.';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: msg
                });
                
                // Reset button state immediately on error
                $btn.html(defaultText).prop('disabled', false);
            },
            // === OPTIONS ===
            { 
                enableHighAccuracy: true, 
                timeout: 10000, 
                maximumAge: 0 
            }
        );
    });

    // Radius input realtime
    $('input[name="radius_meters"]').on('input', function () {
        const lat = parseFloat($('#latitude').val());
        const lng = parseFloat($('#longitude').val());
        const radius = getRadius();

        if (!isNaN(lat) && !isNaN(lng)) {
            drawRadius(lat, lng, radius);
        }
    });

    // Init map saat modal dibuka
    $('#modalAddLocation').on('shown.bs.modal', function () {
        setTimeout(initMap, 200);
    });
</script>


<script>
    let _token = $('meta[name="csrf-token"]').attr('content');

    // =========================================================================
    // HELPER: GLOBAL ERROR HANDLER (Standardized)
    // =========================================================================
    function handleAjaxError(xhr, defaultMessage = "Something went wrong") {
        Swal.close(); // Tutup loading jika ada

        if (xhr.status === 429) {
            Swal.fire({
                title: 'Too Many Requests',
                text: 'You are performing actions too quickly. Please wait a moment.',
                icon: 'warning',
                timer: 4000,
                timerProgressBar: true
            });
        } else {
            let msg = defaultMessage;
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            Swal.fire({
                title: 'Error',
                text: msg,
                icon: 'error'
            });
        }
    }

    $(document).ready(function () {

        // =========================================================================
        // 1. DATATABLE CONFIGURATION
        // =========================================================================
        let table = $('#basic-datatables').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            searching: true,
            pageLength: 15,
            lengthMenu: [10, 15, 25, 50],
            ajax: {
                url: "<?php echo e(router('datatable.industry.attendance_location')); ?>",
                type: 'POST',
                // [NEW] Tambah Header Token
                headers: { 'X-CSRF-TOKEN': _token },
                data: function (d) {
                    d._token = _token; // Fallback body param
                },
                // [NEW] Handle Error Throttle Datatable
                error: function (xhr, error, code) {
                    handleAjaxError(xhr, "Failed to load data table");
                    $('#basic-datatables_processing').hide();
                }
            },
            columns: [
                { data: 'no', orderable: false, searchable: false },
                { data: 'location' },
                { data: 'latitude' },
                { data: 'longitude' },
                { data: 'radius_meters' },
                { data: 'action', orderable: false, searchable: false }
            ]
        });


        // =========================================================================
        // 2. EDIT BUTTON CLICK
        // =========================================================================
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');

            $.ajax({
                url: "<?php echo e(router('ajax.industry.attendance_location.detail')); ?>",
                type: "POST",
                dataType: "json",
                // [NEW] Tambah Header Token
                headers: { 'X-CSRF-TOKEN': _token },
                data: {
                    id_location: id,
                    _token: _token
                },
                beforeSend: function () {
                    Swal.fire({
                        title: 'Loading...',
                        text: 'Fetching attendance location data',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function (res) {
                    if (!res || res.status !== true) {
                        Swal.fire('Failed', res?.message || 'Failed to retrieve data', 'error');
                        return;
                    }

                    const d = res.data;

                    // Set Form Mode & ID
                    $('#form_mode').val('edit');
                    $('#id_location').val(d.id_location);

                    // Fill Form
                    $('#location').val(d.location);
                    $('#latitude').val(d.latitude);
                    $('#longitude').val(d.longitude);
                    $('#radius_meters').val(d.radius_meters);

                    // Open Modal
                    $('#modalAddLocation').modal('show');
                    $('#modalAddLocation .modal-title').text('Edit Attendance Location');

                    // Update Map View
                    $('#modalAddLocation').one('shown.bs.modal', function () {
                        setTimeout(function () {
                            // Asumsi fungsi global setMarker & drawRadius tersedia
                            if(typeof setMarker === 'function') {
                                setMarker(parseFloat(d.latitude), parseFloat(d.longitude));
                            }
                            if(typeof drawRadius === 'function') {
                                drawRadius(parseFloat(d.latitude), parseFloat(d.longitude), parseInt(d.radius_meters));
                            }
                            if(typeof map !== 'undefined') {
                                map.setView([d.latitude, d.longitude], 16);
                            }
                        }, 200);
                    });

                    Swal.close();
                },
                error: function (xhr) {
                    handleAjaxError(xhr, 'Failed to fetch location detail');
                }
            });
        });


        // =========================================================================
        // 3. FORM SUBMIT (ADD/UPDATE)
        // =========================================================================
        $('#formAddLocation').on('submit', function (e) {
            e.preventDefault();

            let url = '';
            const mode = $('#form_mode').val();

            if (mode === 'add') {
                url = "<?php echo e(router('ajax.industry.attendance_location.submit')); ?>";
            } else {
                url = "<?php echo e(router('ajax.industry.attendance_location.update')); ?>";
            }

            const $btn = $('#formAddLocation button[type=submit]');
            $btn.prop('disabled', true).text('Saving...');

            // Ambil FormData dan tambahkan _token jika belum ada di form
            let formData = $(this).serializeArray();
            formData.push({name: "_token", value: _token});

            $.ajax({
                url: url,
                type: 'POST',
                // [NEW] Tambah Header Token
                headers: { 'X-CSRF-TOKEN': _token },
                data: $.param(formData),
                dataType: 'json',
                beforeSend: function () {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Saving attendance location',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function (res) {
                    if (res.status === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false,
                            timerProgressBar: true
                        }).then(() => {
                            $('#modalAddLocation').modal('hide');
                            $('#formAddLocation')[0].reset();
                            $('#form_mode').val('add');
                            $('#id_location').val('');
                            // Reload table without resetting paging
                            table.ajax.reload(null, false);
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Failed',
                            text: res.message
                        });
                    }
                },
                error: function (xhr) {
                    handleAjaxError(xhr, 'Failed to save location');
                },
                complete: function () {
                    $btn.prop('disabled', false).text('Save Location');
                }
            });
        });


        // =========================================================================
        // 4. DELETE BUTTON CLICK
        // =========================================================================
        $(document).on('click', '.btn-delete', function () {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: 'This attendance location can be restored in recycle menu',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo e(router('ajax.industry.attendance_location.destroy')); ?>",
                        type: 'POST',
                        dataType: 'json',
                        // [NEW] Tambah Header Token
                        headers: { 'X-CSRF-TOKEN': _token },
                        data: {
                            _method: 'DELETE',
                            id_location: id,
                            _token: _token
                        },
                        beforeSend: function () {
                            Swal.fire({
                                title: 'Deleting...',
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });
                        },
                        success: function (res) {
                            if (res.status === true) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Failed',
                                    text: res.message
                                });
                            }
                        },
                        error: function (xhr) {
                            handleAjaxError(xhr, 'Failed to delete location');
                        }
                    });
                }
            });
        });

    });
</script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('templates.industry', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp_new\htdocs\pblKita\views/industry/attendance_location/index.blade.php ENDPATH**/ ?>