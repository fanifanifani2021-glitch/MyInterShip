@extends('templates.errors', ['title' => '409 - Conflict'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">409 Conflict</h1>

    <p class="error-subtitle">
        The request could not be completed due to a conflict.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            There is a conflict with the current state of the resource.
        </p>
    @endif

</div>

@endsection
