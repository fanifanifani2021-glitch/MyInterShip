@extends('templates.student')

@push('head-scripts')
{{-- Library Map hanya dimuat jika ada aturan lokasi dari database --}}
@if($has_location_rule)
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endif

<style>
    .page-inner { background: #f8f9fa; min-height: 100vh; padding: 30px 20px !important; }
    .card { border-radius: 8px !important; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    
    /* Styling Peta */
    #attendanceMap { 
        height: 450px; width: 100%; border-radius: 8px; 
        border: 1px solid #e2e8f0; background: #f1f5f9; z-index: 1; 
    }
    
    .time-banner { 
        background: white; padding: 15px 20px; border-radius: 8px; border-left: 5px solid #1572e8; 
        margin-bottom: 20px; border: 1px solid #e2e8f0; border-left-width: 5px;
    }
    
    /* List Lokasi */
    .location-item { padding: 12px; border-radius: 6px; border: 1px solid #edf2f7; margin-bottom: 8px; background: white; transition: 0.3s; }
    .location-item.active { border-color: #28a745; background: #f0fff4; box-shadow: 0 2px 8px rgba(40, 167, 69, 0.1); }
    
    .status-badge { padding: 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 10px; margin-top: 15px; }
    .status-searching { background: #ebf8ff; color: #3182ce; border: 1px solid #bee3f8; }
    .status-success { background: #f0fff4; color: #38a169; border: 1px solid #c6f6d5; }
    .status-failed { background: #fff5f5; color: #e53e3e; border: 1px solid #fed7d7; }


    /* Container Banner Utama */
.time-banner { 
    background: #ffffff; 
    border-radius: 12px; 
    border: 1px solid #e2e8f0; 
    overflow: hidden;
    margin-bottom: 25px;
}

.time-card { padding: 20px; transition: 0.3s; }
.time-card.server-side { border-left: 5px solid #1572e8; background: linear-gradient(90deg, #f0f7ff 0%, #ffffff 100%); }
.time-card.device-side { border-left: 5px solid #6861ce; background: linear-gradient(90deg, #f5f3ff 0%, #ffffff 100%); }

/* Typography Modern */
.label-caps { 
    font-size: 0.65rem; 
    font-weight: 800; 
    text-transform: uppercase; 
    letter-spacing: 0.05em; 
    color: #718096; 
    margin-bottom: 4px;
    display: block;
}

.time-main { 
    font-size: 2.2rem; 
    font-weight: 800; 
    line-height: 1; 
    letter-spacing: -0.02em;
    color: #1a202c;
    font-family: 'JetBrains Mono', 'Monaco', monospace; /* Font angka agar tidak goyang */
}

.timezone-text { font-size: 0.75rem; font-weight: 600; color: #a0aec0; }
.date-sub { font-size: 0.85rem; color: #4a5568; margin-top: 5px; font-weight: 500; }

/* Vertikal Divider di Tengah */
@media (min-width: 768px) {
    .divider-center { border-right: 1px solid #edf2f7; }
}
</style>
@endpush

@section('main-content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Check In Attendance</h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="index.php"><i class="flaticon-home text-primary"></i></a></li>
                
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student.internship') }}">My Internship</a></li>
                
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student.internship.detail', ['id' => $id_enc]) }}">Detail</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><a href="{{ router('student.attendance.index', ['id' => $id_enc]) }}">Attendance</a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item"><b>Check In</b></li>
            </ul>
        </div>

        <div class="time-banner shadow-sm">
            <div class="row no-gutters">
                <div class="col-md-6 divider-center">
                    <div class="time-card server-side">
                        <span class="label-caps"><i class="fas fa-server mr-1"></i> Official Server Time</span>
                        <div class="d-flex align-items-baseline">
                            <span class="time-main text-primary" id="live-server-clock">--:--:--</span>
                            <span class="timezone-text ml-2">({{ date_default_timezone_get() }})</span>
                        </div>
                        <div class="date-sub">
                            <i class="far fa-calendar-alt mr-1"></i> <span id="server-date">{{ date('D, d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="time-card device-side">
                        <span class="label-caps"><i class="fas fa-laptop mr-1"></i> Your Local Device</span>
                        <div class="d-flex align-items-baseline">
                            <span class="time-main text-secondary" id="live-device-clock">--:--:--</span>
                            <span class="timezone-text ml-2" id="device-timezone">(Detecting...)</span>
                        </div>
                        <div class="date-sub">
                            <i class="far fa-clock mr-1"></i> <span id="device-date">--</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action dikosongkan sesuai permintaan --}}
        <form action="{{ router('student.attendance.checkIn-store', ['id' => $id_enc]) }}" id="form_attendance" method="POST">
            <?= csrf(); ?>
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="accuracy" id="accuracy">

            <div class="row">
                {{-- KIRI: FORM --}}
                <div class="{{ $has_location_rule ? 'col-lg-12' : 'col-lg-12' }}" id="form-column">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="form-group mb-4">
                                <label class="fw-bold">Presence Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="attendance_type" id="attendance_type" required>
                                    <option value="" hidden>- Select Presence Type -</option>
                                    <option value="Present">Present (Working Day)</option>
                                    <option value="National Holiday">National Holiday</option>
                                    <option value="Abstaint">Abstaint (Alpha)</option>
                                    <option value="Permit">Permit (Izin)</option>
                                    <option value="Sick Leave">Sick Leave</option>
                                </select>
                            </div>

                            {{-- Panel Lokasi: Hanya muncul jika Present & has_location_rule --}}
                            @if($has_location_rule)
                            <div id="location-list-panel" class="d-none form-group ">
                                <label class="fw-bold mb-2">Office Locations</label>
                                @foreach($attendance_locations as $loc)
                                <div class="location-item shadow-none" id="loc-card-{{ $loop->index }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;">{{ $loc['location'] }}</h6>
                                            <small class="text-muted">Radius: {{ $loc['radius_meters'] }}m</small>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-muted d-block" style="font-size: 0.65rem; font-weight: 800;">DISTANCE</span>
                                            <span class="fw-bold text-dark" id="dist-val-{{ $loop->index }}">-- m</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <button type="submit" id="submit_btn" class="btn btn-success btn-block fw-bold py-2" disabled>
                        <i class="fas fa-sign-in-alt mr-2"></i>SUBMIT ATTENDANCE
                    </button>
                </div>

                {{-- KANAN: MAP (Hanya muncul jika Present & has_location_rule) --}}
                @if($has_location_rule)
                <div class="col-lg-7 d-none" id="map-column">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div id="attendanceMap"></div>

                            <div id="loc-status" class="status-badge status-searching d-none">
                                <div class="spinner-border spinner-border-sm"></div>
                                <span>Detecting your position...</span>
                            </div>

                            <div id="loc-success" class="status-badge status-success d-none">
                                <i class="fas fa-check-circle fa-lg"></i>
                                <span>Verified! You are inside the authorized zone.</span>
                            </div>

                            <div id="loc-error" class="status-badge status-failed d-none">
                                <i class="fas fa-times-circle fa-lg"></i>
                                <span id="loc-error-msg">Please stand within the allowed radius.</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@push('footer-scripts')
<script src='./core/component/soloalert.js'></script>

<script>
    // Inisialisasi Timestamp dari Server (PHP)
    let serverMillis = {{ time() * 1000 }};

    function updateLiveClocks() {
        // 1. Logika Waktu Server
        serverMillis += 1000;
        const sDate = new Date(serverMillis);
        const sTimeStr = sDate.toTimeString().split(' ')[0];
        $('#live-server-clock').text(sTimeStr);

        // 2. Logika Waktu Perangkat
        const dDate = new Date();
        const dTimeStr = dDate.toTimeString().split(' ')[0];
        $('#live-device-clock').text(dTimeStr);
        
        // Update Tanggal Perangkat
        const dateOptions = { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' };
        $('#device-date').text(dDate.toLocaleDateString('en-GB', dateOptions));

        // Deteksi Timezone Perangkat
        const dTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        $('#device-timezone').text(`(${dTimezone})`);

        // 3. Validasi Selisih (Keamanan)
        // Jika selisih > 1 menit, berikan tanda merah pada waktu perangkat
        const diff = Math.abs(serverMillis - dDate.getTime());
        if (diff > 60000) { 
            $('#live-device-clock').addClass('text-danger').removeClass('text-secondary');
        } else {
            $('#live-device-clock').addClass('text-secondary').removeClass('text-danger');
        }
    }

    setInterval(updateLiveClocks, 1000);
    updateLiveClocks(); // Run immediately
</script>

@if($has_location_rule)
<script>
    const ATT_LOCATIONS = <?= json_encode($attendance_locations) ?>;
    let map = null, userMarker = null;

    function initLeafletMap(lat, lng) {
        if (map) return;
        map = L.map('attendanceMap').setView([lat, lng], 17);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        ATT_LOCATIONS.forEach(loc => {
            L.circle([parseFloat(loc.latitude), parseFloat(loc.longitude)], {
                radius: parseInt(loc.radius_meters),
                color: '#28a745', fillOpacity: 0.15, weight: 1
            }).addTo(map);
        });
    }

    function startDetection() {
        // 1. Tampilkan Loader & Kunci Tombol
        $('#loc-status').removeClass('d-none').html(`
            <div class="spinner-border spinner-border-sm text-primary"></div>
            <span>Detecting your location, please wait...</span>
        `);
        
        $('#loc-success, #loc-error').addClass('d-none');
        $('#submit_btn').prop('disabled', true).html(`
            <i class="fas fa-spinner fa-spin mr-2"></i>Verifying Location...
        `);

        // Cek dukungan Geolocation
        if (!navigator.geolocation) {
            Swal.fire('Error', 'Geolocation is not supported by your browser', 'error');
            resetDetectionUI();
            return;
        }

        // 2. Mulai Request GPS
        navigator.geolocation.getCurrentPosition(pos => {
            const { latitude, longitude, accuracy } = pos.coords;
            $('#latitude').val(latitude); 
            $('#longitude').val(longitude); 
            $('#accuracy').val(accuracy);

            initLeafletMap(latitude, longitude);
            
            if (userMarker) userMarker.setLatLng([latitude, longitude]);
            else userMarker = L.marker([latitude, longitude]).addTo(map).bindPopup("Your Position").openPopup();
            
            map.flyTo([latitude, longitude], 18);
            
            // Validasi Jarak & Update UI Sukses/Gagal
            validateArea(latitude, longitude, accuracy);
            
            // Kembalikan Teks Tombol
            $('#submit_btn').html('<i class="fas fa-sign-in-alt mr-2"></i>SUBMIT ATTENDANCE');

        }, err => {
            // Tangani Error & Sembunyikan Loader
            $('#loc-status').addClass('d-none');
            $('#loc-error').removeClass('d-none');
            $('#loc-error-msg').text("Location Access Denied. Please enable GPS permissions.");
            
            $('#submit_btn').prop('disabled', true).html('<i class="fas fa-sign-in-alt mr-2"></i>SUBMIT ATTENDANCE');
            
            Swal.fire('Location Required', 'Please enable GPS and refresh to check in.', 'warning');
        }, { 
            enableHighAccuracy: true, 
            timeout: 15000 // Berikan waktu 15 detik sebelum timeout
        });
    }

    function resetDetectionUI() {
        $('#loc-status').addClass('d-none');
        $('#submit_btn').prop('disabled', true).html('<i class="fas fa-sign-in-alt mr-2"></i>SUBMIT ATTENDANCE');
    }
    function validateArea(uLat, uLng, accuracy) {
        let isInside = false;
        ATT_LOCATIONS.forEach((loc, index) => {
            const dist = getDistance(uLat, uLng, parseFloat(loc.latitude), parseFloat(loc.longitude));
            $(`#dist-val-${index}`).text(Math.round(dist) + " m");
            if (dist <= parseInt(loc.radius_meters)) {
                isInside = true; $(`#loc-card-${index}`).addClass('active');
            } else {
                $(`#loc-card-${index}`).removeClass('active');
            }
        });

        $('#loc-status').addClass('d-none');
        if (isInside && accuracy <= 100) {
            $('#loc-success').removeClass('d-none'); $('#submit_btn').prop('disabled', false);
        } else {
            $('#loc-error').removeClass('d-none');
            $('#loc-error-msg').text(accuracy > 100 ? "Low accuracy. Stay in open area." : "Outside authorized area.");
        }
    }

    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180, dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) * Math.sin(dLon/2) * Math.sin(dLon/2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }
</script>
@endif

<script>
    setInterval(() => { $('#live-clock').text(new Date().toLocaleTimeString('en-GB')); }, 1000);

    $('#attendance_type').on('change', function() {
        const val = $(this).val();
        
        // Sembunyikan/Tampilkan Peta secara dinamis
        if (val === 'Present' && <?= $has_location_rule ? 'true' : 'false' ?>) {
            $('#map-column, #location-list-panel').removeClass('d-none');
            $('#form-column').removeClass('col-lg-12').addClass('col-lg-5');
            startDetection();
        } else {
            $('#map-column, #location-list-panel').addClass('d-none');
            $('#form-column').removeClass('col-lg-5').addClass('col-lg-12');
            $('#submit_btn').prop('disabled', val === "");
        }
    });
</script>
@endpush