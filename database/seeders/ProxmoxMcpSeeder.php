<?php

namespace Database\Seeders;

use App\Services\McpManager;
use Illuminate\Database\Seeder;

class ProxmoxMcpSeeder extends Seeder
{
    public function run(McpManager $manager): void
    {
        $manager->register(
            name: 'Proxmox',
            type: 'manual',
            transport: 'stdio',
            command: 'npx',
            args: ['-y', 'mcp-proxmox'],
            env: [
                'PROXMOX_URL' => env('PROXMOX_URL', 'https://proxmox.local:8006'),
                'PROXMOX_TOKEN_ID' => env('PROXMOX_TOKEN_ID'),
                'PROXMOX_TOKEN_SECRET' => env('PROXMOX_TOKEN_SECRET'),
            ],
        );
    }
}
