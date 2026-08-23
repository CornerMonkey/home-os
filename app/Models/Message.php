<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'tool_calls',
        'tool_results',
        'tokens_used',
    ];

    protected function casts(): array
    {
        return [
            'tool_calls' => 'array',
            'tool_results' => 'array',
            'tokens_used' => 'array',
        ];
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
