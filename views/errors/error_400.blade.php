@extends('templates.errors', ['title' => '400 - Bad Request'])

@section('main-content')

<div class="error-header">
    <h1 class="error-title">400 Bad Request</h1>

    <p class="error-subtitle">Your request cannot be processed.</p>

    @if ( $message )
    <p class="error-desc">
        {{ $message }}
    </p>
    @else
    <p class="error-desc">
        It looks like something is wrong with the request you sent.
    </p>
    @endif
</div>

@endsection
