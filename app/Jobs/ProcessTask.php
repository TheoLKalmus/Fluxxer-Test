<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTask implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $taskId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Busca a task atualizada
        $task = Task::find($this->taskId);
        
        // Verifica se a task existe e não está done
        if (!$task || $task->status === 'done') {
            return; // Não processa se já estiver done
        }

        sleep(5);

        // Atualiza o status para done
        $task->update(['status' => 'done']);
    }
}
