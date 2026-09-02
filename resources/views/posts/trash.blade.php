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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>🗑 Trash</h4>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary btn-sm">← Back to Posts</a>
    </div>

    <div class="card shadow-sm">
        <table class="table table-bordered mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Deleted At</th>
                    <th width="200">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>
                    <td>{{ $post->title }}</td>
                    <td>{{ $post->deleted_at->format('d M Y, h:i A') }}</td>
                    <td>
                        {{-- Restore --}}
                        <form action="{{ route('posts.restore', $post->id) }}" method="POST" class="no-spinner d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm">Restore</button>
                        </form>

                        {{-- Permanent Delete --}}
                        <button class="btn btn-danger btn-sm" onclick="forceDelete({{ $post->id }})">Delete Forever</button>
                        <form id="force-form-{{ $post->id }}" action="{{ route('posts.forceDelete', $post->id) }}"
                              method="POST" class="no-spinner d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted">Trash is empty.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $posts->links() }}</div>
</div>

<script>
function forceDelete(id) {
    Swal.fire({
        title: 'Permanently delete?',
        text: 'This cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete forever'
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('force-form-' + id).submit();
        }
    });
}
</script>
@endsection
