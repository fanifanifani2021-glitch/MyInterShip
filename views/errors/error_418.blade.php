@extends('templates.errors', ['title' => '418 - I\'m a Teapot'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">418 I'm a Teapot</h1>

    <p class="error-subtitle">
        The server refuses to brew coffee because it is, permanently, a teapot.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            This is an Easter egg error from the original HTCPCP protocol.
        </p>
    @endif

</div>

@endsection
