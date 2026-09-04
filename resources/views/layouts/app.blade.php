<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        Posts App
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>

        body {
            transition: background-color 0.3s, color 0.3s;
        }

        /* -------------------------------------------------------------
           Dark Mode
        ------------------------------------------------------------- */

        body.dark-mode {
            background-color: #121212 !important;
            color: #f5f5f5;
        }

        body.dark-mode .card {
            background-color: #1e1e1e;
            color: #f5f5f5;
            border-color: #333;
        }

        body.dark-mode .table {
            color: #f5f5f5;
        }

        body.dark-mode .table-bordered {
            border-color: #444;
        }

        body.dark-mode .form-control,
        body.dark-mode .form-select {
            background-color: #2a2a2a;
            color: #fff;
            border-color: #555;
        }

        body.dark-mode .form-control::placeholder {
            color: #aaa;
        }

        body.dark-mode .form-label {
            color: #fff;
        }

        body.dark-mode .text-muted {
            color: #aaa !important;
        }

        body.dark-mode .navbar {
            background-color: #1b1b1b !important;
        }

        body.dark-mode .navbar-brand,
        body.dark-mode .nav-link {
            color: #fff !important;
        }

        body.dark-mode .page-link {
            background-color: #2a2a2a;
            color: #fff;
            border-color: #555;
        }

        body.dark-mode .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        /* -------------------------------------------------------------
           Loading Spinner
        ------------------------------------------------------------- */

        #loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
        }

        #loading-overlay .spinner-box {
            background: #fff;
            padding: 25px 35px;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.25);
            text-align: center;
        }

        body.dark-mode #loading-overlay .spinner-box {
            background: #222;
            color: #fff;
        }

        .unsaved-indicator {
            display: none;
            color: #dc3545;
            font-size: 13px;
            font-weight: 600;
        }

        .unsaved-indicator.show {
            display: inline-block;
        }

        /* -------------------------------------------------------------
           Dashboard Statistics
        ------------------------------------------------------------- */

        .stat-card {
            border: 0;
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
        }

        .stat-label {
            font-size: 14px;
            font-weight: 500;
        }

        /* -------------------------------------------------------------
           Sorting Links
        ------------------------------------------------------------- */

        .sort-link {
            color: inherit;
            text-decoration: none;
            font-weight: 600;
        }

        .sort-link:hover {
            text-decoration: underline;
        }

        .sort-arrow {
            font-size: 11px;
            margin-left: 3px;
        }

    </style>

</head>


<body class="bg-light">


    <!-- ================================================================
         Loading Overlay
    ================================================================= -->

    <div id="loading-overlay">

        <div class="spinner-box">

            <div
                class="spinner-border text-primary mb-3"
                role="status"
            >
                <span class="visually-hidden">
                    Loading...
                </span>
            </div>

            <div>
                Please wait...
            </div>

        </div>

    </div>


    <!-- ================================================================
         Navbar
    ================================================================= -->

    <nav class="navbar navbar-expand-lg bg-dark navbar-dark">

        <div class="container">

            <a
                href="{{ route('posts.index') }}"
                class="navbar-brand fw-bold"
            >
                Posts App
            </a>


            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarContent"
            >

                <span class="navbar-toggler-icon"></span>

            </button>


            <div
                class="collapse navbar-collapse"
                id="navbarContent"
            >

                <ul class="navbar-nav me-auto">

                    <li class="nav-item">

                        <a
                            href="{{ route('posts.index') }}"
                            class="nav-link"
                        >
                            Posts
                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            href="{{ route('posts.trash') }}"
                            class="nav-link"
                        >
                            🗑 Trash
                        </a>

                    </li>

                </ul>


                <button
                    type="button"
                    id="darkModeToggle"
                    class="btn btn-outline-light btn-sm"
                >
                    🌙 Dark Mode
                </button>

            </div>

        </div>

    </nav>


    <!-- ================================================================
         Main Content
    ================================================================= -->

    <main class="py-4">

        @yield('content')

    </main>


    <!-- ================================================================
         Bootstrap JS
    ================================================================= -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    ></script>


    <script>

        /*
        |--------------------------------------------------------------------------
        | Global CSRF Token
        |--------------------------------------------------------------------------
        */

        const csrfToken =
            document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content');


        /*
        |--------------------------------------------------------------------------
        | Dark Mode
        |--------------------------------------------------------------------------
        */

        const darkModeToggle =
            document.getElementById('darkModeToggle');


        function applyDarkMode() {

            const darkMode =
                localStorage.getItem('darkMode') === 'true';

            if (darkMode) {

                document.body.classList.add('dark-mode');

                if (darkModeToggle) {

                    darkModeToggle.innerHTML =
                        '☀️ Light Mode';

                }

            } else {

                document.body.classList.remove('dark-mode');

                if (darkModeToggle) {

                    darkModeToggle.innerHTML =
                        '🌙 Dark Mode';

                }

            }

        }


        applyDarkMode();


        if (darkModeToggle) {

            darkModeToggle.addEventListener(
                'click',
                function() {

                    const isDark =
                        document.body.classList.toggle(
                            'dark-mode'
                        );

                    localStorage.setItem(
                        'darkMode',
                        isDark
                    );

                    this.innerHTML =
                        isDark
                            ? '☀️ Light Mode'
                            : '🌙 Dark Mode';

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | SweetAlert Toast
        |--------------------------------------------------------------------------
        */

        const Toast = Swal.mixin({

            toast: true,

            position: 'top-end',

            showConfirmButton: false,

            timer: 3000,

            timerProgressBar: true

        });


        /*
        |--------------------------------------------------------------------------
        | Loading Spinner
        |--------------------------------------------------------------------------
        */

        function showLoading() {

            const overlay =
                document.getElementById('loading-overlay');

            if (overlay) {

                overlay.style.display = 'flex';

            }

        }


        function hideLoading() {

            const overlay =
                document.getElementById('loading-overlay');

            if (overlay) {

                overlay.style.display = 'none';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Form Submit Loading
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'submit',
            function(event) {

                const form = event.target;

                if (
                    form.tagName === 'FORM' &&
                    !form.classList.contains('no-spinner')
                ) {

                    showLoading();

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Browser Back / Forward
        |--------------------------------------------------------------------------
        */

        window.addEventListener(
            'pageshow',
            function() {

                hideLoading();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Session Success Message
        |--------------------------------------------------------------------------
        */

        @if(session('success'))

        document.addEventListener(
            'DOMContentLoaded',
            function() {

                Toast.fire({

                    icon: 'success',

                    title: @json(session('success'))

                });

            }
        );

        @endif


        /*
        |--------------------------------------------------------------------------
        | Session Error Message
        |--------------------------------------------------------------------------
        */

        @if(session('error'))

        document.addEventListener(
            'DOMContentLoaded',
            function() {

                Toast.fire({

                    icon: 'error',

                    title: @json(session('error'))

                });

            }
        );

        @endif


        /*
        |--------------------------------------------------------------------------
        | Validation Errors
        |--------------------------------------------------------------------------
        */

        @if($errors->any())

        document.addEventListener(
            'DOMContentLoaded',
            function() {

                Swal.fire({

                    icon: 'error',

                    title: 'Validation Error',

                    html: `
                        <div style="text-align:left;">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    `

                });

            }
        );

        @endif


        /*
        |--------------------------------------------------------------------------
        | AJAX Error Handler
        |--------------------------------------------------------------------------
        */

        function showAjaxError(error) {

            hideLoading();

            let message =
                'Something went wrong. Please try again.';


            if (
                error &&
                error.responseJSON &&
                error.responseJSON.message
            ) {

                message =
                    error.responseJSON.message;

            }


            Swal.fire({

                icon: 'error',

                title: 'Error',

                text: message

            });

        }


        /*
        |--------------------------------------------------------------------------
        | Generic AJAX POST Helper
        |--------------------------------------------------------------------------
        */

        function ajaxPost(
            url,
            data = {},
            options = {}
        ) {

            showLoading();


            return fetch(
                url,
                {

                    method: 'POST',

                    headers: {

                        'Content-Type':
                            'application/json',

                        'Accept':
                            'application/json',

                        'X-CSRF-TOKEN':
                            csrfToken,

                        'X-Requested-With':
                            'XMLHttpRequest'

                    },

                    body:
                        JSON.stringify(data)

                }
            )

            .then(
                async function(response) {

                    const result =
                        await response.json();


                    if (!response.ok) {

                        throw {

                            responseJSON:
                                result

                        };

                    }


                    return result;

                }
            )

            .catch(
                function(error) {

                    showAjaxError(error);

                    throw error;

                }
            )

            .finally(
                function() {

                    hideLoading();

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Unsaved Changes Protection
        |--------------------------------------------------------------------------
        */

        let unsavedChanges = false;

        let allowNavigation = false;


        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const forms =
                    document.querySelectorAll(
                        '.unsaved-changes-form'
                    );


                forms.forEach(
                    function(form) {

                        const indicator =
                            form
                                .closest('.card')
                                ?.parentElement
                                ?.querySelector(
                                    '.unsaved-indicator'
                                );


                        form.addEventListener(
                            'input',
                            function() {

                                unsavedChanges = true;


                                if (indicator) {

                                    indicator.classList.add(
                                        'show'
                                    );

                                }

                            }
                        );


                        form.addEventListener(
                            'change',
                            function() {

                                unsavedChanges = true;


                                if (indicator) {

                                    indicator.classList.add(
                                        'show'
                                    );

                                }

                            }
                        );


                        form.addEventListener(
                            'submit',
                            function() {

                                allowNavigation = true;

                                unsavedChanges = false;


                                if (indicator) {

                                    indicator.classList.remove(
                                        'show'
                                    );

                                }

                            }
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Internal Link Protection
                |--------------------------------------------------------------------------
                */

                document
                    .querySelectorAll('a')
                    .forEach(
                        function(link) {

                            link.addEventListener(
                                'click',
                                function(event) {

                                    if (
                                        !unsavedChanges ||
                                        allowNavigation
                                    ) {

                                        return;

                                    }


                                    const href =
                                        link.getAttribute('href');


                                    if (
                                        !href ||
                                        href === '#' ||
                                        href.startsWith(
                                            'javascript:'
                                        )
                                    ) {

                                        return;

                                    }


                                    event.preventDefault();


                                    Swal.fire({

                                        title:
                                            'Unsaved changes',

                                        text:
                                            'You have unsaved changes. Are you sure you want to leave this page?',

                                        icon:
                                            'warning',

                                        showCancelButton:
                                            true,

                                        confirmButtonText:
                                            'Leave page',

                                        cancelButtonText:
                                            'Stay'

                                    }).then(
                                        function(result) {

                                            if (
                                                result.isConfirmed
                                            ) {

                                                allowNavigation =
                                                    true;

                                                unsavedChanges =
                                                    false;

                                                window.location.href =
                                                    href;

                                            }

                                        }
                                    );

                                }
                            );

                        }
                    );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Browser Before Unload
        |--------------------------------------------------------------------------
        */

        window.addEventListener(
            'beforeunload',
            function(event) {

                if (
                    unsavedChanges &&
                    !allowNavigation
                ) {

                    event.preventDefault();

                    event.returnValue = '';

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Prevent Spinner on AJAX Forms
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'DOMContentLoaded',
            function() {

                document
                    .querySelectorAll(
                        'form.no-spinner'
                    )
                    .forEach(
                        function(form) {

                            form.addEventListener(
                                'submit',
                                function(event) {

                                    event.stopPropagation();

                                }
                            );

                        }
                    );

            }
        );

    </script>

</body>

</html>