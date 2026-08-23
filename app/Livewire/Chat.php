<?php

namespace App\Livewire;

use App\Ai\Agents\HomeAgent;
use App\Models\Conversation;
use App\Models\Message;
use Livewire\Component;

class Chat extends Component
{
    public ?Conversation $conversation = null;
    public string $message = '';
    public array $messages = [];
    public bool $isStreaming = false;
    public string $streamedContent = '';
    public array $activeToolCalls = [];
    public bool $awaitingConfirmation = false;
    public ?string $pendingAction = null;

    public function mount(): void
    {
        $this->conversation = Conversation::firstOrCreate(
            ['title' => 'New Conversation'],
        );

        $this->messages = $this->conversation->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn ($msg) => [
                'role' => $msg->role,
                'content' => $msg->content,
            ])
            ->toArray();
    }

    public function sendMessage(): void
    {
        if (empty(trim($this->message))) {
            return;
        }

        $userMessage = $this->message;
        $this->message = '';

        // Store user message
        Message::create([
            'conversation_id' => $this->conversation->id,
            'role' => 'user',
            'content' => $userMessage,
        ]);

        $this->messages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        // Stream response from agent
        $this->isStreaming = true;
        $this->streamedContent = '';
        $this->activeToolCalls = [];

        try {
            $agent = new HomeAgent();
            $response = $agent->prompt($userMessage);

            $content = (string) $response;

            // Store assistant message
            Message::create([
                'conversation_id' => $this->conversation->id,
                'role' => 'assistant',
                'content' => $content,
            ]);

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $content,
            ];
        } catch (\Throwable $e) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Error: ' . $e->getMessage(),
            ];
        }

        $this->isStreaming = false;
        $this->streamedContent = '';
    }

    public function confirmAction(): void
    {
        if ($this->pendingAction) {
            $this->message = $this->pendingAction;
            $this->pendingAction = null;
            $this->awaitingConfirmation = false;
            $this->sendMessage();
        }
    }

    public function cancelAction(): void
    {
        $this->pendingAction = null;
        $this->awaitingConfirmation = false;
        $this->messages[] = [
            'role' => 'assistant',
            'content' => 'Action cancelled.',
        ];
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
