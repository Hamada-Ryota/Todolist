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

    <!-- エラーメッセージ表示-->
    @if ($errors->any())
    <div style="color:red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- 新規タスク追加フォーム-->
     <form action="{{ route('todos.store') }}" method="POST">
        @csrf
        <input type="text" name="title" style="padding: 5px 10px;" placeholder="タスクを入力" value="{{ old('title' )}}" required>
        <button type="submit">追加</button>
     </form>

     <!-- タスク一覧表示 -->
     <ul>
        @forelse ($todos as $todo)
        <li>
            <span>{{ $todo->title }}</span>
            
            <span class="actions">
                <!-- 編集ボタン -->
                <a href="{{ route('todos.edit', $todo->id) }}">編集</a>

                <!-- 削除フォーム -->
                <form action="{{ route('todos.destroy', $todo->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">削除</button>
                </form>
            </span>
        </li>
        @empty
            <li>タスクはまだありません。</li>
        @endforelse
     </ul>
</body>
</html>
