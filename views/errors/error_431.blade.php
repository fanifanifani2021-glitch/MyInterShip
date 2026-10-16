@extends('templates.errors', ['title' => '431 - Request Header Fields Too Large'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">431 Request Header Fields Too Large</h1>

    <p class="error-subtitle">
        One or more request headers are too large for the server to process.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            Reduce the size of your HTTP headers and try again.
        </p>
    @endif

</div>

@endsection
