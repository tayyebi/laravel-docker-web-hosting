<?php
namespace App\Http\Controllers;

use App\Models\Domain;
use Illuminate\Http\Request;

class DomainsController extends Controller
{
    public function index()
    {
        $records = Domain::all();
        return view('domains.index', compact('records'));
    }

    public function create()
    {
        return view('domains.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255'
        ]);

        $userId = auth()->id();

        Domain::create([
            'address' => $request->address,
            'user_id' => $userId,
        ]);

        return redirect()->route('domains.index')->with('success', 'Record created successfully.');
    }

    public function show(Domain $domain)
    {
        return view('domains.show', compact('domain'));
    }

    public function edit(Domain $domain)
    {
        return view('domains.edit', compact('domain'));
    }

    public function update(Request $request, Domain $domain)
    {
        $request->validate([
            'address' => 'required|string|max:255'
        ]);

        $domain->update($request->all());
        return redirect()->route('domains.index')->with('success', 'Record updated successfully.');
    }

    public function destroy(Domain $domain)
    {
        $domain->delete();
        return redirect()->route('domains.index')->with('success', 'Record deleted successfully.');
    }
}