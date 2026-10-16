@extends('templates.errors', ['title' => '502 - Bad Gateway'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">502 Bad Gateway</h1>

    <p class="error-subtitle">
        The server received an invalid response from an upstream service.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            This can happen due to network or server issues. Please try again shortly.
        </p>
    @endif

</div>

@endsection
