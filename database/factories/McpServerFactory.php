<?php

namespace Database\Factories;

use App\Models\McpServer;
use Illuminate\Database\Eloquent\Factories\Factory;

class McpServerFactory extends Factory
{
    protected $model = McpServer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'type' => 'manual',
            'transport' => 'stdio',
            'command' => 'npx',
            'args' => ['-y', 'mcp-test'],
            'env' => null,
            'url' => null,
            'status' => 'disconnected',
            'capabilities' => null,
            'last_seen_at' => null,
            'metadata' => null,
        ];
    }

    public function connected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'connected',
            'last_seen_at' => now(),
            'capabilities' => [
                'tools' => [
                    ['name' => 'test_tool', 'description' => 'A test tool'],
                ],
            ],
        ]);
    }
}
