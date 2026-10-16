@extends('templates.simple')

@section('title', 'Router Analytics')

@section('content')

    {{-- HEADER JUDUL --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 font-weight-bold">
            <i class="fas fa-route text-primary mr-2"></i>
            Router Analytics
        </h4>

        <span class="badge bg-info text-white p-2 shadow-sm" style="font-size: 0.9rem;">
            Total Logs: {{ $totalLogs }}
        </span>
    </div>

    {{-- CARD TABEL --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            @if(empty($groupedData))
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i><br>
                    <h5>No Error Logs Found</h5>
                    <p class="mb-0">Great! Your router seems healthy.</p>
                </div>
            @else
                
                {{-- WRAPPER TABEL SCROLLABLE --}}
                <div class="table-responsive" style="max-height: 75vh; overflow-y: auto;">
                    
                    <table class="table table-hover table-fixed-head mb-0" style="font-size: 0.9rem;">
                        <thead>
                            <tr>
                                <th class="align-middle text-center" style="width: 50px;">#</th>
                                <th class="align-middle" style="width: 110px;">Time</th>
                                <th class="align-middle text-center" style="width: 80px;">Code</th>
                                <th class="align-middle text-center" style="width: 90px;">Method</th>
                                <th class="align-middle">Route / Path</th>
                                <th class="align-middle">Referer</th>
                                <th class="align-middle" style="width: 140px;">IP Address</th>
                                <th class="align-middle">User Agent</th>
                            </tr>
                        </thead>
                        
                        {{-- START LOOP GROUPING (DI LUAR TBODY) --}}
                        @php $globalIndex = 1; @endphp

                        @foreach($groupedData as $date => $logs)
                            
                            {{-- BUAT TBODY BARU UNTUK SETIAP TANGGAL --}}
                            <tbody class="border-top-0">
                                
                                {{-- HEADER TANGGAL (STICKY) --}}
                                <tr class="sticky-date-header">
                                    {{-- Gunakan 'th' atau 'td' dengan bg-dark solid --}}
                                    <td colspan="100%" class="py-2 px-3 align-middle bg-dark text-white border-0">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <i class="far fa-calendar-alt mr-2 text-warning"></i> 
                                                <span class="font-weight-bold" style="font-size: 1rem;">
                                                    {{ date('l, d F Y', strtotime($date)) }}
                                                </span>
                                            </div>
                                            <span class="badge bg-light text-dark shadow-sm">
                                                {{ count($logs) }} Events
                                            </span>
                                        </div>
                                    </td>
                                </tr>

                                {{-- ISI LOGS PER TANGGAL --}}
                                @foreach($logs as $log)
                                    <tr style="border-bottom: 1px solid #f0f0f0;">
                                        <td class="text-muted align-middle text-center">{{ $globalIndex++ }}</td>

                                        <td class="align-middle">
                                            <span class="font-weight-bold text-dark font-monospace">
                                                {{ date('H:i:s', strtotime($log['timestamp'] ?? 'now')) }}
                                            </span>
                                        </td>

                                        <td class="align-middle text-center">
                                            @php 
                                                $code = $log['code'] ?? 500; 
                                                // FIX: Gunakan class 'bg-*' sesuai parent layout
                                                $badgeClass = 'bg-secondary text-white';
                                                
                                                if ($code >= 500) $badgeClass = 'bg-danger text-white'; 
                                                elseif ($code == 404) $badgeClass = 'bg-warning text-white'; 
                                                elseif ($code == 403) $badgeClass = 'bg-dark text-white'; 
                                                elseif ($code == 419) $badgeClass = 'bg-info text-white'; 
                                            @endphp
                                            <span class="badge {{ $badgeClass }} p-2 shadow-sm" style="min-width: 50px;">
                                                {{ $code }}
                                            </span>
                                        </td>

                                        <td class="align-middle text-center">
                                            @php 
                                                $method = strtoupper($log['method'] ?? '-'); 
                                                // FIX: Gunakan class 'bg-*' sesuai parent layout
                                                $methodClass = 'bg-secondary text-white';
                                                
                                                if ($method === 'GET') $methodClass = 'bg-primary text-white'; 
                                                if ($method === 'POST') $methodClass = 'bg-success text-white'; 
                                                if ($method === 'PUT' || $method === 'PATCH') $methodClass = 'bg-warning text-white'; 
                                                if ($method === 'DELETE') $methodClass = 'bg-danger text-white'; 
                                            @endphp
                                            <span class="badge {{ $methodClass }} w-100 py-1 shadow-sm">
                                                {{ $method }}
                                            </span>
                                        </td>

                                        <td class="align-middle">
                                            <code class="text-danger font-weight-bold small" style="word-break: break-all;">
                                                {{ $log['route'] ?? $log['message'] ?? '-' }}
                                            </code>
                                        </td>

                                        <td class="align-middle">
                                            <code class="text-danger font-weight-bold small" style="word-break: break-all;">
                                                {{ $log['referer'] ?? '-' }}
                                            </code>
                                        </td>

                                        <td class="align-middle">
                                            <small class="text-monospace font-weight-bold text-secondary bg-light px-2 py-1 rounded border">
                                                {{ $log['ip'] ?? '-' }}
                                            </small>
                                        </td>

                                        <td class="align-middle">
                                            <div style="max-width: 250px; line-height: 1.2;">
                                                <small class="text-muted d-block text-truncate" title="{{ $log['user_agent'] ?? '-' }}">
                                                    {{ $log['user_agent'] ?? '-' }}
                                                </small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            {{-- END TBODY PER TANGGAL --}}

                        @endforeach

                    </table>
                </div>

            @endif

        </div>
    </div>

@endsection