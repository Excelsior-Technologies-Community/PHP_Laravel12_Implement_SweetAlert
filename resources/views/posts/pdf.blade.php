<!DOCTYPE html>
<html>
<head>
    <title>Posts Export</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 10px; text-align: left; }
        th { background: #333; color: #fff; }
        tr:nth-child(even) { background: #f5f5f5; }
        h2 { margin-bottom: 10px; }
        .badge-pub { color: green; font-weight: bold; }
        .badge-draft { color: gray; }
    </style>
</head>
<body onload="window.print()">
    <h2>Posts List — {{ now()->format('d M Y') }}</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Content</th>
                <th>Category</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $post)
            <tr>
                <td>{{ $post->id }}</td>
                <td>{{ $post->title }}</td>
                <td>{{ Str::limit($post->content, 80) }}</td>
                <td>{{ $post->category ?? '—' }}</td>
                <td class="{{ $post->status === 'published' ? 'badge-pub' : 'badge-draft' }}">
                    {{ ucfirst($post->status) }}
                </td>
                <td>{{ $post->created_at->format('d M Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
