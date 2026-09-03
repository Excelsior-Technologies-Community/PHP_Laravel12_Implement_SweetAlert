@extends('layouts.app')

@section('content')

<div class="container">

    <!-- SweetAlert Success -->
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


    <!-- ========================================================= -->
    <!-- Search & Filters -->
    <!-- ========================================================= -->

    <form
        method="GET"
        action="{{ route('posts.index') }}"
        class="row g-2 mb-3 no-spinner"
    >

        <div class="col-md-4">

            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search title or content..."
                value="{{ request('search') }}"
            >

        </div>


        <div class="col-md-3">

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


        <div class="col-md-2">

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


        <div class="col-md-3 d-flex gap-2">

            <button
                type="submit"
                class="btn btn-primary"
            >
                🔍 Search
            </button>

            <a
                href="{{ route('posts.index') }}"
                class="btn btn-secondary"
            >
                Reset
            </a>

        </div>

    </form>


    <!-- ========================================================= -->
    <!-- Top Bar -->
    <!-- ========================================================= -->

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <a
                href="{{ route('posts.create') }}"
                class="btn btn-success"
            >
                + Add Post
            </a>

        </div>


        <div class="d-flex gap-2">

            <a
                href="{{ route('posts.exportCsv') }}"
                class="btn btn-outline-success btn-sm"
            >
                ⬇ CSV
            </a>

            <a
                href="{{ route('posts.exportPdf') }}"
                class="btn btn-outline-danger btn-sm"
                target="_blank"
            >
                ⬇ PDF
            </a>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- Bulk Action Toolbar -->
    <!-- ========================================================= -->

    <div
        id="bulk-toolbar"
        class="bulk-toolbar alert alert-primary align-items-center justify-content-between mb-3"
    >

        <div>

            <strong>
                <span id="selected-count">0</span>
            </strong>

            post(s) selected.

        </div>


        <div class="d-flex gap-2">

            <button
                type="button"
                class="btn btn-danger btn-sm"
                onclick="bulkDeletePosts()"
            >
                🗑 Delete Selected
            </button>

            <button
                type="button"
                class="btn btn-secondary btn-sm"
                onclick="clearSelection()"
            >
                Clear
            </button>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- Posts Table -->
    <!-- ========================================================= -->

    <div class="card shadow-sm">

        <div class="table-responsive">

            <table class="table table-bordered mb-0">

                <thead class="table-dark">

                    <tr>

                        <th width="45">

                            <input
                                type="checkbox"
                                id="select-all"
                                class="form-check-input"
                                title="Select all"
                                onchange="toggleSelectAll(this)"
                            >

                        </th>

                        <th>#</th>

                        <th>Image</th>

                        <th>Title</th>

                        <th>Category</th>

                        <th>Status</th>

                        <th width="220">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($posts as $post)

                    <tr
                        id="post-row-{{ $post->id }}"
                    >

                        <!-- Checkbox -->

                        <td class="text-center">

                            <input
                                type="checkbox"
                                class="form-check-input post-checkbox"
                                value="{{ $post->id }}"
                                onchange="updateBulkSelection()"
                            >

                        </td>


                        <!-- ID -->

                        <td>
                            {{ $post->id }}
                        </td>


                        <!-- Image -->

                        <td>

                            @if($post->image)

                                <img
                                    src="{{ asset('storage/'.$post->image) }}"
                                    width="50"
                                    height="50"
                                    style="object-fit:cover;border-radius:4px;"
                                >

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        <!-- Title -->

                        <td>

                            <strong>
                                {{ $post->title }}
                            </strong>

                            @if($post->content)

                                <br>

                                <small class="text-muted">

                                    {{ Str::limit($post->content, 60) }}

                                </small>

                            @endif

                        </td>


                        <!-- Category -->

                        <td>

                            {{ $post->category ?? '—' }}

                        </td>


                        <!-- ================================================= -->
                        <!-- AJAX Status Toggle -->
                        <!-- ================================================= -->

                        <td>

                            <button
                                type="button"
                                id="status-btn-{{ $post->id }}"
                                class="badge border-0 status-toggle-btn
                                {{ $post->status === 'published'
                                    ? 'bg-success'
                                    : 'bg-secondary' }}"
                                style="cursor:pointer;"
                                onclick="togglePostStatus({{ $post->id }})"
                            >

                                <span id="status-text-{{ $post->id }}">
                                    {{ ucfirst($post->status) }}
                                </span>

                            </button>

                        </td>


                        <!-- Actions -->

                        <td>

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
                            colspan="7"
                            class="text-center text-muted py-4"
                        >

                            No posts found.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    <!-- Pagination -->

    <div class="mt-3">

        {{ $posts->links() }}

    </div>

</div>


<script>

/*
|--------------------------------------------------------------------------
| Individual Delete Confirmation
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

    }).then(result => {

        if (result.isConfirmed) {

            document
                .getElementById('delete-form-' + id)
                .submit();

        }

    });

}


/*
|--------------------------------------------------------------------------
| AJAX Status Toggle
|--------------------------------------------------------------------------
*/

async function togglePostStatus(id) {

    const button = document.getElementById(
        'status-btn-' + id
    );

    const text = document.getElementById(
        'status-text-' + id
    );


    const currentStatus = text.textContent
        .trim()
        .toLowerCase();


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

        confirmButtonColor: '#0d6efd',

        reverseButtons: true

    });


    if (!result.isConfirmed) {
        return;
    }


    try {

        button.disabled = true;


        Swal.fire({

            title: 'Updating status...',

            allowOutsideClick: false,

            allowEscapeKey: false,

            didOpen: () => {

                Swal.showLoading();

            }

        });


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
                data.message || 'Unable to update status.'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Update badge without page reload
        |--------------------------------------------------------------------------
        */

        text.textContent =
            data.status.charAt(0).toUpperCase() +
            data.status.slice(1);


        button.classList.remove(
            'bg-success',
            'bg-secondary'
        );


        if (data.status === 'published') {

            button.classList.add('bg-success');

        } else {

            button.classList.add('bg-secondary');

        }


        Swal.close();


        Toast.fire({

            icon: 'success',

            title: data.message

        });


    } catch (error) {

        Swal.close();


        Swal.fire({

            icon: 'error',

            title: 'Status Update Failed',

            text: error.message ||
                'Something went wrong.',

            confirmButtonText: 'OK'

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

    const checkboxes = document.querySelectorAll(
        '.post-checkbox'
    );


    checkboxes.forEach(function (item) {

        item.checked = checkbox.checked;

    });


    updateBulkSelection();

}


/*
|--------------------------------------------------------------------------
| Update Bulk Selection
|--------------------------------------------------------------------------
*/

function updateBulkSelection() {

    const checkboxes = document.querySelectorAll(
        '.post-checkbox'
    );


    const selected = document.querySelectorAll(
        '.post-checkbox:checked'
    );


    const count = selected.length;


    document.getElementById(
        'selected-count'
    ).textContent = count;


    const toolbar =
        document.getElementById('bulk-toolbar');


    if (count > 0) {

        toolbar.classList.add('show');

    } else {

        toolbar.classList.remove('show');

    }


    /*
    |--------------------------------------------------------------------------
    | Update Select All Checkbox State
    |--------------------------------------------------------------------------
    */

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

    document.querySelectorAll(
        '.post-checkbox'
    ).forEach(function (checkbox) {

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


    const ids = Array.from(selected)
        .map(checkbox => checkbox.value);


    if (ids.length === 0) {

        Swal.fire({

            icon: 'info',

            title: 'No posts selected',

            text: 'Please select at least one post.',

            confirmButtonText: 'OK'

        });

        return;

    }


    /*
    |--------------------------------------------------------------------------
    | SweetAlert Bulk Confirmation
    |--------------------------------------------------------------------------
    */

    const result = await Swal.fire({

        title: 'Delete selected posts?',

        html:
            'You are about to move <strong>' +
            ids.length +
            '</strong> post(s) to trash.<br><br>' +
            '<span class="text-danger">' +
            'This action will remove them from the current list.' +
            '</span>',

        icon: 'warning',

        showCancelButton: true,

        confirmButtonColor: '#d33',

        cancelButtonColor: '#6c757d',

        confirmButtonText:
            'Yes, delete ' + ids.length + ' post(s)',

        cancelButtonText: 'Cancel',

        reverseButtons: true

    });


    if (!result.isConfirmed) {

        return;

    }


    try {

        /*
        |--------------------------------------------------------------------------
        | Loading SweetAlert
        |--------------------------------------------------------------------------
        */

        Swal.fire({

            title: 'Deleting posts...',

            html:
                'Moving <strong>' +
                ids.length +
                '</strong> post(s) to trash.',

            allowOutsideClick: false,

            allowEscapeKey: false,

            didOpen: () => {

                Swal.showLoading();

            }

        });


        /*
        |--------------------------------------------------------------------------
        | AJAX Request
        |--------------------------------------------------------------------------
        */

        const response = await fetch(
            "{{ route('posts.bulkDelete') }}",
            {

                method: 'POST',

                headers: {

                    'Content-Type': 'application/json',

                    'Accept': 'application/json',

                    'X-CSRF-TOKEN': csrfToken,

                    'X-Requested-With': 'XMLHttpRequest'

                },

                body: JSON.stringify({

                    post_ids: ids

                })

            }
        );


        const data = await response.json();


        if (!response.ok || !data.success) {

            throw new Error(
                data.message ||
                'Unable to delete selected posts.'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Remove Deleted Rows Without Page Reload
        |--------------------------------------------------------------------------
        */

        ids.forEach(function (id) {

            const row = document.getElementById(
                'post-row-' + id
            );


            if (row) {

                row.remove();

            }

        });


        clearSelection();


        /*
        |--------------------------------------------------------------------------
        | Success SweetAlert
        |--------------------------------------------------------------------------
        */

        Swal.fire({

            icon: 'success',

            title: 'Deleted Successfully!',

            html:
                '<strong>' +
                data.deleted_count +
                '</strong> post(s) have been moved to trash.',

            confirmButtonText: 'OK',

            timer: 2500,

            timerProgressBar: true

        });


        /*
        |--------------------------------------------------------------------------
        | Check Empty Table
        |--------------------------------------------------------------------------
        */

        const remainingRows =
            document.querySelectorAll(
                'tbody tr[id^="post-row-"]'
            );


        if (remainingRows.length === 0) {

            setTimeout(function () {

                window.location.reload();

            }, 2600);

        }


    } catch (error) {

        Swal.close();


        Swal.fire({

            icon: 'error',

            title: 'Bulk Delete Failed',

            text:
                error.message ||
                'Something went wrong while deleting posts.',

            confirmButtonText: 'OK'

        });

    }

}


/*
|--------------------------------------------------------------------------
| Initial Selection State
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