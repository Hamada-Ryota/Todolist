<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>Todoアプリ</title>
    <style>
        body {
            font-family: sans-serif;
            max-width: 600px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h1 {
            text-align: center;
        }

        form {
            margin-bottom: 20px;
        }

        ul {
            padding-left: 0;
        }

        li {
            list-style: none;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .actions {
            display: inline-flex;
            gap: 8px;
            align-items: center;
        }

        button {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
        }
    </style>
</head>

<body>
    <h1>Todoアプリ</h1>

    <!-- タイトル検索機能 -->
    <form action="{{ route('todos.index') }}" method="GET">
        <input type="hidden" name="filter" value="{{ request('filter') }}">
        <input type="hidden" name="priority" value="{{ request('priority') }}">
        <input type="text" name="search" placeholder="キーワードを入力" value="{{ request('search') }}">
        <button type="submit">検索</button>
        <a href="{{ route('todos.index') }}">検索をクリア</a>
    </form>
    @if ($todos->isEmpty())
        <p>該当するタスクは見つかりませんでした。</p>
    @endif

    <!-- 未完了フィルター -->
    <div style="margin-bottom: 20px;">
        <a style ="{{ request()->missing('filter') ? 'color:black; font-weight:bold;' : '' }}"
            href="{{ route('todos.index') }}">すべて表示</a> |
        <a style ="{{ request('filter') === 'incomplete' ? 'color:blue; font-weight:bold;' : '' }}"
            href="{{ route('todos.index', ['filter' => 'incomplete']) }}">未完了のみ</a> |
        <a style ="{{ request('filter') === 'complete' ? 'color:green; font-weight:bold;' : '' }}"
            href="{{ route('todos.index', ['filter' => 'complete']) }}">完了済み</a>
    </div>
    <div style="margin-bottom: 10px;">
        <strong>優先度：</strong>
        <a style ="{{ request()->missing('filter') ? 'color:black; font-weight:bold;' : '' }}"
            href="{{ route('todos.index', request()->except('priority')) }}">すべて</a> |
        <a style ="{{ request('priority') === '高' ? 'color:red; font-weight:bold;' : '' }}"
            href="{{ route('todos.index', array_merge(request()->all(), ['priority' => '高'])) }}">高</a> |
        <a style ="{{ request('priority') === '中' ? 'color:orange; font-weight:bold;' : '' }}"
            href="{{ route('todos.index', array_merge(request()->all(), ['priority' => '中'])) }}">中</a> |
        <a style ="{{ request('priority') === '低' ? 'color:gray; font-weight:bold;' : '' }}"
            href="{{ route('todos.index', array_merge(request()->all(), ['priority' => '低'])) }}">低</a>
    </div>
    <!-- エラーメッセージ表示 -->
    @if ($errors->any())
        <div style="color:red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 新規タスク追加フォーム -->
    <form action="{{ route('todos.store') }}" method="POST">
        @csrf
        <input type="text" name="title" style="padding: 5px 10px;" placeholder="タスクを入力"
            value="{{ old('title') }}" required>
        <input type="date" name="due_date" value="{{ old('due_date') }}">
        <label for="priority">優先度</label>
        <select name="priority" id="priority" required>
            <option value="高" {{ old('priority') == '高' ? 'selected' : '' }}>高</option>
            <option value="中" {{ old('priority', '中') == '中' ? 'selected' : '' }}>中</option>
            <option value="低" {{ old('priority') == '低' ? 'selected' : '' }}>低</option>
        </select>
        <label for="tags">タグ</label>
        <select name="tags" id="tags" required>
            @foreach ($allTags as $tag)
                <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
                    {{ $tag->name }}</option>
            @endforeach
        </select>
        <button type="submit">追加</button>
    </form>

    <!-- タスク一覧表示 -->
    <ul>
        @forelse ($todos as $todo)
            <li>
                <div style="display: flex; align-items: center; gap: 10px; width: 100%;">
                    <!-- チェックフォーム -->
                    <form action="{{ route('todos.toggle', $todo->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="checkbox" onchange="this.form.submit()" {{ $todo->completed ? 'checked' : '' }}>
                    </form>

                    <!-- タイトル -->
                    <span style="{{ $todo->completed ? 'text-decoration: line-through; color: gray;' : '' }}">
                        {{ $todo->title }}
                    </span>

                    <!-- 締切日 -->
                    @php
                        $isOverdue =
                            $todo->due_date && \Carbon\Carbon::parse($todo->due_date)->lt(\Carbon\Carbon::today());
                    @endphp

                    <span style="{{ $isOverdue ? 'color: red; font-weight: bold;' : '' }}">
                        @if ($todo->due_date)
                            締切： {{ \Carbon\Carbon::parse($todo->due_date)->format('Y年m月d日') }}
                        @else
                            締切：未設定
                        @endif
                    </span>

                    <!-- 優先度色分け -->
                    @php
                        $priorityColor = match ($todo->priority) {
                            '高' => 'red',
                            '中' => 'orange',
                            '低' => 'gray',
                            default => 'black',
                        };
                    @endphp

                    <span style="color: {{ $priorityColor }};">
                        優先度：{{ $todo->priority }}
                    </span>

                    <!-- 編集・削除 -->
                    <span class="actions">
                        <a href="{{ route('todos.edit', $todo->id) }}">編集</a>

                        <form action="{{ route('todos.destroy', $todo->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">削除</button>
                        </form>
                    </span>
                </div>
            </li>
        @empty
            <li>タスクはまだありません。</li>
        @endforelse
    </ul>
</body>

</html>
