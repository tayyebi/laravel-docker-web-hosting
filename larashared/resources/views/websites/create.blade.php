@extends('layouts.app')

@section('content')
<h2>Create a New Website</h2>
<form action="{{ route('websites.store') }}" method="POST">
    @csrf

    <!-- Plan Selection -->
    <div class="form-group">
        <label for="plan_id">Select a Hosting Plan:</label>
        <select id="plan_id" name="plan_id" class="form-control" required>
            <option value="">Select a Plan</option>
            @foreach ($plans as $plan)
                <option value="{{ $plan['id'] }}">
                    {{ $plan['name'] }} (CPU: {{ $plan['containers'][0]['cpu'] }}, RAM: {{ $plan['containers'][0]['ram'] }}, Storage: {{ $plan['storage'] }})
                </option>
            @endforeach
        </select>
    </div>

    <!-- Domain Selection -->
    <div class="form-group">
        <label for="domain_id">Choose a Domain:</label>
        <select id="domain_id" name="domain_id" class="form-control" required>
            <option value="">Select a Domain</option>
            @foreach ($domains as $domain)
                <option value="{{ $domain->id }}">{{ $domain->address }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Create Website</button>
</form>
@endsection