<?php

use App\Http\Controllers\McpServerController;
use Illuminate\Support\Facades\Route;

Route::apiResource('mcp-servers', McpServerController::class);
