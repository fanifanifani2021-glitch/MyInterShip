@extends('templates.errors', ['title' => '428 - Precondition Required'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">428 Precondition Required</h1>

    <p class="error-subtitle">
        The server requires conditions to be specified before processing.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            This request must include the required preconditions.
        </p>
    @endif

</div>

@endsection
