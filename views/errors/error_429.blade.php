@extends('templates.errors', ['title' => '429 - Too Many Requests'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">429 Too Many Requests</h1>

    <p class="error-subtitle">
        You have sent too many requests in a short period of time.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            Please slow down and try again later. This is a rate-limit protection to keep the system stable.
        </p>
    @endif

</div>

@endsection
