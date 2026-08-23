<?php

namespace Tests\Unit\Services;

use App\Models\McpServer;
use App\Services\McpManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class McpManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register_manual_server(): void
    {
        $manager = new McpManager();

        $server = $manager->register(
            name: 'Test Proxmox',
            type: 'manual',
            transport: 'stdio',
            command: 'npx',
            args: ['-y', 'mcp-proxmox'],
            env: ['PROXMOX_URL' => 'https://proxmox.local:8006'],
        );

        $this->assertInstanceOf(McpServer::class, $server);
        $this->assertEquals('Test Proxmox', $server->name);
        $this->assertEquals('manual', $server->type);
        $this->assertEquals('disconnected', $server->status);
    }

    public function test_can_list_connected_servers(): void
    {
        McpServer::factory()->create(['status' => 'connected']);
        McpServer::factory()->create(['status' => 'disconnected']);

        $manager = new McpManager();
        $connected = $manager->connectedServers();

        $this->assertCount(1, $connected);
    }

    public function test_can_remove_server(): void
    {
        $server = McpServer::factory()->create();
        $manager = new McpManager();

        $manager->remove($server->id);

        $this->assertDatabaseEmpty('mcp_servers');
    }

    public function test_can_update_status(): void
    {
        $server = McpServer::factory()->create(['status' => 'disconnected']);
        $manager = new McpManager();

        $updated = $manager->updateStatus($server->id, 'connected', [
            'tools' => [['name' => 'test_tool']],
        ]);

        $this->assertEquals('connected', $updated->status);
        $this->assertNotNull($updated->last_seen_at);
        $this->assertEquals(['tools' => [['name' => 'test_tool']]], $updated->capabilities);
    }
}
