@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Choose a Plan</h1>

    <form action="{{ route('wizard.processPlan', ['domain_id' => $domain->id]) }}" method="POST">
        @csrf
        <div class="row">
            @foreach ($plans as $plan)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $plan['name'] }}</h5>
                        <p class="card-text">
                            <strong>Price:</strong> {{ $plan['price'] ?? 'N/A' }} <br>
                            <strong>Description:</strong> {{ $plan['description'] ?? 'No description available' }}
                        </p>
                        <div class="form-check">
                            <input type="radio" name="plan_id" value="{{ $plan['id'] }}" id="plan-{{ $plan['id'] }}" class="form-check-input" required>
                            <label for="plan-{{ $plan['id'] }}" class="form-check-label">Select</label>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary btn-lg">Next</button>
        </div>
    </form>
</div>
@endsection
