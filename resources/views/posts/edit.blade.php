@extends('layouts.app')

@section('content')

<div class="container" style="max-width:600px;">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4 class="mb-0">
            Edit Post
        </h4>

        <span class="unsaved-indicator">
            ● Unsaved changes
        </span>

    </div>


    <div class="card shadow-sm p-4">

        <form
            action="{{ route('posts.update', $post->id) }}"
            method="POST"
            enctype="multipart/form-data"
            class="unsaved-changes-form"
        >

            @csrf

            @method('PUT')


            <!-- Title -->

            <div class="mb-3">

                <label class="form-label">

                    Title

                    <span class="text-danger">
                        *
                    </span>

                </label>


                <input
                    type="text"
                    name="title"
                    class="form-control @error('title') is-invalid @enderror"
                    value="{{ old('title', $post->title) }}"
                >


                @error('title')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                @enderror

            </div>


            <!-- Content -->

            <div class="mb-3">

                <label class="form-label">
                    Content
                </label>


                <textarea
                    name="content"
                    rows="4"
                    class="form-control @error('content') is-invalid @enderror"
                >{{ old('content', $post->content) }}</textarea>


                @error('content')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                @enderror

            </div>


            <!-- Category -->

            <div class="mb-3">

                <label class="form-label">
                    Category
                </label>


                <input
                    type="text"
                    name="category"
                    list="cat-list"
                    class="form-control @error('category') is-invalid @enderror"
                    value="{{ old('category', $post->category) }}"
                >


                <datalist id="cat-list">

                    @foreach($categories as $cat)

                        <option value="{{ $cat }}">

                    @endforeach

                </datalist>


                @error('category')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                @enderror

            </div>


            <!-- Status -->

            <div class="mb-3">

                <label class="form-label">
                    Status
                </label>


                <select
                    name="status"
                    class="form-select @error('status') is-invalid @enderror"
                >

                    <option
                        value="published"
                        {{ old('status', $post->status) === 'published'
                            ? 'selected'
                            : '' }}
                    >
                        Published
                    </option>


                    <option
                        value="draft"
                        {{ old('status', $post->status) === 'draft'
                            ? 'selected'
                            : '' }}
                    >
                        Draft
                    </option>

                </select>


                @error('status')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                @enderror

            </div>


            <!-- Image -->

            <div class="mb-3">

                <label class="form-label">
                    Image
                </label>


                @if($post->image)

                    <div class="mb-2">

                        <img
                            src="{{ asset('storage/'.$post->image) }}"
                            id="img-preview"
                            style="max-height:120px;"
                            class="rounded"
                        >

                    </div>

                @else

                    <img
                        id="img-preview"
                        src="#"
                        class="mt-2 d-none rounded mb-2"
                        style="max-height:120px;"
                    >

                @endif


                <input
                    type="file"
                    name="image"
                    accept="image/*"
                    class="form-control @error('image') is-invalid @enderror"
                    onchange="previewImg(this)"
                >


                @error('image')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                @enderror

            </div>


            <!-- Buttons -->

            <div class="d-flex gap-2">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Update
                </button>


                <a
                    href="{{ route('posts.index') }}"
                    class="btn btn-secondary"
                >
                    Back
                </a>

            </div>

        </form>

    </div>

</div>


<script>

/*
|--------------------------------------------------------------------------
| Image Preview
|--------------------------------------------------------------------------
*/

function previewImg(input) {

    const preview =
        document.getElementById('img-preview');


    if (
        input.files &&
        input.files[0]
    ) {

        preview.src =
            URL.createObjectURL(
                input.files[0]
            );

        preview.classList.remove(
            'd-none'
        );

    }

}

</script>

@endsection