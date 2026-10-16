@extends('templates.errors', ['title' => '407 - Proxy Authentication Required'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">407 Proxy Authentication Required</h1>

    <p class="error-subtitle">
        Authentication with the proxy server is required before proceeding.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            Please authenticate with your proxy before retrying the request.
        </p>
    @endif

</div>

@endsection
