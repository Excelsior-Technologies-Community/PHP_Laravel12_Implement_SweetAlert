@extends('layouts.app')

@section('content')

<div class="container py-4">

    {{-- =========================================================
         Custom Styling
    ========================================================== --}}
    <style>
        .page-header {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            color: #fff;
            border-radius: 18px;
            padding: 25px 30px;
            margin-bottom: 25px;
        }

        .page-header h2 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-card {
            border-radius: 15px;
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .filter-card {
            border: 0;
            border-radius: 15px;
        }

        .table-card {
            border: 0;
            border-radius: 15px;
            overflow: hidden;
        }

        .table thead th {
            white-space: nowrap;
            vertical-align: middle;
        }

        .table tbody td {
            vertical-align: middle;
        }

        .table tbody tr {
            transition: background 0.15s ease;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .post-image {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #dee2e6;
        }

        .status-toggle-btn {
            cursor: pointer;
            padding: 7px 12px;
            border-radius: 20px;
        }

        .bulk-toolbar {
            border-radius: 12px;
        }

        .pagination {
            gap: 6px;
        }

        .pagination .page-item .page-link {
            border-radius: 8px;
            min-width: 40px;
            text-align: center;
            font-weight: 600;
            border: 1px solid #dee2e6;
            color: #495057;
        }

        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }

        .pagination .page-item .page-link:hover {
            background-color: #e9ecef;
        }

        .pagination .page-item.active .page-link:hover {
            background-color: #0d6efd;
            color: #fff;
        }

        .sort-link {
            color: #fff !important;
            text-decoration: none;
        }

        .sort-link:hover {
            color: #e9ecef !important;
        }
    </style>


    {{-- =========================================================
         Header
    ========================================================== --}}
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

        <div>
            <h2>Posts Management</h2>
            <div class="opacity-75">
                Manage your posts, status and content
            </div>
        </div>

        <a
            href="{{ route('posts.create') }}"
            class="btn btn-light fw-semibold px-4"
        >
            + Add Post
        </a>

    </div>


    {{-- =========================================================
         Success Message
    ========================================================== --}}
    @if(session('success'))

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                Toast.fire({
                    icon: 'success',
                    title: @json(session('success'))
                });

            });
        </script>

    @endif


    {{-- =========================================================
         Statistics
    ========================================================== --}}
    <div class="row g-3 mb-4">

        {{-- Total --}}
        <div class="col-sm-6 col-lg-3">

            <div class="card shadow-sm border-0 stat-card h-100">

                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        <small class="text-muted">
                            Total Posts
                        </small>

                        <h3 class="mb-0 fw-bold">
                            {{ $totalPosts }}
                        </h3>
                    </div>

                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        📝
                    </div>

                </div>

            </div>

        </div>


        {{-- Published --}}
        <div class="col-sm-6 col-lg-3">

            <div class="card shadow-sm border-0 stat-card h-100">

                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        <small class="text-muted">
                            Published
                        </small>

                        <h3 class="mb-0 fw-bold text-success">
                            {{ $publishedPosts }}
                        </h3>
                    </div>

                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        ✓
                    </div>

                </div>

            </div>

        </div>


        {{-- Draft --}}
        <div class="col-sm-6 col-lg-3">

            <div class="card shadow-sm border-0 stat-card h-100">

                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        <small class="text-muted">
                            Draft
                        </small>

                        <h3 class="mb-0 fw-bold text-secondary">
                            {{ $draftPosts }}
                        </h3>
                    </div>

                    <div class="stat-icon bg-secondary bg-opacity-10 text-secondary">
                        ✎
                    </div>

                </div>

            </div>

        </div>


        {{-- Trash --}}
        <div class="col-sm-6 col-lg-3">

            <div class="card shadow-sm border-0 stat-card h-100">

                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        <small class="text-muted">
                            Trash
                        </small>

                        <h3 class="mb-0 fw-bold text-danger">
                            {{ $trashPosts }}
                        </h3>
                    </div>

                    <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                        🗑
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         Search & Filters
    ========================================================== --}}
    <div class="card shadow-sm filter-card mb-3">

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('posts.index') }}"
                class="row g-2 no-spinner"
            >

                {{-- Search --}}
                <div class="col-lg-3 col-md-6">

                    <div class="input-group">

                        <span class="input-group-text bg-white">
                            🔍
                        </span>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search ID, title or content..."
                            value="{{ request('search') }}"
                        >

                    </div>

                </div>


                {{-- Category --}}
                <div class="col-lg-2 col-md-6">

                    <select
                        name="category"
                        class="form-select"
                    >

                        <option value="">
                            All Categories
                        </option>

                        @foreach($categories as $cat)

                            <option
                                value="{{ $cat }}"
                                {{ request('category') == $cat ? 'selected' : '' }}
                            >
                                {{ $cat }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Status --}}
                <div class="col-lg-2 col-md-6">

                    <select
                        name="status"
                        class="form-select"
                    >

                        <option value="">
                            All Status
                        </option>

                        <option
                            value="published"
                            {{ request('status') == 'published' ? 'selected' : '' }}
                        >
                            Published
                        </option>

                        <option
                            value="draft"
                            {{ request('status') == 'draft' ? 'selected' : '' }}
                        >
                            Draft
                        </option>

                    </select>

                </div>


                {{-- From Date --}}
                <div class="col-lg-2 col-md-6">

                    <input
                        type="date"
                        name="from_date"
                        class="form-control"
                        value="{{ request('from_date') }}"
                        title="From Date"
                    >

                </div>


                {{-- To Date --}}
                <div class="col-lg-2 col-md-6">

                    <input
                        type="date"
                        name="to_date"
                        class="form-control"
                        value="{{ request('to_date') }}"
                        title="To Date"
                    >

                </div>


                {{-- Search Button --}}
                <div class="col-lg-1 col-md-6">

                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                    >
                        Search
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
         Filter Controls
    ========================================================== --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">

        <a
            href="{{ route('posts.index') }}"
            class="btn btn-outline-secondary btn-sm"
        >
            ↻ Reset Filters
        </a>


        <div class="d-flex align-items-center gap-2">

            <span class="small text-muted">
                Per Page
            </span>

            <form
                method="GET"
                action="{{ route('posts.index') }}"
                class="no-spinner"
            >

                @foreach(request()->except('per_page', 'page') as $key => $value)

                    <input
                        type="hidden"
                        name="{{ $key }}"
                        value="{{ $value }}"
                    >

                @endforeach

                <select
                    name="per_page"
                    class="form-select form-select-sm"
                    onchange="this.form.submit()"
                >

                    @foreach([5, 10, 25, 50] as $number)

                        <option
                            value="{{ $number }}"
                            {{ $perPage == $number ? 'selected' : '' }}
                        >
                            {{ $number }}
                        </option>

                    @endforeach

                </select>

            </form>

        </div>

    </div>


    {{-- =========================================================
         Export Bar
    ========================================================== --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">

        <div>
            <span class="text-muted small">
                Showing
                <strong>{{ $posts->firstItem() ?? 0 }}</strong>
                -
                <strong>{{ $posts->lastItem() ?? 0 }}</strong>
                of
                <strong>{{ $posts->total() }}</strong>
                posts
            </span>
        </div>


        <div class="d-flex gap-2">

            <a
                href="{{ route('posts.exportCsv', request()->query()) }}"
                class="btn btn-outline-success btn-sm"
            >
                ↓ Filtered CSV
            </a>

            <a
                href="{{ route('posts.exportPdf') }}"
                class="btn btn-outline-danger btn-sm"
                target="_blank"
            >
                ↓ PDF
            </a>

        </div>

    </div>


    {{-- =========================================================
         Bulk Toolbar
    ========================================================== --}}
    <div
        id="bulk-toolbar"
        class="bulk-toolbar alert alert-primary align-items-center justify-content-between mb-3"
        style="display: none;"
    >

        <div>

            <strong>
                <span id="selected-count">
                    0
                </span>
            </strong>

            post(s) selected.

        </div>


        <div class="d-flex gap-2 flex-wrap">

            <button
                type="button"
                class="btn btn-success btn-sm"
                onclick="bulkUpdateStatus('published')"
            >
                ✓ Publish
            </button>


            <button
                type="button"
                class="btn btn-secondary btn-sm"
                onclick="bulkUpdateStatus('draft')"
            >
                Draft
            </button>


            <button
                type="button"
                class="btn btn-danger btn-sm"
                onclick="bulkDeletePosts()"
            >
                🗑 Delete
            </button>


            <button
                type="button"
                class="btn btn-light btn-sm"
                onclick="clearSelection()"
            >
                Clear
            </button>

        </div>

    </div>


    {{-- =========================================================
         Posts Table
    ========================================================== --}}
    <div class="card shadow-sm table-card">

        <div class="table-responsive">

            <table class="table table-bordered mb-0">

                <thead class="table-dark">

                    <tr>

                        {{-- Select --}}
                        <th width="45" class="text-center">

                            <input
                                type="checkbox"
                                id="select-all"
                                class="form-check-input"
                                onchange="toggleSelectAll(this)"
                            >

                        </th>


                        {{-- ID --}}
                        <th>

                            <a
                                href="{{ request()->fullUrlWithQuery([
                                    'sort' => 'id',
                                    'direction' => ($sort === 'id' && $direction === 'asc') ? 'desc' : 'asc',
                                    'page' => 1
                                ]) }}"
                                class="sort-link"
                            >

                                #

                                @if($sort === 'id')
                                    {{ $direction === 'asc' ? '↑' : '↓' }}
                                @endif

                            </a>

                        </th>


                        {{-- Image --}}
                        <th>
                            Image
                        </th>


                        {{-- Title --}}
                        <th>

                            <a
                                href="{{ request()->fullUrlWithQuery([
                                    'sort' => 'title',
                                    'direction' => ($sort === 'title' && $direction === 'asc') ? 'desc' : 'asc',
                                    'page' => 1
                                ]) }}"
                                class="sort-link"
                            >

                                Title

                                @if($sort === 'title')
                                    {{ $direction === 'asc' ? '↑' : '↓' }}
                                @endif

                            </a>

                        </th>


                        {{-- Category --}}
                        <th>

                            <a
                                href="{{ request()->fullUrlWithQuery([
                                    'sort' => 'category',
                                    'direction' => ($sort === 'category' && $direction === 'asc') ? 'desc' : 'asc',
                                    'page' => 1
                                ]) }}"
                                class="sort-link"
                            >

                                Category

                                @if($sort === 'category')
                                    {{ $direction === 'asc' ? '↑' : '↓' }}
                                @endif

                            </a>

                        </th>


                        {{-- Status --}}
                        <th>

                            <a
                                href="{{ request()->fullUrlWithQuery([
                                    'sort' => 'status',
                                    'direction' => ($sort === 'status' && $direction === 'asc') ? 'desc' : 'asc',
                                    'page' => 1
                                ]) }}"
                                class="sort-link"
                            >

                                Status

                                @if($sort === 'status')
                                    {{ $direction === 'asc' ? '↑' : '↓' }}
                                @endif

                            </a>

                        </th>


                        {{-- Created --}}
                        <th>

                            <a
                                href="{{ request()->fullUrlWithQuery([
                                    'sort' => 'created_at',
                                    'direction' => ($sort === 'created_at' && $direction === 'asc') ? 'desc' : 'asc',
                                    'page' => 1
                                ]) }}"
                                class="sort-link"
                            >

                                Created

                                @if($sort === 'created_at')
                                    {{ $direction === 'asc' ? '↑' : '↓' }}
                                @endif

                            </a>

                        </th>


                        <th width="220">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($posts as $post)

                        <tr id="post-row-{{ $post->id }}">

                            {{-- Checkbox --}}
                            <td class="text-center">

                                <input
                                    type="checkbox"
                                    class="form-check-input post-checkbox"
                                    value="{{ $post->id }}"
                                    onchange="updateBulkSelection()"
                                >

                            </td>


                            {{-- ID --}}
                            <td>
                                <strong>
                                    {{ $post->id }}
                                </strong>
                            </td>


                            {{-- Image --}}
                            <td>

                                @if($post->image)

                                    <img
                                        src="{{ asset('storage/' . $post->image) }}"
                                        class="post-image"
                                        alt="Post Image"
                                    >

                                @else

                                    <span class="text-muted">
                                        —
                                    </span>

                                @endif

                            </td>


                            {{-- Title --}}
                            <td>

                                <strong>
                                    {{ $post->title }}
                                </strong>

                                @if($post->content)

                                    <br>

                                    <small class="text-muted">

                                        {{ \Illuminate\Support\Str::limit($post->content, 60) }}

                                    </small>

                                @endif

                            </td>


                            {{-- Category --}}
                            <td>

                                @if($post->category)

                                    <span class="badge bg-light text-dark border">
                                        {{ $post->category }}
                                    </span>

                                @else

                                    —

                                @endif

                            </td>


                            {{-- Status --}}
                            <td>

                                <button
                                    type="button"
                                    id="status-btn-{{ $post->id }}"
                                    class="badge border-0 status-toggle-btn
                                        {{ $post->status === 'published'
                                            ? 'bg-success'
                                            : 'bg-secondary' }}"
                                    onclick="togglePostStatus({{ $post->id }})"
                                >

                                    <span id="status-text-{{ $post->id }}">
                                        {{ ucfirst($post->status) }}
                                    </span>

                                </button>

                            </td>


                            {{-- Created --}}
                            <td>

                                <small class="text-muted">

                                    {{ $post->created_at->format('d M Y') }}

                                    <br>

                                    {{ $post->created_at->format('h:i A') }}

                                </small>

                            </td>


                            {{-- Actions --}}
                            <td>

                                <div class="d-flex gap-1 flex-wrap">

                                    <a
                                        href="{{ route('posts.edit', $post->id) }}"
                                        class="btn btn-warning btn-sm"
                                    >
                                        Edit
                                    </a>


                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm"
                                        onclick="deletePost({{ $post->id }})"
                                    >
                                        Delete
                                    </button>

                                </div>


                                <form
                                    id="delete-form-{{ $post->id }}"
                                    action="{{ route('posts.delete', $post->id) }}"
                                    method="POST"
                                    class="no-spinner d-none"
                                >

                                    @csrf

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="text-center py-5"
                            >

                                <div class="fs-1 mb-2">
                                    📝
                                </div>

                                <h5 class="text-muted">
                                    No posts found
                                </h5>

                                <p class="text-muted mb-0">
                                    Try changing your search or filters.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- =========================================================
         NUMERIC ONLY PAGINATION
         No Previous / Next
    ========================================================== --}}
    @if($posts->hasPages())

        <div class="d-flex justify-content-center mt-4">

            <nav aria-label="Posts pagination">

                <ul class="pagination mb-0">

                    @for($page = 1; $page <= $posts->lastPage(); $page++)

                        <li
                            class="page-item {{ $page == $posts->currentPage() ? 'active' : '' }}"
                        >

                            <a
                                class="page-link"
                                href="{{ $posts->url($page) }}"
                            >
                                {{ $page }}
                            </a>

                        </li>

                    @endfor

                </ul>

            </nav>

        </div>

    @endif

</div>


{{-- =========================================================
     JavaScript
========================================================== --}}
<script>

/*
|--------------------------------------------------------------------------
| Individual Delete
|--------------------------------------------------------------------------
*/

function deletePost(id) {

    Swal.fire({

        title: 'Delete this post?',

        text: 'The post will be moved to trash.',

        icon: 'warning',

        showCancelButton: true,

        confirmButtonColor: '#d33',

        cancelButtonColor: '#6c757d',

        confirmButtonText: 'Yes, delete it!',

        cancelButtonText: 'Cancel',

        reverseButtons: true

    }).then(function (result) {

        if (result.isConfirmed) {

            document
                .getElementById('delete-form-' + id)
                .submit();

        }

    });

}


/*
|--------------------------------------------------------------------------
| Status Toggle
|--------------------------------------------------------------------------
*/

async function togglePostStatus(id) {

    const button =
        document.getElementById('status-btn-' + id);

    const text =
        document.getElementById('status-text-' + id);

    const currentStatus =
        text.textContent.trim().toLowerCase();

    const newStatus =
        currentStatus === 'published'
            ? 'draft'
            : 'published';


    const result = await Swal.fire({

        title: 'Change post status?',

        html:
            'Change status from <strong>' +
            currentStatus.toUpperCase() +
            '</strong> to <strong>' +
            newStatus.toUpperCase() +
            '</strong>?',

        icon: 'question',

        showCancelButton: true,

        confirmButtonText: 'Yes, change it',

        cancelButtonText: 'Cancel',

        reverseButtons: true

    });


    if (!result.isConfirmed) {
        return;
    }


    try {

        button.disabled = true;


        const response = await fetch(
            "{{ url('/posts/toggle-status') }}/" + id,
            {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }
        );


        const data = await response.json();


        if (!response.ok || !data.success) {

            throw new Error(
                data.message ||
                'Unable to update status.'
            );

        }


        text.textContent =
            data.status.charAt(0).toUpperCase() +
            data.status.slice(1);


        button.classList.remove(
            'bg-success',
            'bg-secondary'
        );


        button.classList.add(
            data.status === 'published'
                ? 'bg-success'
                : 'bg-secondary'
        );


        Toast.fire({
            icon: 'success',
            title: data.message
        });


    } catch (error) {

        Swal.fire({

            icon: 'error',

            title: 'Status Update Failed',

            text:
                error.message ||
                'Something went wrong.'

        });

    } finally {

        button.disabled = false;

    }

}


/*
|--------------------------------------------------------------------------
| Select All
|--------------------------------------------------------------------------
*/

function toggleSelectAll(checkbox) {

    document
        .querySelectorAll('.post-checkbox')
        .forEach(function (item) {

            item.checked = checkbox.checked;

        });


    updateBulkSelection();

}


/*
|--------------------------------------------------------------------------
| Bulk Selection
|--------------------------------------------------------------------------
*/

function updateBulkSelection() {

    const checkboxes =
        document.querySelectorAll('.post-checkbox');


    const selected =
        document.querySelectorAll(
            '.post-checkbox:checked'
        );


    const count =
        selected.length;


    document.getElementById(
        'selected-count'
    ).textContent = count;


    const toolbar =
        document.getElementById('bulk-toolbar');


    if (count > 0) {

        toolbar.style.display = 'flex';

    } else {

        toolbar.style.display = 'none';

    }


    const selectAll =
        document.getElementById('select-all');


    if (checkboxes.length === 0) {

        selectAll.checked = false;

        selectAll.indeterminate = false;

        return;

    }


    if (count === checkboxes.length) {

        selectAll.checked = true;

        selectAll.indeterminate = false;

    } else if (count > 0) {

        selectAll.checked = false;

        selectAll.indeterminate = true;

    } else {

        selectAll.checked = false;

        selectAll.indeterminate = false;

    }

}


/*
|--------------------------------------------------------------------------
| Clear Selection
|--------------------------------------------------------------------------
*/

function clearSelection() {

    document
        .querySelectorAll('.post-checkbox')
        .forEach(function (checkbox) {

            checkbox.checked = false;

        });


    document.getElementById(
        'select-all'
    ).checked = false;


    document.getElementById(
        'select-all'
    ).indeterminate = false;


    updateBulkSelection();

}


/*
|--------------------------------------------------------------------------
| Bulk Delete
|--------------------------------------------------------------------------
*/

async function bulkDeletePosts() {

    const selected =
        document.querySelectorAll(
            '.post-checkbox:checked'
        );


    const ids =
        Array.from(selected)
            .map(function (checkbox) {
                return checkbox.value;
            });


    if (ids.length === 0) {

        Swal.fire({

            icon: 'info',

            title: 'No posts selected',

            text: 'Please select at least one post.'

        });

        return;

    }


    const result = await Swal.fire({

        title: 'Delete selected posts?',

        html:
            'Move <strong>' +
            ids.length +
            '</strong> post(s) to trash?',

        icon: 'warning',

        showCancelButton: true,

        confirmButtonColor: '#d33',

        confirmButtonText: 'Yes, delete them',

        cancelButtonText: 'Cancel',

        reverseButtons: true

    });


    if (!result.isConfirmed) {
        return;
    }


    try {

        const data =
            await ajaxPost(
                "{{ route('posts.bulkDelete') }}",
                {
                    post_ids: ids
                }
            );


        ids.forEach(function (id) {

            const row =
                document.getElementById(
                    'post-row-' + id
                );


            if (row) {
                row.remove();
            }

        });


        clearSelection();


        Toast.fire({

            icon: 'success',

            title: data.message

        });


        setTimeout(function () {

            window.location.reload();

        }, 1200);


    } catch (error) {

        showAjaxError(error);

    }

}


/*
|--------------------------------------------------------------------------
| Bulk Status Update
|--------------------------------------------------------------------------
*/

async function bulkUpdateStatus(status) {

    const selected =
        document.querySelectorAll(
            '.post-checkbox:checked'
        );


    const ids =
        Array.from(selected)
            .map(function (checkbox) {
                return checkbox.value;
            });


    if (ids.length === 0) {

        Swal.fire({

            icon: 'info',

            title: 'No posts selected',

            text: 'Please select at least one post.'

        });

        return;

    }


    const result = await Swal.fire({

        title: 'Update status?',

        html:
            'Set <strong>' +
            ids.length +
            '</strong> selected post(s) to <strong>' +
            status.toUpperCase() +
            '</strong>?',

        icon: 'question',

        showCancelButton: true,

        confirmButtonText: 'Yes, update',

        cancelButtonText: 'Cancel',

        reverseButtons: true

    });


    if (!result.isConfirmed) {
        return;
    }


    try {

        const data =
            await ajaxPost(
                "{{ route('posts.bulkStatus') }}",
                {
                    post_ids: ids,
                    status: status
                }
            );


        Swal.fire({

            icon: 'success',

            title: 'Updated Successfully',

            text: data.message,

            timer: 1800,

            showConfirmButton: false

        });


        setTimeout(function () {

            window.location.reload();

        }, 1800);


    } catch (error) {

        showAjaxError(error);

    }

}


/*
|--------------------------------------------------------------------------
| Initial State
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function () {

        updateBulkSelection();

    }
);

</script>

@endsection