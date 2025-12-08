@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h2>Edit Post</h2>

    <!-- Update Post Form -->
    <form action="{{ route('posts.update', $post->id) }}" method="POST">

        @csrf
        @method('PUT') <!-- Spoof PUT request method -->

        <!-- Input field with old title value -->
        <input type="text" name="title" class="form-control mb-3"
               value="{{ $post->title }}">

        <!-- Submit & Back Buttons -->
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary">Back</a>
    </form>

</div>
@endsection
