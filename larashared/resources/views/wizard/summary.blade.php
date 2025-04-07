@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Summary</h1>

    <!-- Website Details -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h4 class="card-title">Website Details</h4>
            <p class="card-text"><strong>Website ID:</strong> {{ $website->id }}</p>
            <p class="card-text"><strong>Domain:</strong> <a href="http://{{ $website->domain->address }}">{{ $website->domain->address }}</a></p>
            <p class="card-text"><strong>Created At:</strong> {{ $website->created_at->format('d M, Y H:i') }}</p>
        </div>
    </div>

    <!-- Actions -->
    <div class="text-center mt-4">
        <!-- Delete Website Button -->
        <form action="{{ route('wizard.deleteWebsite', ['website' => $website->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this website?');" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-lg">Delete Website</button>
        </form>

        <!-- Edit Website Button -->
        <a href="http://{{ $website->domain->address }}/filemanager/" class="btn btn-secondary btn-lg">Files</a>
        <a href="http://{{ $website->domain->address }}/phpmyadmin/" class="btn btn-secondary btn-lg">Databases</a>
    </div>
</div>
@endsection
