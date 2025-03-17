<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\DockerJob;

class ImagesController extends Controller
{
    /**
     * Display the form for selecting a Dockerfile and building an image.
     */
    public function index()
    {
        // Fetch available Dockerfile names
        $dockerfiles = $this->getDockerfileNames();
        return view('images', ['dockerfiles' => $dockerfiles]);
    }

    /**
     * Handle image build request.
     */
    public function build(Request $request)
    {
        // Validate the selected Dockerfile
        $request->validate([
            'dockerfile' => 'required|string',
        ]);

        $projectRoot = base_path();
        $dockerfilePath = $projectRoot . '/daemon/docker/' . $request->input('dockerfile');
        $contextPath = $projectRoot . '/daemon/docker';

        if (!file_exists($dockerfilePath)) {
            Log::error('Dockerfile does not exist at path: ' . $dockerfilePath);
            return redirect()->back()->withErrors(['dockerfile' => 'Dockerfile not found.']);
        }
        
        $dockerfileName = pathinfo($request->input('dockerfile'), PATHINFO_FILENAME);
        $imageName = strtolower(preg_replace('/[^a-zA-Z0-9-_]/', '', $dockerfileName));

        try {
            DockerJob::dispatch('build', [
                'dockerfilePath' => $dockerfilePath,
                'contextPath' => $contextPath,
                'imageName' => $imageName,
                'title' => "build/$imageName"
            ]);
            return redirect()->back()->with('success', 'Docker build job dispatched successfully.');
        } catch (\Exception $e) {
            Log::error('Error dispatching Docker build job: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to dispatch Docker build job.']);
        }
    }

    /**
     * Get available Dockerfile names from a specific directory.
     */
    private function getDockerfileNames()
    {
        $path = base_path('daemon/docker');
        $files = scandir($path);
        return array_filter($files, function ($file) {
            return is_file(base_path("daemon/docker/$file")) && pathinfo($file, PATHINFO_EXTENSION) === 'Dockerfile';
        });
    }
}
