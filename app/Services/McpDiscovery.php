<?php

namespace App\Services;

use App\Models\McpServer;
use Illuminate\Support\Collection;

class McpDiscovery
{
    public function discoverDocker(): Collection
    {
        $discoveries = collect();

        try {
            $process = new \Symfony\Component\Process\Process([
                'docker', 'ps', '--format', 'json',
                '--filter', 'label=mcp.enabled=true',
            ]);
            $process->run();

            if ($process->isSuccessful()) {
                $lines = array_filter(explode("\n", $process->getOutput()));

                foreach ($lines as $line) {
                    $container = json_decode($line, true);
                    if ($container) {
                        $discoveries->push([
                            'name' => $container['Names'] ?? $container['name'],
                            'command' => $container['Labels']['mcp.command'] ?? null,
                            'args' => isset($container['Labels']['mcp.args'])
                                ? json_decode($container['Labels']['mcp.args'], true)
                                : null,
                            'source' => 'docker',
                            'container_id' => $container['ID'] ?? $container['id'],
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Docker not available or other error
        }

        return $discoveries;
    }

    public function discoverAll(): Collection
    {
        return $this->discoverDocker();
    }

    public function registerDiscovery(array $discovery): McpServer
    {
        return McpServer::create([
            'name' => $discovery['name'],
            'type' => 'discovered',
            'transport' => 'stdio',
            'command' => $discovery['command'],
            'args' => $discovery['args'],
            'status' => 'disconnected',
            'metadata' => [
                'source' => $discovery['source'],
                'container_id' => $discovery['container_id'] ?? null,
            ],
        ]);
    }
}
