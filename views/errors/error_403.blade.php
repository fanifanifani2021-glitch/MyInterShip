@extends('templates.errors', ['title' => '403 - Forbidden'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">403 Forbidden</h1>

    <p class="error-subtitle">
        You do not have permission to access this page.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            Please log in with a valid account or check your permission level.
        </p>
    @endif

</div>

@endsection
