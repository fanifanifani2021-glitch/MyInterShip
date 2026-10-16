@extends('templates.errors', ['title' => '405 - Method Not Allowed'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">405 Method Not Allowed</h1>

    <p class="error-subtitle">
        The method used in the request is not allowed for this resource.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            Please check the HTTP request method and try again.
        </p>
    @endif

</div>

@endsection
