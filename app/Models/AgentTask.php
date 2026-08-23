<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgentTask extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'conversation_id',
        'type',
        'target',
        'status',
        'input',
        'output',
        'error',
    ];

    protected function casts(): array
    {
        return [
            'input' => 'array',
            'output' => 'array',
        ];
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
