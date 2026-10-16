@extends('templates.errors', ['title' => '410 - Gone'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">410 Gone</h1>

    <p class="error-subtitle">
        The requested resource is no longer available.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            This page or endpoint has been permanently removed from the server.
        </p>
    @endif

</div>

@endsection
