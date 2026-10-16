@extends('templates.errors', ['title' => '411 - Length Required'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">411 Length Required</h1>

    <p class="error-subtitle">
        The request did not specify the required Content-Length header.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            Please include a valid Content-Length header and try again.
        </p>
    @endif

</div>

@endsection
