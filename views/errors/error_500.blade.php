@extends('templates.errors', ['title' => '500 - Internal Server Error'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">500 Internal Server Error</h1>

    <p class="error-subtitle">
        Something went wrong on our server.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            An unexpected error occurred. Please try again later or contact support if the issue persists.
        </p>
    @endif

</div>

@endsection
