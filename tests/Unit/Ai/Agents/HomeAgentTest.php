<?php

namespace Tests\Unit\Ai\Agents;

use App\Ai\Agents\HomeAgent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeAgentTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_has_instructions(): void
    {
        $agent = new HomeAgent();

        $this->assertNotEmpty($agent->instructions());
    }

    public function test_agent_can_be_instantiated(): void
    {
        $agent = new HomeAgent();

        $this->assertInstanceOf(HomeAgent::class, $agent);
    }

    public function test_agent_has_messages_method(): void
    {
        $agent = new HomeAgent();

        $this->assertIsArray($agent->messages());
    }

    public function test_agent_has_tools_method(): void
    {
        $agent = new HomeAgent();

        $this->assertIsArray($agent->tools());
    }
}
