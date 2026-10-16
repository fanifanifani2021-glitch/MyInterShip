@extends('templates.errors', ['title' => '415 - Unsupported Media Type'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">415 Unsupported Media Type</h1>

    <p class="error-subtitle">
        The server does not support the media type sent in the request.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            Please use a supported content type and try again.
        </p>
    @endif

</div>

@endsection
