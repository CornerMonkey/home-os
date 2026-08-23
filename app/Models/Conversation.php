<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'context',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
        ];
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function agentTasks()
    {
        return $this->hasMany(AgentTask::class);
    }
}
