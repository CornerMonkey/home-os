<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class McpServer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'type',
        'transport',
        'command',
        'args',
        'env',
        'url',
        'status',
        'capabilities',
        'last_seen_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'args' => 'array',
            'env' => 'encrypted:array',
            'capabilities' => 'array',
            'metadata' => 'array',
            'last_seen_at' => 'datetime',
        ];
    }

    public function scopeConnected($query)
    {
        return $query->where('status', 'connected');
    }

    public function scopeDisconnected($query)
    {
        return $query->where('status', 'disconnected');
    }

    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }

    public function tools(): array
    {
        return $this->capabilities['tools'] ?? [];
    }
}
