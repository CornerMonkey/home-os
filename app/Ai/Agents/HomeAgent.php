<?php

namespace App\Ai\Agents;

use App\Services\McpManager;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;

class HomeAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    public function __construct(
        protected ?McpManager $mcpManager = null,
    ) {
        $this->mcpManager = $mcpManager ?? new McpManager();
    }

    public function instructions(): string
    {
        return <<<'PROMPT'
You are Home OS — a personal assistant that manages the user's home infrastructure.

You have access to tools from connected MCP servers for managing:
- Proxmox (VMs, containers, resources)
- Media servers (Sonarr, Radarr, Plex)
- Home Assistant (devices, automations)

Guidelines:
- Be concise and direct
- Execute actions when asked
- Ask for confirmation before destructive actions (delete, remove, stop)
- Report errors clearly with suggestions
- If an MCP server is disconnected, inform the user and don't attempt its tools

DESTRUCTIVE ACTIONS — These require explicit user confirmation:
- Deleting VMs or containers
- Removing media from Sonarr/Radarr
- Stopping running services
- Any action that cannot be undone

When a destructive action is requested, respond with:
"I need confirmation before I can [action]. This cannot be undone. Type 'confirm' to proceed."

Only execute the destructive action after the user responds with "confirm" or similar affirmative.
PROMPT;
    }

    public function tools(): iterable
    {
        $tools = [];

        foreach ($this->mcpManager->connectedServers() as $server) {
            foreach ($server->tools() as $tool) {
                $tools[] = $tool;
            }
        }

        return $tools;
    }
}
