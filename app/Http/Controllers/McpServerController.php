<?php

namespace App\Http\Controllers;

use App\Models\McpServer;
use App\Services\McpManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class McpServerController extends Controller
{
    public function index(): JsonResponse
    {
        $servers = McpServer::all();

        return response()->json(['data' => $servers]);
    }

    public function store(Request $request, McpManager $manager): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'transport' => 'in:stdio,sse,streamable-http',
            'command' => 'nullable|string',
            'args' => 'nullable|array',
            'env' => 'nullable|array',
            'url' => 'nullable|url',
        ]);

        $server = $manager->register(
            name: $validated['name'],
            transport: $validated['transport'] ?? 'stdio',
            command: $validated['command'] ?? null,
            args: $validated['args'] ?? null,
            env: $validated['env'] ?? null,
            url: $validated['url'] ?? null,
        );

        return response()->json(['data' => $server], 201);
    }

    public function show(McpServer $mcpServer): JsonResponse
    {
        return response()->json(['data' => $mcpServer]);
    }

    public function destroy(McpServer $mcpServer, McpManager $manager): JsonResponse
    {
        $manager->remove($mcpServer->id);

        return response()->noContent();
    }
}
