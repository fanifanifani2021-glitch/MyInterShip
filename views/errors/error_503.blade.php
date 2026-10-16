@extends('templates.errors', ['title' => '503 - Service Unavailable'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">503 Service Unavailable</h1>

    <p class="error-subtitle">
        The server is currently unavailable or under maintenance.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            Please try again later. This service might be temporarily offline or overloaded.
        </p>
    @endif

</div>

@endsection
