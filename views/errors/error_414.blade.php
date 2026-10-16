@extends('templates.errors', ['title' => '414 - URI Too Long'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">414 URI Too Long</h1>

    <p class="error-subtitle">
        The requested URI is longer than the server is willing to interpret.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            Please shorten the requested URL and try again.
        </p>
    @endif

</div>

@endsection
