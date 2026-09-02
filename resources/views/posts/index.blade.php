@extends('layouts.app')

@section('content')
<div class="container">

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Toast.fire({ icon: 'success', title: '{{ session("success") }}' });
        });
    </script>
    @endif

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('posts.index') }}" class="row g-2 mb-3 no-spinner">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search title or content..."
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft"     {{ request('status') == 'draft'      ? 'selected' : '' }}>Draft</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary">Search</button>
            <a href="{{ route('posts.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    {{-- Top bar --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <a href="{{ route('posts.create') }}" class="btn btn-success">+ Add Post</a>
        <div class="d-flex gap-2">
            <a href="{{ route('posts.exportCsv') }}" class="btn btn-outline-success btn-sm">⬇ CSV</a>
            <a href="{{ route('posts.exportPdf') }}" class="btn btn-outline-danger btn-sm" target="_blank">⬇ PDF</a>
        </div>
    </div>

    {{-- Posts Table --}}
    <div class="card shadow-sm">
        <table class="table table-bordered mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th width="220">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>
                    <td>
                        @if($post->image)
                            <img src="{{ asset('storage/'.$post->image) }}" width="50" height="50"
                                 style="object-fit:cover;border-radius:4px;">
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $post->title }}</strong>
                        @if($post->content)
                            <br><small class="text-muted">{{ Str::limit($post->content, 60) }}</small>
                        @endif
                    </td>
                    <td>{{ $post->category ?? '—' }}</td>
                    <td>
                        <form action="{{ route('posts.toggleStatus', $post->id) }}" method="POST" class="no-spinner d-inline">
                            @csrf
                            <button class="badge border-0 {{ $post->status === 'published' ? 'bg-success' : 'bg-secondary' }}"
                                    style="cursor:pointer;" title="Click to toggle">
                                {{ ucfirst($post->status) }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" onclick="deletePost({{ $post->id }})">Delete</button>
                        <form id="delete-form-{{ $post->id }}" action="{{ route('posts.delete', $post->id) }}"
                              method="POST" class="no-spinner d-none">
                            @csrf
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No posts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">{{ $posts->links() }}</div>

</div>

<script>
function deletePost(id) {
    Swal.fire({
        title: 'Delete this post?',
        text: 'It will be moved to trash.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete'
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
@endsection
