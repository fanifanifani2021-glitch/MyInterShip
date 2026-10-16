@extends('templates.errors', ['title' => '426 - Upgrade Required'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">426 Upgrade Required</h1>

    <p class="error-subtitle">
        The client must upgrade to a different protocol.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            Please upgrade your request protocol and try again.
        </p>
    @endif

</div>

@endsection
