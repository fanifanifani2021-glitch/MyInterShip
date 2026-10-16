@extends('templates.errors', ['title' => '412 - Precondition Failed'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">412 Precondition Failed</h1>

    <p class="error-subtitle">
        One or more request conditions were not met.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            The server cannot complete the request due to unmet conditions.
        </p>
    @endif

</div>

@endsection
