<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Domain;
use App\Models\Website;
use App\Services\PlanService;
use App\Jobs\DockerJob;

class WizardController extends Controller
{
    protected $planService;

    public function __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }

    public function showDomainSelection()
    {
        $domains = Domain::with('website')
            ->where('user_id', Auth::id())
            ->get();
        return view('wizard.domain', compact('domains'));
    }

    public function processDomainSelection(Request $request)
    {
        $request->validate([
            'new_domain' => 'required|string|max:255',
        ]);

        $domain = Domain::create([
            'address' => $request->input('new_domain'),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('wizard.plan', ['domain_id' => $domain->id]);
    }

    public function deleteDomain(Domain $domain)
    {
        if ($domain->user_id !== Auth::id()) {
            return redirect()->route('wizard.domain')->withErrors(['error' => 'Unauthorized to delete this domain.']);
        }

        if ($domain->website) {
            return redirect()->route('wizard.domain')->withErrors(['error' => 'Cannot delete a domain with an attached website.']);
        }

        $domain->delete();
        return redirect()->route('wizard.domain')->with('success', 'Domain deleted successfully.');
    }

    public function showPlanSelection($domain_id)
    {
        $domain = Domain::findOrFail($domain_id);

        if ($domain->user_id !== Auth::id()) {
            return redirect()->route('wizard.domain')->withErrors(['error' => 'Unauthorized access to this domain.']);
        }

        $plans = $this->planService->getAllPlans();
        return view('wizard.plan', compact('plans', 'domain'));
    }

    public function processPlanSelection(Request $request, $domain_id)
    {
        $request->validate([
            'plan_id' => 'required|integer',
        ]);

        $plan = $this->planService->getPlanById($request->plan_id);
        if (!$plan) {
            return redirect()->route('wizard.plan', ['domain_id' => $domain_id])->withErrors(['plan_id' => 'Invalid plan selected.']);
        }

        $userId = Auth::id();
        $domain = Domain::findOrFail($domain_id);

        $stack = strtolower(preg_replace('/[^a-zA-Z0-9-_]/', '', $domain->address));
        $website = Website::create([
            'user_id' => $userId,
            'domain_id' => $domain_id,
            'plan_id' => $request->plan_id,
        ]);

        try {
            $placeholders = ['{{stack}}', '{{storage}}', '{{userId}}', '{{websiteId}}', '{{registry}}', '{{domain}}'];
            $replacements = [$stack, storage_path('sites'), $userId, $website->id, storage_path('virtualizer/docker'), $domain->address];

            // Create Docker network
            DockerJob::dispatch('create-network', ['title' => "Create network: private_$stack", 'networkName' => "private_$stack"]);

            // Process containers
            foreach ($plan['containers'] as &$container) {
                foreach ($container as $key => &$parameter) {
                    if (is_string($parameter)) {
                        $parameter = str_replace($placeholders, $replacements, $parameter);
                    }

                    if (is_array($parameter)) {
                        foreach ($parameter as $subKey => $subValue) {
                            $newKey = str_replace($placeholders, $replacements, $subKey);
                            unset($parameter[$subKey]);
                            $parameter[$newKey] = is_string($subValue) ? str_replace($placeholders, $replacements, $subValue) : $subValue;
                        }
                    }
                }

                $container['volumes'] ??= [];

                foreach ($container['volumes'] as $hostDirectory => $containerDirectory) {
                    $permissions = 0777;
                    if (!Storage::exists($hostDirectory)) {
                        Storage::makeDirectory($hostDirectory, $permissions, true);
                    }
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
            return redirect()->route('wizard.domain')->withErrors(['error' => 'Failed to create website containers.']);
        }

        return redirect()->route('wizard.summary', ['website_id' => $website->id]);
    }

    public function deleteWebsite(Website $website)
    {
        if ($website->user_id !== Auth::id()) {
            return redirect()->route('wizard.domain')->withErrors(['error' => 'Unauthorized to delete this website.']);
        }

        $plan = $this->planService->getPlanById($website->plan_id);
        if (!$plan) {
            return redirect()->route('wizard.domain')->withErrors(['error' => 'Plan not found for this website.']);
        }

        $stack = strtolower(preg_replace('/[^a-zA-Z0-9-_]/', '', $website->domain->address));

        try {
            // Remove containers
            foreach ($plan['containers'] as $container) {
                $hostname = str_replace('{{stack}}', $stack, $container['hostname']);
                DockerJob::dispatch('remove', ['title' => "Remove container: $hostname", 'containerName' => $hostname, 'force' => true]);
            }

            // Remove Docker network
            DockerJob::dispatch('rm-network', ['title' => "Remove network: private_$stack", 'networkName' => "private_$stack"]);

            $website->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting containers for website', ['error' => $e->getMessage()]);
            return redirect()->route('wizard.domain')->withErrors(['error' => 'Failed to delete website and its containers.']);
        }

        return redirect()->route('wizard.domain')->with('success', 'Website and containers deleted successfully.');
    }

    public function showSummary($website_id)
    {
        $website = Website::with('domain')->findOrFail($website_id);
        return view('wizard.summary', compact('website'));
    }
}