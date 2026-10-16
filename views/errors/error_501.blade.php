@extends('templates.errors', ['title' => '501 - Not Implemented'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">501 Not Implemented</h1>

    <p class="error-subtitle">
        This feature or request has not been implemented.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            The server does not support this functionality yet. Please check back later.
        </p>
    @endif

</div>

@endsection
