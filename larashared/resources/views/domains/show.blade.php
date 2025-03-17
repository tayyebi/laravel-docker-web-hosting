@extends('layouts.app')

@section('content')
    <h1>Domain Record Details</h1>
    <p><strong>ID:</strong> {{ $domain->id }}</p>
    <p><strong>Address:</strong> {{ $domain->address }}</p>
    <a href="{{ route('domains.index') }}" class="btn btn-secondary">Back</a>
@endsection