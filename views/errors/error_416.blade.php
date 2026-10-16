@extends('templates.errors', ['title' => '416 - Range Not Satisfiable'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">416 Range Not Satisfiable</h1>

    <p class="error-subtitle">
        The requested range cannot be served by the server.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            The requested byte range is not acceptable for this resource.
        </p>
    @endif

</div>

@endsection
