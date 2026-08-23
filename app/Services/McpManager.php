<?php

namespace App\Services;

use App\Models\McpServer;
use Illuminate\Support\Collection;

class McpManager
{
    public function register(
        string $name,
        string $type = 'manual',
        string $transport = 'stdio',
        ?string $command = null,
        ?array $args = null,
        ?array $env = null,
        ?string $url = null,
        ?array $metadata = null,
    ): McpServer {
        return McpServer::create([
            'name' => $name,
            'type' => $type,
            'transport' => $transport,
            'command' => $command,
            'args' => $args,
            'env' => $env,
            'url' => $url,
            'status' => 'disconnected',
            'metadata' => $metadata,
        ]);
    }

    public function connectedServers(): Collection
    {
        return McpServer::connected()->get();
    }

    public function allServers(): Collection
    {
        return McpServer::all();
    }

    public function remove(string $id): bool
    {
        return McpServer::findOrFail($id)->delete();
    }

    public function updateStatus(string $id, string $status, ?array $capabilities = null): McpServer
    {
        $server = McpServer::findOrFail($id);
        $server->update([
            'status' => $status,
            'last_seen_at' => now(),
            'capabilities' => $capabilities ?? $server->capabilities,
        ]);

        return $server;
    }
}
