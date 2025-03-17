<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Services\PlanService;
use App\Models\Website;
use App\Models\Domain;
use App\Jobs\DockerJob;

class WebsitesController extends Controller
{
    protected $planService;

    public function __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }

    public function index()
    {
        // Fetch websites with their associated domains
        $websites = Auth::user()->websites()->with('domain')->get();
        return view('websites.index', compact('websites'));
    }

    public function create()
    {
        $plans = $this->planService->getAllPlans();
        $domains = Domain::all();
        return view('websites.create', compact('plans', 'domains'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|integer',
            'domain_id' => 'required|exists:domains,id',
        ]);

        $plan = $this->planService->getPlanById($request->plan_id);
        if (!$plan) {
            return redirect('websites')->withErrors(['plan_id' => 'Invalid plan selected.']);
        }

        $domain = Domain::findOrFail($request->domain_id);
        $userId = Auth::id();
        $stack = strtolower(preg_replace('/[^a-zA-Z0-9-_]/', '', $domain->address));

        // Create website record
        $website = Website::create([
            'user_id' => $userId,
            'domain_id' => $request->domain_id,
            'plan_id' => $request->plan_id,
        ]);

        try {
            $placeholders = ['{{stack}}', '{{storage}}', '{{userId}}', '{{websiteId}}', '{{registry}}', '{{domain}}'];
            $replacements = [$stack, storage_path('sites'), $userId, $website->id, storage_path('virtualizer/docker'), $website->domain->address];

            DockerJob::dispatch('create-network', ['title' => "Create network: private_$stack", 'networkName' => "private_$stack"]);
            foreach ($plan['containers'] as &$container) {
                foreach ($container as $key => &$parameter) {
                    // Handle parameter if it's a string
                    if (is_string($parameter)) {
                        $parameter = str_replace($placeholders, $replacements, $parameter);
                    }

                    // Handle parameter if it's an array (key-value pairs)
                    if (is_array($parameter)) {
                        foreach ($parameter as $subKey => $subValue) {
                            // Replace placeholders in the key
                            $newKey = str_replace($placeholders, $replacements, $subKey);
                            unset($parameter[$subKey]); // Remove the old key

                            // Replace placeholders in the value if it's a string
                            if (is_string($subValue)) {
                                $subValue = str_replace($placeholders, $replacements, $subValue);
                            }

                            // Update the array with the new key and value
                            $parameter[$newKey] = $subValue;
                        }
                    }
                }

                $container['volumes'] ??= [];

                foreach ($container['volumes'] as $hostDirectory => $containerDirectory) {
                    $permissions = 777;
                    if (!Storage::exists($hostDirectory)) {
                        Storage::makeDirectory($hostDirectory, $permissions, true);
                    }
                    // if (!empty($container['user'])) {
                    //     chmod($hostDirectory, $permissions);
                    //     if (function_exists('chown')) {
                    //             chown($hostDirectory, $container['user']);
                    //     }
                    // }
                }

                DockerJob::dispatch('run', [
                    'title' => 'Run container: ' . $container['hostname'],
                    'imageName' => $container['imageName'],
                    'containerName' => $container['hostname'],
                    'hostname' => $container['hostname'],
                    'volumes' => $container['volumes'] ?? [],
                    'networks' => $container['networks'] ?? [],
                    'extraFlags' => $container['extraFlags'] ?? [],
                    'variables' => $container['variables'] ?? [],
                    'ports' => $container['ports'] ?? [],
                    'workdir' => $container['workdir'] ?? '',
                    'user' => $container['user'] ?? '',
                    'dns' => $container['dns'] ?? [],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error creating containers for website', ['error' => $e->getMessage()]);
            return redirect('websites')->withErrors(['error' => 'Failed to create website containers.']);
        }

        return redirect('websites')->with('success', 'Website and containers created successfully.');
    }

    public function show(Website $website)
    {
        $plan = $this->planService->getPlanById($website->plan_id);
        $containers = $plan['containers'] ?? [];
        return view('websites.show', compact('website', 'containers'));
    }

    public function edit(Website $website)
    {
        $plans = $this->planService->getAllPlans();
        $domains = Domain::all();
        return view('websites.edit', compact('website', 'plans', 'domains'));
    }

    public function update(Request $request, Website $website)
    {
        $request->validate([
            'plan_id' => 'required|integer',
            'domain_id' => 'required|exists:domains,id',
        ]);

        $plan = $this->planService->getPlanById($request->plan_id);
        if (!$plan) {
            return redirect('websites')->withErrors(['plan_id' => 'Invalid plan selected.']);
        }

        $website->update([
            'plan_id' => $request->plan_id,
            'domain_id' => $request->domain_id,
        ]);

        return redirect('websites')->with('success', 'Website updated successfully.');
    }

    public function destroy(Website $website)
    {
        $plan = $this->planService->getPlanById($website->plan_id);
        if (!$plan) {
            return redirect('websites')->withErrors(['error' => 'Plan not found for this website.']);
        }

        $containerStack = strtolower(preg_replace('/[^a-zA-Z0-9-_]/', '', $website->domain->address));

        try {
            foreach ($plan['containers'] as $container) {
                $hostname = str_replace('{{stack}}', $containerStack, $container['hostname']);
                DockerJob::dispatch('remove', ['title' => "Remove container: $hostname", 'containerName' => $hostname, 'force' => true]);
            }
            $domain = Domain::findOrFail($website->domain_id);
            $stack = strtolower(preg_replace('/[^a-zA-Z0-9-_]/', '', $domain->address));
            DockerJob::dispatch('rm-network', ['title' => "Remove network: private_stack", 'networkName' => "private_$stack"]);
            $website->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting containers for website', ['error' => $e->getMessage()]);
            return redirect('websites')->withErrors(['error' => 'Failed to delete website and its containers.']);
        }

        return redirect('websites')->with('success', 'Website and containers deleted successfully.');
    }
}
