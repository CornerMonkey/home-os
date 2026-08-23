<?php

namespace Database\Seeders;

use App\Services\McpManager;
use Illuminate\Database\Seeder;

class ArrMcpSeeder extends Seeder
{
    public function run(McpManager $manager): void
    {
        $manager->register(
            name: 'Arr Suite',
            type: 'manual',
            transport: 'stdio',
            command: 'npx',
            args: ['-y', 'mcp-arr'],
            env: [
                'SONARR_URL' => env('SONARR_URL', 'http://localhost:8989'),
                'SONARR_API_KEY' => env('SONARR_API_KEY'),
                'RADARR_URL' => env('RADARR_URL', 'http://localhost:7878'),
                'RADARR_API_KEY' => env('RADARR_API_KEY'),
            ],
        );
    }
}
