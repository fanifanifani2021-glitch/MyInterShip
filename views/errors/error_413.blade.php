@extends('templates.errors', ['title' => '413 - Payload Too Large'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">413 Payload Too Large</h1>

    <p class="error-subtitle">
        The data sent in the request is too large for the server to process.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            Try reducing the size of your upload or request data.
        </p>
    @endif

</div>

@endsection
