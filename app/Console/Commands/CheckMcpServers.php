<?php

namespace App\Console\Commands;

use App\Models\McpServer;
use App\Services\McpManager;
use Illuminate\Console\Command;

class CheckMcpServers extends Command
{
    protected $signature = 'mcp:health-check';
    protected $description = 'Check health of all registered MCP servers';

    public function handle(McpManager $manager): int
    {
        $servers = McpServer::all();

        if ($servers->isEmpty()) {
            $this->info('No MCP servers registered.');
            return self::SUCCESS;
        }

        foreach ($servers as $server) {
            $this->line("Checking {$server->name}...");

            if ($server->last_seen_at && $server->last_seen_at->diffInMinutes(now()) > 5) {
                $manager->updateStatus($server->id, 'disconnected');
                $this->warn("  {$server->name}: disconnected (not seen in 5+ minutes)");
            } else {
                $this->info("  {$server->name}: {$server->status}");
            }
        }

        return self::SUCCESS;
    }
}
