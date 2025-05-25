<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>タスク編集</title>
</head>
<body>
    <h1>タスクを編集</h1>

    @if ($errors->any())
        <div style="color:red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('todos.update', $todo->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="text" name="title" value="{{ old('title', $todo->title) }}">
        <input type="date" name="due_date" value="{{ old('due_date') }}">
        <button type="submit">更新</button>
    </form>

    <p><a href="{{ route('todos.index') }}">戻る</a></p>
</body>
</html>
