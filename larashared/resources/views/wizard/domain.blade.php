@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Select or Add a Domain</h1>

    <!-- Domains Display -->
    <div class="row justify-content-center">
        @foreach ($domains as $domain)
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1">{{ $domain->address }}</h5>
                        <p class="card-text text-muted mb-0">
                            Created on: {{ $domain->created_at->format('d M, Y') }}
                        </p>
                        @if ($domain->website)
                        <p class="card-text mb-0">
                            <strong>Website ID:</strong> {{ $domain->website->id }}
                        </p>
                        <p class="card-text">
                            <strong>Plan:</strong> {{ $domain->website->plan->name ?? 'N/A' }}
                        </p>
                        <a href="{{ route('wizard.summary', ['website_id' => $domain->website->id]) }}" class="btn btn-success">
                            Proceed to Website Summary
                        </a>
                        @else
                        <a href="{{ route('wizard.plan', ['domain_id' => $domain->id]) }}" class="btn btn-primary">
                            Proceed to Plan Selection
                        </a>
                        @endif
                    </div>

                    <!-- Delete Button -->
                    <div>
                        <form action="{{ route('wizard.deleteDomain', ['domain' => $domain->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this domain?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Divider -->
    <div class="text-center mt-4 mb-4">
        <h5 class="text-muted">Or add a new domain</h5>
    </div>

    <!-- New Domain Submission Form -->
    <form action="{{ route('wizard.processDomain') }}" method="POST">
        @csrf
        <div class="card mb-4">
            <div class="card-body">
                <div class="form-group">
                    <label for="new_domain" class="form-label">New Domain Name</label>
                    <input type="text" name="new_domain" id="new_domain" class="form-control" placeholder="Enter new domain name">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg">Next Step</button>
        </div>
    </form>
</div>
@endsection