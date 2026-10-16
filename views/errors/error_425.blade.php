@extends('templates.errors', ['title' => '425 - Too Early'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">425 Too Early</h1>

    <p class="error-subtitle">
        The server is unwilling to risk processing a request that may be replayed.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            Please retry the request after some time.
        </p>
    @endif

</div>

@endsection
