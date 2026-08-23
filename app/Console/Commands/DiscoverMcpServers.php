<?php

namespace App\Console\Commands;

use App\Services\McpDiscovery;
use Illuminate\Console\Command;

class DiscoverMcpServers extends Command
{
    protected $signature = 'mcp:discover';
    protected $description = 'Discover MCP servers from Docker and other sources';

    public function handle(McpDiscovery $discovery): int
    {
        $this->info('Discovering MCP servers...');

        $servers = $discovery->discoverAll();

        if ($servers->isEmpty()) {
            $this->info('No new MCP servers discovered.');
            return self::SUCCESS;
        }

        foreach ($servers as $server) {
            $this->line("Found: {$server['name']} ({$server['source']})");
        }

        if ($this->confirm('Register discovered servers?')) {
            foreach ($servers as $server) {
                $discovery->registerDiscovery($server);
                $this->info("Registered: {$server['name']}");
            }
        }

        return self::SUCCESS;
    }
}
