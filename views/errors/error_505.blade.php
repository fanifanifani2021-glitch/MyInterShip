@extends('templates.errors', ['title' => $title ?? '505 - HTTP Version Not Supported'])

@section('main-content')

<div class="error-header">

    <img class="error-logo" src="{{ base_url('assets/jobcy/img/logo.png') }}" alt="MyInternship">

    <h1 class="error-title">{{ $code }} — HTTP Version Not Supported</h1>

    @if ($message)
        <p class="error-desc">
            {{ $message }}
        </p>
    @else
        <p class="error-desc">
            The server does not support the HTTP protocol version used in this request.
        </p>
    @endif

    <p class="error-subtitle">Please try again using a modern browser or valid request method.</p>

</div>

@endsection
