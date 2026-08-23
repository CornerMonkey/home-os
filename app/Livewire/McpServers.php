<?php

namespace App\Livewire;

use App\Models\McpServer;
use App\Services\McpManager;
use App\Services\McpDiscovery;
use App\Services\McpRegistry;
use Livewire\Component;

class McpServers extends Component
{
    public array $servers = [];
    public bool $showAddForm = false;
    public string $newName = '';
    public string $newCommand = 'npx';
    public string $newArgs = '';
    public string $newEnv = '';
    public array $discoveredServers = [];
    public string $registrySearch = '';
    public array $registryResults = [];

    public function mount(): void
    {
        $this->loadServers();
    }

    public function loadServers(): void
    {
        $this->servers = McpServer::all()
            ->map(fn ($server) => [
                'id' => $server->id,
                'name' => $server->name,
                'type' => $server->type,
                'status' => $server->status,
                'command' => $server->command,
                'args' => $server->args,
            ])
            ->toArray();
    }

    public function toggleAddForm(): void
    {
        $this->showAddForm = !$this->showAddForm;
    }

    public function addServer(McpManager $manager): void
    {
        $args = array_filter(array_map('trim', explode(' ', $this->newArgs)));
        $env = [];
        if (!empty($this->newEnv)) {
            foreach (explode("\n", $this->newEnv) as $line) {
                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $env[trim($parts[0])] = trim($parts[1]);
                }
            }
        }

        $manager->register(
            name: $this->newName,
            command: $this->newCommand,
            args: $args,
            env: $env,
        );

        $this->newName = '';
        $this->newCommand = 'npx';
        $this->newArgs = '';
        $this->newEnv = '';
        $this->showAddForm = false;
        $this->loadServers();
    }

    public function removeServer(string $id, McpManager $manager): void
    {
        $manager->remove($id);
        $this->loadServers();
    }

    public function discoverServers(McpDiscovery $discovery): void
    {
        $this->discoveredServers = $discovery->discoverAll()->toArray();
    }

    public function installDiscovered(int $index, McpDiscovery $discovery): void
    {
        if (isset($this->discoveredServers[$index])) {
            $discovery->registerDiscovery($this->discoveredServers[$index]);
            $this->discoveredServers = [];
            $this->loadServers();
        }
    }

    public function searchRegistry(McpRegistry $registry): void
    {
        if (empty($this->registrySearch)) {
            $this->registryResults = [];
            return;
        }

        $this->registryResults = $registry->search($this->registrySearch)->toArray();
    }

    public function installFromRegistry(string $package, McpRegistry $registry, McpManager $manager): void
    {
        $registry->install($package, $manager);
        $this->registryResults = [];
        $this->registrySearch = '';
        $this->loadServers();
    }

    public function render()
    {
        return view('livewire.mcp-servers');
    }
}
