<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Jobs\ProcessTask;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $tasks = Task::all();
        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255'
        ]);

        $task = Task::create([
            'title' => $request->title,
            'status' => 'pending'
        ]);

        // Atualiza o status para processing
        $task->update(['status' => 'processing']);

        // Dispara o job para processar a task APENAS se não estiver done
        if ($task->status !== 'done') {
            ProcessTask::dispatch($task->id);
        }

        return response()->json($task, 201);
    }
}
