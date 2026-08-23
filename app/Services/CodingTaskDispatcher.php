<?php

namespace App\Services;

use App\Models\AgentTask;

class CodingTaskDispatcher
{
    public function dispatch(
        string $repo,
        string $prompt,
        ?string $conversationId = null,
        string $branchStrategy = 'pr',
    ): AgentTask {
        $task = AgentTask::create([
            'conversation_id' => $conversationId,
            'type' => 'coding_task',
            'target' => "github:{$repo}",
            'status' => 'pending',
            'input' => [
                'repo' => $repo,
                'prompt' => $prompt,
                'branch_strategy' => $branchStrategy,
            ],
        ]);

        // Dispatch to queue for background processing
        dispatch(function () use ($task) {
            $this->executeTask($task);
        });

        return $task;
    }

    protected function executeTask(AgentTask $task): void
    {
        $task->update(['status' => 'running']);

        try {
            $input = $task->input;

            // Execute via OpenCode CLI
            $process = new \Symfony\Component\Process\Process([
                'opencode',
                '--profile', 'headless',
                $input['prompt'],
            ], null, [
                'OPENCODE_GO_API_KEY' => config('ai.providers.opencode-go.key'),
            ]);

            $process->setTimeout(600); // 10 minutes
            $process->run();

            if ($process->isSuccessful()) {
                $task->update([
                    'status' => 'completed',
                    'output' => ['result' => $process->getOutput()],
                ]);
            } else {
                $task->update([
                    'status' => 'failed',
                    'error' => $process->getErrorOutput(),
                ]);
            }
        } catch (\Throwable $e) {
            $task->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
