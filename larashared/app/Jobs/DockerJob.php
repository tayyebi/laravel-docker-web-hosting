<?php

namespace App\Jobs;

use App\Services\DockerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DockerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $action;
    private $parameters;

    /**
     * Create a new job instance.
     *
     * @param string $action The action to perform (e.g., build, run, remove, stop, restart, start, get-state, rebuild)
     * @param array $parameters Parameters for the Docker command
     */
    public function __construct(string $action, array $parameters)
    {
        $this->action = $action;
        $this->parameters = $parameters;
    }

    /**
     * Generate a meaningful job title.
     *
     * @return string
     */
    public function displayName()
    {
        return $this->parameters['title'] ?? 'DockerJob';
    }

    /**
     * Execute the job.
     *
     * @param DockerService $dockerService
     */
    public function handle(DockerService $dockerService)
    {
        $result = [];

        switch ($this->action) {
            case 'build':
                $result = $dockerService->buildImage(
                    $this->parameters['dockerfilePath'],
                    $this->parameters['contextPath'],
                    $this->parameters['imageName']
                );
                break;

            case 'run':
                $result = $dockerService->runContainer(
                    imageName: $this->parameters['imageName'],
                    containerName: $this->parameters['containerName'],
                    volumes: $this->parameters['volumes'],
                    hostname: $this->parameters['hostname'],
                    networks: $this->parameters['networks'],
                    extraFlags: $this->parameters['extraFlags'],
                    variables: $this->parameters['variables'],
                    ports: $this->parameters['ports'],
                    workdir: $this->parameters['workdir'],
                    dns: $this->parameters['dns'],
                    user: $this->parameters['user']
                );
                break;

            case 'remove':
                $result = $dockerService->removeContainer(
                    $this->parameters['containerName'],
                    $this->parameters['force'] ?? false
                );
                break;

            case 'stop':
                $result = $dockerService->stopContainer(
                    $this->parameters['containerName']
                );
                break;

            case 'restart':
                $result = $dockerService->restartContainer(
                    $this->parameters['containerName']
                );
                break;

            // case 'start':
            //     $result = $dockerService->startContainer(
            //         $this->parameters['containerName']
            //     );
            //     break;

            case 'get-state':
                $result = $dockerService->getContainerState(
                    $this->parameters['containerName']
                );
                break;

            case 'create-network':
                $result = $dockerService->createNetwork(
                    $this->parameters['networkName']
                );
                break;

            case 'rm-network':
                $result = $dockerService->removeNetwork(
                    $this->parameters['networkName']
                );
                break;

            default:
                Log::error("Unknown Docker action: {$this->action}");
                $result = [
                    'success' => false,
                    'error' => "Unknown Docker action: {$this->action}",
                ];
                break;
        }

        if (!$result['success']) {
            Log::error("Docker {$this->action} task failed", [
                'error' => $result['error'],
                'parameters' => $this->parameters,
            ]);
        }
    }
}
