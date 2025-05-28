<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use App\Models\Tag;

class TodoController extends Controller
{
    //タスクの一覧を表示
    public function index(Request $request)
    {
        //並び順：優先度→締切日
        $query = Todo::orderByRaw("priority IS NULL, FIELD(priority, '高', '中', '低')")
            ->orderByRaw('due_date IS NULL, due_date ASC');

        //検索キーワード変数
        $keyword = $request->search;

        //フィルター変数
        $filter = $request->query('filter');

        //フィルター：未完了or完了
        if ($filter === 'incomplete') {
            $query->where('completed', false);
        } elseif ($filter === 'complete') {
            $query->where('completed', true);
        }

        //フィルター：優先度（高・中・低）
        if ($request->filled('priority')) {
            $query->where('priority', $request->query('priority'));
        }

        //キーワードを含んだタイトルを取得
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$keyword}%");
        }

        $todos = $query->paginate(10)->withQueryString();

        $allTags = Tag::all();
        return view('todos.index', compact('todos', 'allTags'));
    }

    //新しいタスクを保存
    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required|max:255',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:高,中,低',
        ]);

        $todo = Todo::create([
            'title' => $request->title,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
        ]);

        //タグIDを取得（空でもOK）
        $tagIds = $request->input('tags', []);

        //タグを関連付ける（中間テーブルに保存）
        $todo->tags()->sync($tagIds);

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
