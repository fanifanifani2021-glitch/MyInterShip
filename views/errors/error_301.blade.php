@extends('templates.errors_redirection', ['title' => 'Page Moved'])

@section('icon')
    <i class="fas fa-exchange-alt"></i>
@endsection

@section('heading', 'Page Moved Permanently')

@section('message')
    The page you are looking for has been moved to a new location due to system updates.
@endsection

@section('action_area')
    <div class="info-box">
        Redirecting you automatically in <span id="timer" class="countdown">{{ $seconds ?? 5 }}</span> seconds to:<br>
        <a href="{{ $newUrl }}" class="font-weight-bold text-primary mt-2 d-block" style="word-break: break-all;">
            {{ $newUrl }}
        </a>
    </div>

    <a href="{{ $newUrl }}" class="btn btn-primary">
        <i class="fas fa-arrow-right mr-2"></i> Go There Now
    </a>
@endsection

@push('footer-scripts')
<script>
    let seconds = {{ $seconds ?? 5 }};
    const timerElement = document.getElementById('timer');
    const newUrl = "{{ $newUrl }}";

    const countdown = setInterval(function() {
        seconds--;
        if(timerElement) timerElement.textContent = seconds;
        
        if (seconds <= 0) {
            clearInterval(countdown);
            window.location.href = newUrl;
        }
    }, 1000);
</script>
@endpush