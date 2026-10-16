@extends('templates.errors', ['title' => '417 - Expectation Failed'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">417 Expectation Failed</h1>

    <p class="error-subtitle">
        The server cannot meet the requirements of the Expect header.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            Please remove the Expect header and try again.
        </p>
    @endif

</div>

@endsection
