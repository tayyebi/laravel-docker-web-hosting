@extends('layouts.app')

@section('content')
<h1>Website: {{ $website->domain->address }}</h1>

<h3>Details</h3>
<ul>
    <li><strong>Domain:</strong> {{ $website->domain->address }}</li>
    <li><strong>Plan:</strong> {{ $website->plan->name ?? 'N/A' }}</li>
    <li><strong>Created By:</strong> {{ $website->user->name }}</li>
</ul>

<h3>Container States</h3>
@if (!empty($website->containers))
    <ul>
        @foreach ($website->containers as $container)
            <li>
                <strong>{{ $container['name'] }}</strong>: {{ $container['state'] }}
            </li>
        @endforeach
    </ul>
@else
    <p>No containers found for this website.</p>
@endif

<a href="{{ route('websites.edit', $website) }}" class="btn btn-warning">Edit Website</a>
<a href="{{ route('websites.index') }}" class="btn btn-secondary">Back to Websites</a>
@endsection
