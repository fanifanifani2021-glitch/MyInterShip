@extends('templates.errors', ['title' => '422 - Unprocessable Entity'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">422 Unprocessable Entity</h1>

    <p class="error-subtitle">
        The server understands the request but cannot process the contained instructions.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            Please check your submitted data and try again.
        </p>
    @endif

</div>

@endsection
