@extends('templates.errors', ['title' => '406 - Not Acceptable'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">406 Not Acceptable</h1>

    <p class="error-subtitle">
        The server cannot generate a response matching the criteria.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            The requested resource is not available in a suitable format.
        </p>
    @endif

</div>

@endsection
