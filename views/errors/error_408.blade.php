@extends('templates.errors', ['title' => '408 - Request Timeout'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">408 Request Timeout</h1>

    <p class="error-subtitle">
        The server timed out waiting for your request.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            Your request took too long. Please try again.
        </p>
    @endif

</div>

@endsection
