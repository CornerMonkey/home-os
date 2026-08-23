<?php

use App\Http\Controllers\McpRegistryController;
use App\Http\Controllers\McpServerController;
use Illuminate\Support\Facades\Route;

Route::apiResource('mcp-servers', McpServerController::class);

Route::get('/mcp-registry/search', [McpRegistryController::class, 'search']);
Route::post('/mcp-registry/install', [McpRegistryController::class, 'install']);
