@extends('layouts.app')

@section('content')
<div class="container" style="max-width:600px;">
    <h4 class="mb-3">Add Post</h4>

    <div class="card shadow-sm p-4">
        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title') }}" placeholder="Enter title">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Content</label>
                <textarea name="content" rows="4"
                          class="form-control @error('content') is-invalid @enderror"
                          placeholder="Enter content">{{ old('content') }}</textarea>
                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Category</label>
                <input type="text" name="category" list="cat-list"
                       class="form-control @error('category') is-invalid @enderror"
                       value="{{ old('category') }}" placeholder="e.g. Tech, News">
                <datalist id="cat-list">
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">
                    @endforeach
                </datalist>
                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="published" {{ old('status','published') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft"     {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Image</label>
                <input type="file" name="image" accept="image/*"
                       class="form-control @error('image') is-invalid @enderror"
                       onchange="previewImg(this)">
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <img id="img-preview" src="#" class="mt-2 d-none rounded" style="max-height:120px;">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ route('posts.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewImg(input) {
    const preview = document.getElementById('img-preview');
    if (input.files && input.files[0]) {
        preview.src = URL.createObjectURL(input.files[0]);
        preview.classList.remove('d-none');
    }
}
</script>
@endsection
