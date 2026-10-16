@extends('templates.errors', ['title' => '402 - Payment Required'])

@section('main-content')

<div class="error-header">

    <h1 class="error-title">402 Payment Required</h1>

    <p class="error-subtitle">
        Access to this resource requires payment.
    </p>

    @if ($message)
        <p class="error-desc">{{ $message }}</p>
    @else
        <p class="error-desc">
            The server cannot process your request without required payment.
        </p>
    @endif

</div>

@endsection
