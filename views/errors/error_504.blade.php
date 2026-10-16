@extends('templates.errors', ['title' => '504 - Gateway Timeout'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">504 Gateway Timeout</h1>

    <p class="error-subtitle">
        The server took too long to respond.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            This usually happens when a request takes too long to process. Please try again later.
        </p>
    @endif

</div>

@endsection
