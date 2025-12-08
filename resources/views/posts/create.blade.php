@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h2>Add Post</h2>

    <!-- Create Post Form -->
    <form action="{{ route('posts.store') }}" method="POST">

        @csrf  <!-- Security token -->

        <!-- Input field for post title -->
        <input type="text" name="title" class="form-control mb-3"
               placeholder="Enter Title">

        <!-- Submit & Back Buttons -->
        <button class="btn btn-success">Save</button>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary">Back</a>
    </form>

</div>
@endsection
