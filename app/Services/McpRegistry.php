<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class McpRegistry
{
    protected string $baseUrl = 'https://registry.modelcontextprotocol.io';

    public function search(string $query): Collection
    {
        $response = Http::get("{$this->baseUrl}/v0/api/v0/servers", [
            'q' => $query,
        ]);

        if ($response->successful()) {
            return collect($response->json('servers', []));
        }

        return collect();
    }

    public function getServer(string $id): ?array
    {
        $response = Http::get("{$this->baseUrl}/v0/api/v0/servers/{$id}");

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function install(string $package, McpManager $manager): \App\Models\McpServer
    {
        return $manager->register(
            name: $package,
            type: 'registry',
            transport: 'stdio',
            command: 'npx',
            args: ['-y', $package],
            metadata: [
                'registry_package' => $package,
                'installed_at' => now()->toISOString(),
            ],
        );
    }
}
