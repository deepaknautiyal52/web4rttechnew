@extends('layouts.site', ['title' => $heading, 'description' => $heading])

@section('content')
<main class="container">
    <h2>{{ $heading }}</h2>
    @if (!empty($message))
        <p>{{ $message }}</p>
    @endif
    <a href="{{ $backUrl }}">{{ $backLabel }}</a>
</main>
@endsection
