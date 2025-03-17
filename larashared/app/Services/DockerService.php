<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class DockerService
{
    /**
     * Build a Docker image with logging.
     */
    public function buildImage(string $dockerfilePath, string $contextPath, string $imageName): array
    {
        $command = sprintf(
            'docker build -t %s -f %s %s',
            escapeshellarg($imageName),
            escapeshellarg($dockerfilePath),
            escapeshellarg($contextPath)
        );

        return $this->logAndExecute($command, "Failed to build Docker image", compact('dockerfilePath', 'contextPath', 'imageName'));
    }

    /**
     * Run a Docker container with logging.
     */
    public function runContainer(
        string $imageName,
        string $containerName,
        array $volumes = [],
        string $hostname,
        array $networks = [],
        array $extraFlags = [],
        array $variables = [],
        array $ports = [],
        string $workdir,
        array $dns = [],
        string $user
    ): array {
        $command = sprintf(
            'docker run --restart unless-stopped -d --name %s --hostname=%s',
            escapeshellarg($containerName),
            escapeshellarg($hostname)
        );

        foreach ($volumes as $hostPath => $containerPath) {
            $command .= sprintf(' -v %s:%s', escapeshellarg($hostPath), escapeshellarg($containerPath));
        }

        foreach ($variables as $key => $value) {
            $command .= sprintf(' -e %s=%s', escapeshellarg($key), escapeshellarg($value));
        }

        foreach ($networks as $network) {
            $command .= sprintf(' --network %s', escapeshellarg($network));
        }

        foreach ($dns as $domainNameServer) {
            $command .= sprintf(' --dns %s', escapeshellarg($domainNameServer));
        }

        foreach ($ports as $hostPort => $containerPort) {
            $command .= sprintf(' -p %s:%s', escapeshellarg($hostPort), escapeshellarg($containerPort));
        }

        if (!empty($extraFlags))
            $command .= ' ' . implode(' ', array_map('escapeshellarg', $extraFlags));

        if (!empty($workdir))
            $command .= ' --workdir=' . escapeshellarg($workdir);

        if (!empty($workdir))
            $command .= ' --user=' . escapeshellarg($user) . ':' . escapeshellarg($user);

        $command .= ' ' . escapeshellarg($imageName);

        return $this->logAndExecute($command, "Failed to run Docker container", compact('imageName', 'containerName'));
    }

    /**
     * Stop a running Docker container.
     */
    public function stopContainer(string $containerName): array
    {
        $command = sprintf('docker container stop %s', escapeshellarg($containerName));

        return $this->logAndExecute($command, "Failed to stop Docker container", compact('containerName'));
    }

    /**
     * Stop a running Docker container.
     */
    public function removeContainer(string $containerName): array
    {
        $command = sprintf('docker container rm -f %s', escapeshellarg($containerName));

        return $this->logAndExecute($command, "Failed to remove Docker container", compact('containerName'));
    }

    /**
     * Get the state of a Docker container.
     */
    public function getContainerState(string $containerName): array
    {
        $command = sprintf('docker inspect -f \'{{.State.Status}}\' %s', escapeshellarg($containerName));

        return $this->logAndExecute($command, "Failed to get Docker container state", compact('containerName'));
    }

    /**
     * Get all containers in a stack by prefix (e.g., domain name).
     */
    public function getContainersForStack(string $stackPrefix): array
    {
        $command = sprintf('docker ps -a --filter "name=%s" --format "{{.Names}}: {{.State}}"', escapeshellarg($stackPrefix));

        $result = $this->logAndExecute($command, "Failed to list Docker containers for stack", compact('stackPrefix'));

        if ($result['success']) {
            $containers = [];
            foreach (explode(PHP_EOL, trim($result['output'])) as $line) {
                if (!empty($line)) {
                    [$name, $state] = explode(': ', $line);
                    $containers[] = ['name' => $name, 'state' => $state];
                }
            }
            $result['containers'] = $containers;
        }

        return $result;
    }

    /**
     * Restart a Docker container.
     */
    public function restartContainer(string $containerName): array
    {
        $command = sprintf('docker restart %s', escapeshellarg($containerName));

        return $this->logAndExecute($command, "Failed to restart Docker container", compact('containerName'));
    }

    public function createNetwork(string $networkName): array
    {
        $command = sprintf('docker network create %s', escapeshellarg($networkName));

        return $this->logAndExecute($command, "Failed to create Docker network", compact('networkName'));
    }

    public function removeNetwork(string $networkName): array
    {
        $command = sprintf('docker network rm %s', escapeshellarg($networkName));

        return $this->logAndExecute($command, "Failed to remove Docker network", compact('networkName'));
    }

    /**
     * Generic method to execute and log Docker commands.
     */
    private function logAndExecute(string $command, string $errorMessage, array $context = []): array
    {
        Log::info("Executing Docker command", ['command' => $command]);

        $process = Process::fromShellCommandline($command);
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error($errorMessage, array_merge($context, ['error' => $process->getErrorOutput()]));

            return [
                'success' => false,
                'error' => $process->getErrorOutput(),
            ];
        }

        return [
            'success' => true,
            'output' => $process->getOutput(),
        ];
    }
}
