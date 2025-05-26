<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;

class TodoController extends Controller
{
    //タスクの一覧を表示
    public function index(Request $request)
    {
        $query = Todo::orderByRaw('due_date IS NULL , due_date ASC');

        $filter = $request->query('filter');

        if ($filter === 'incomplete') {
            $query->where('completed', false);
        } elseif ($filter === 'complete') {
            $query->where('completed', true);
        }

        $todos = $query->get();

        return view('todos.index', compact('todos'));
    }

    //新しいタスクを保存
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:高,中,低',
        ]);

        Todo::create([
            'title' => $request->title,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
        ]);

        return redirect()->route('todos.index');
    }

    //編集画面を表示
    public function edit($id)
    {
        $todo = Todo::findOrFail($id);
        return view('todos.edit', compact('todo'));
    }

    //編集内容を更新
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:高,中,低',
        ]);

        $todo = Todo::findOrFail($id);
        $todo->update([
            'title' => $request->title,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
        ]);

        return redirect()->route('todos.index');
    }

    //タスクを削除
    public function destroy($id)
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();

        return redirect()->route('todos.index');
    }

    //チェックの切り替え
    public function toggle(Todo $todo)
    {
        $todo->completed = !$todo->completed;
        $todo->save();

        return redirect()->back();
    }
}
