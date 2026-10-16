@extends('templates.errors', ['title' => '404 - Not Found'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">404 Not Found</h1>

    <p class="error-subtitle">
        The page you are looking for could not be found.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            The resource you are trying to access does not exist or may have been moved.
        </p>
    @endif

</div>

@endsection
