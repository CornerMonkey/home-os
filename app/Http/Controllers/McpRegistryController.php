<?php

namespace App\Http\Controllers;

use App\Services\McpManager;
use App\Services\McpRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class McpRegistryController extends Controller
{
    public function search(Request $request, McpRegistry $registry): JsonResponse
    {
        $query = $request->input('q', '');

        $results = $registry->search($query);

        return response()->json(['data' => $results]);
    }

    public function install(Request $request, McpRegistry $registry, McpManager $manager): JsonResponse
    {
        $validated = $request->validate([
            'package' => 'required|string',
        ]);

        $server = $registry->install($validated['package'], $manager);

        return response()->json(['data' => $server], 201);
    }
}
