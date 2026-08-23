<?php

namespace Tests\Feature\Api;

use App\Models\McpServer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class McpServerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_servers(): void
    {
        McpServer::factory(3)->create();

        $response = $this->getJson('/api/mcp-servers');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_server(): void
    {
        $response = $this->postJson('/api/mcp-servers', [
            'name' => 'Proxmox',
            'command' => 'npx',
            'args' => ['-y', 'mcp-proxmox'],
            'env' => ['PROXMOX_URL' => 'https://proxmox.local:8006'],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Proxmox');

        $this->assertDatabaseHas('mcp_servers', ['name' => 'Proxmox']);
    }

    public function test_can_show_server(): void
    {
        $server = McpServer::factory()->create();

        $response = $this->getJson("/api/mcp-servers/{$server->id}");

        $response->assertOk()
            ->assertJsonPath('data.name', $server->name);
    }

    public function test_can_delete_server(): void
    {
        $server = McpServer::factory()->create();

        $response = $this->deleteJson("/api/mcp-servers/{$server->id}");

        $response->assertNoContent();
        $this->assertDatabaseEmpty('mcp_servers');
    }
}
