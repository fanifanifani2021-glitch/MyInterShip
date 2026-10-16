@extends('templates.errors', ['title' => '419 - Page Expired'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">419 Page Expired</h1>

    <p class="error-subtitle">
        Your session has expired or the security token is invalid.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            Please refresh the page and try again. This often happens if the form was open for too long or the session ended.
        </p>
    @endif

</div>

@endsection
