@extends('templates.errors')

@php $need_extra = false; @endphp

@section('main-content')
    <div class="error-header">
        {{-- Judul Bilingual --}}
        <h1 class="error-title">Scheduled Maintenance</h1>
        <h5 class="error-subtitle text-primary">Sistem Sedang Dalam Pemeliharaan</h5>
    </div>

    <div class="px-3">
        {{-- Deskripsi Bilingual --}}
        <p class="mb-1 text-dark" style="font-weight: 600;">
            We are currently performing scheduled maintenance to improve our services.
        </p>
        <p class="text-muted small">
            Kami sedang melakukan pemeliharaan sistem rutin untuk meningkatkan kualitas layanan MyInternship.
        </p>

        <div class="alert alert-warning py-2 mt-4" style="border-radius: 8px; border-left: 4px solid #ffad46;">
            <small class="d-block font-weight-bold">
                <i class="fas fa-clock mr-1"></i> Estimated Completion / Estimasi Selesai:
            </small>
            <span class="badge badge-warning">{{ $end ?? 'TBA' }}</span>
        </div>

        <p class="error-desc mt-4">
            Thank you for your patience. / Terima kasih atas kesabaran Anda.
        </p>
    </div>

    <div class="separator">Restricted Access</div>

    <div class="d-flex justify-content-center">
        {{-- Tombol Khusus Admin CDC --}}
        <a href="{{ base_url('auth/login/cdc') }}" class="btn btn-dark shadow-sm py-2 px-4 d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 100%; max-width: 280px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="mr-2" viewBox="0 0 16 16">
                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
            </svg>
            Admin / CDC Login
        </a>
    </div>
@endsection

@push('head-scripts')
<style>
    /* Tambahan animasi pulsa untuk kesan sistem sedang bekerja */
    .error-logo {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }

    .badge-warning {
        background-color: #ffad46;
        color: #fff;
        font-size: 14px;
        padding: 5px 12px;
        border-radius: 50px;
    }
</style>
@endpush