@extends('layouts.app')

@section('content')

<div class="container">

    @if(session('success'))

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                Toast.fire({

                    icon: 'success',

                    title: @json(session('success'))

                });

            }
        );
    </script>

    @endif


    @if(session('error'))

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                Swal.fire({

                    icon: 'error',

                    title: 'Error',

                    text: @json(session('error'))

                });

            }
        );
    </script>

    @endif


    {{-- ========================================================= --}}
    {{-- Header --}}
    {{-- ========================================================= --}}

    <div
        class="d-flex
               justify-content-between
               align-items-center
               mb-3">

        <div>

            <h4>
                🗑 Trash
            </h4>

            <small class="text-muted">

                {{ $trashCount }}
                post(s) in trash

            </small>

        </div>


        <div class="d-flex gap-2">

            @if($trashCount > 0)

            <button
                type="button"
                class="btn btn-success btn-sm"
                onclick="restoreAllPosts()">
                ↩ Restore All
            </button>

            @endif


            <a
                href="{{ route('posts.index') }}"
                class="btn btn-secondary btn-sm">
                ← Back to Posts
            </a>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Trash Table --}}
    {{-- ========================================================= --}}

    <div class="card shadow-sm">

        <div class="table-responsive">

            <table class="table table-bordered mb-0">

                <thead class="table-dark">

                    <tr>

                        <th>
                            #
                        </th>

                        <th>
                            Title
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Deleted At
                        </th>

                        <th width="220">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($posts as $post)

                    <tr>

                        <td>
                            {{ $post->id }}
                        </td>


                        <td>

                            <strong>
                                {{ $post->title }}
                            </strong>

                        </td>


                        <td>
                            {{ $post->category ?? '—' }}
                        </td>


                        <td>

                            {{ $post->deleted_at
                                    ->format('d M Y, h:i A') }}

                        </td>


                        <td>

                            {{-- Restore --}}

                            <form
                                action="{{ route('posts.restore', $post->id) }}"
                                method="POST"
                                class="no-spinner d-inline">

                                @csrf

                                <button
                                    type="submit"
                                    class="btn btn-success btn-sm">
                                    Restore
                                </button>

                            </form>


                            {{-- Permanent Delete --}}

                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                onclick="
                                        forceDelete(
                                            {{ $post->id }}
                                        )
                                    ">
                                Delete Forever
                            </button>


                            <form
                                id="force-form-{{ $post->id }}"
                                action="{{ route('posts.forceDelete', $post->id) }}"
                                method="POST"
                                class="no-spinner d-none">

                                @csrf

                                @method('DELETE')

                            </form>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="5"
                            class="text-center
                                       text-muted
                                       py-5">

                            🗑 Trash is empty.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- Pagination --}}

    <div class="mt-3">

        {{ $posts->links() }}

    </div>

</div>


<script>
    /*
|--------------------------------------------------------------------------
| Permanent Delete
|--------------------------------------------------------------------------
*/

    function forceDelete(id) {
        Swal.fire({

            title: 'Permanently delete?',

            text: 'This cannot be undone!',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#d33',

            cancelButtonColor: '#6c757d',

            confirmButtonText: 'Yes, delete forever',

            cancelButtonText: 'Cancel',

            reverseButtons: true

        }).then(result => {

            if (result.isConfirmed) {

                document
                    .getElementById(
                        'force-form-' + id
                    )
                    .submit();

            }

        });
    }


    /*
    |--------------------------------------------------------------------------
    | Restore All
    |--------------------------------------------------------------------------
    */

    function restoreAllPosts() {
        Swal.fire({

            title: 'Restore all posts?',

            html: 'All deleted posts will be restored.',

            icon: 'question',

            showCancelButton: true,

            confirmButtonColor: '#198754',

            cancelButtonColor: '#6c757d',

            confirmButtonText: 'Yes, restore all',

            cancelButtonText: 'Cancel',

            reverseButtons: true

        }).then(result => {

            if (result.isConfirmed) {

                const form =
                    document.createElement('form');

                form.method = 'POST';

                form.action =
                    "{{ route('posts.restoreAll') }}";


                const csrf =
                    document.createElement('input');

                csrf.type = 'hidden';

                csrf.name = '_token';

                csrf.value = csrfToken;


                form.appendChild(csrf);

                document.body.appendChild(form);

                form.submit();

            }

        });
    }
</script>

@endsection