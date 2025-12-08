@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h2>Posts List</h2>

    <!-- Button to open create form -->
    <a href="{{ route('posts.create') }}" class="btn btn-primary mb-3">Add Post</a>

    <!-- SweetAlert success message -->
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '{{ session("success") }}',  // Display success message
                timer: 1500,                      // Auto close after 1.5 sec
                showConfirmButton: false
            });
        </script>
    @endif

    <!-- Posts table -->
    <table class="table table-bordered bg-white">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th width="220">Actions</th>
        </tr>

        <!-- Loop through posts -->
        @foreach ($posts as $post)
        <tr>
            <td>{{ $post->id }}</td>
            <td>{{ $post->title }}</td>

            <td>
                <!-- Edit button -->
                <a href="{{ route('posts.edit', $post->id) }}"
                   class="btn btn-warning btn-sm">Edit</a>

                <!-- Delete button triggers SweetAlert -->
                <button class="btn btn-danger btn-sm"
                        onclick="deletePost({{ $post->id }})">
                    Delete
                </button>

                <!-- Hidden delete form -->
                <form id="delete-form-{{ $post->id }}"
                      action="{{ route('posts.delete', $post->id) }}"
                      method="POST"
                      style="display:none;">
                    @csrf
                </form>
            </td>
        </tr>
        @endforeach
    </table>

</div>

<!-- SweetAlert Delete Confirmation Script -->
<script>
function deletePost(id) {
    Swal.fire({
        title: 'Delete this post?',
        text: "You cannot undo this action!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete'
    }).then((result) => {

        // If user confirms deletion
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>

@endsection
