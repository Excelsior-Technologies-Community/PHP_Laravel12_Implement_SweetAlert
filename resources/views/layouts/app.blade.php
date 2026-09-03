<!DOCTYPE html>
<html lang="en" id="htmlRoot">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Laravel CRUD + SweetAlert</title>

    <!-- Bootstrap -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    >

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>

        body.dark-mode {
            background: #1a1a2e !important;
            color: #e0e0e0;
        }

        body.dark-mode .table {
            color: #e0e0e0;
        }

        body.dark-mode .card,
        body.dark-mode .table {
            background: #16213e !important;
            border-color: #0f3460;
        }

        body.dark-mode .form-control,
        body.dark-mode .form-select {
            background: #0f3460;
            color: #e0e0e0;
            border-color: #1a1a2e;
        }

        body.dark-mode .form-control::placeholder {
            color: #aaa;
        }

        body.dark-mode .btn-light {
            background: #0f3460;
            color: #e0e0e0;
            border-color: #1a1a2e;
        }

        body.dark-mode .text-muted {
            color: #aaa !important;
        }

        #spinner-overlay {

            display: none;

            position: fixed;

            inset: 0;

            background: rgba(0, 0, 0, .45);

            z-index: 9999;

            align-items: center;

            justify-content: center;
        }

        #spinner-overlay.show {
            display: flex;
        }

        .bulk-toolbar {
            display: none;
        }

        .bulk-toolbar.show {
            display: flex;
        }

        .post-checkbox {
            cursor: pointer;
        }

        /* Unsaved Changes Indicator */

.unsaved-indicator {
    display: none;
    font-size: 0.85rem;
    color: #dc3545;
    font-weight: 600;
}

.unsaved-indicator.show {
    display: inline-block;
}

    </style>

</head>

<body class="bg-light" id="bodyEl">


<!-- Loading Spinner -->

<div id="spinner-overlay">

    <div
        class="spinner-border text-light"
        style="width:3rem;height:3rem;"
    ></div>

</div>


<!-- Navigation -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3 mb-4">

    <a
        class="navbar-brand"
        href="{{ route('posts.index') }}"
    >
        📝 Posts App
    </a>

    <div class="ms-auto d-flex gap-2">

        <a
            href="{{ route('posts.trash') }}"
            class="btn btn-sm btn-outline-warning"
        >
            🗑 Trash
        </a>

        <button
            onclick="toggleDark()"
            class="btn btn-sm btn-outline-light"
            id="darkBtn"
        >
            🌙 Dark
        </button>

    </div>

</nav>


@yield('content')


<script>

/*
|--------------------------------------------------------------------------
| CSRF Token
|--------------------------------------------------------------------------
*/

const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute('content');


/*
|--------------------------------------------------------------------------
| Dark Mode
|--------------------------------------------------------------------------
*/

function toggleDark() {

    document.body.classList.toggle('dark-mode');

    const on = document.body.classList.contains('dark-mode');

    localStorage.setItem(
        'dark',
        on ? '1' : '0'
    );

    document.getElementById('darkBtn').textContent =
        on ? '☀️ Light' : '🌙 Dark';
}


if (localStorage.getItem('dark') === '1') {

    document.body.classList.add('dark-mode');

    document.getElementById('darkBtn').textContent =
        '☀️ Light';
}


/*
|--------------------------------------------------------------------------
| Loading Spinner
|--------------------------------------------------------------------------
*/

document.addEventListener('submit', function (e) {

    if (!e.target.classList.contains('no-spinner')) {

        document
            .getElementById('spinner-overlay')
            .classList.add('show');
    }

});


/*
|--------------------------------------------------------------------------
| Hide Spinner
|--------------------------------------------------------------------------
*/

window.addEventListener('pageshow', function () {

    document
        .getElementById('spinner-overlay')
        .classList.remove('show');

});


/*
|--------------------------------------------------------------------------
| SweetAlert Toast
|--------------------------------------------------------------------------
*/

const Toast = Swal.mixin({

    toast: true,

    position: 'top-end',

    showConfirmButton: false,

    timer: 2500,

    timerProgressBar: true,

});


/*
|--------------------------------------------------------------------------
| SweetAlert Session Success
|--------------------------------------------------------------------------
*/

@if(session('success'))

document.addEventListener('DOMContentLoaded', function () {

    Toast.fire({

        icon: 'success',

        title: @json(session('success'))

    });

});

@endif


/*
|--------------------------------------------------------------------------
| SweetAlert Session Error
|--------------------------------------------------------------------------
*/

@if(session('error'))

document.addEventListener('DOMContentLoaded', function () {

    Swal.fire({

        icon: 'error',

        title: 'Something went wrong',

        text: @json(session('error')),

        confirmButtonText: 'OK'

    });

});

@endif


/*
|--------------------------------------------------------------------------
| SweetAlert Validation Errors
|--------------------------------------------------------------------------
*/

@if($errors->any())

document.addEventListener('DOMContentLoaded', function () {

    Swal.fire({

        icon: 'error',

        title: 'Validation Error',

        html: `
            <div class="text-start">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        `,

        confirmButtonText: 'Fix Errors'

    });

});

@endif


/*
|--------------------------------------------------------------------------
| AJAX Error Handler
|--------------------------------------------------------------------------
*/

function showAjaxError(error, defaultMessage = 'Something went wrong.') {

    let message = defaultMessage;

    if (
        error &&
        error.responseJSON &&
        error.responseJSON.message
    ) {
        message = error.responseJSON.message;
    }

    Swal.fire({

        icon: 'error',

        title: 'Request Failed',

        text: message,

        confirmButtonText: 'OK'

    });
}


/*
|--------------------------------------------------------------------------
| Generic AJAX POST Helper
|--------------------------------------------------------------------------
*/

async function ajaxPost(url, data = {}) {

    const response = await fetch(url, {

        method: 'POST',

        headers: {

            'Content-Type': 'application/json',

            'Accept': 'application/json',

            'X-CSRF-TOKEN': csrfToken,

            'X-Requested-With': 'XMLHttpRequest'

        },

        body: JSON.stringify(data)

    });

    const result = await response.json();

    if (!response.ok) {

        throw {

            status: response.status,

            responseJSON: result

        };
    }

    return result;
}

/*
|--------------------------------------------------------------------------
| Unsaved Changes Protection
|--------------------------------------------------------------------------
*/

let unsavedChanges = false;
let allowNavigation = false;


/*
|--------------------------------------------------------------------------
| Mark Form As Changed
|--------------------------------------------------------------------------
*/

function markFormAsChanged() {

    unsavedChanges = true;

    document
        .querySelectorAll('.unsaved-indicator')
        .forEach(function (indicator) {

            indicator.classList.add('show');

        });

}


/*
|--------------------------------------------------------------------------
| Mark Form As Saved / Clean
|--------------------------------------------------------------------------
*/

function markFormAsClean() {

    unsavedChanges = false;

    document
        .querySelectorAll('.unsaved-indicator')
        .forEach(function (indicator) {

            indicator.classList.remove('show');

        });

}


/*
|--------------------------------------------------------------------------
| Initialize Unsaved Changes Protection
|--------------------------------------------------------------------------
*/

document.addEventListener('DOMContentLoaded', function () {

    const forms = document.querySelectorAll(
        '.unsaved-changes-form'
    );


    forms.forEach(function (form) {


        /*
        |--------------------------------------------------------------------------
        | Detect Text Input Changes
        |--------------------------------------------------------------------------
        */

        form.addEventListener('input', function () {

            markFormAsChanged();

        });


        /*
        |--------------------------------------------------------------------------
        | Detect Select / File / Other Changes
        |--------------------------------------------------------------------------
        */

        form.addEventListener('change', function () {

            markFormAsChanged();

        });


        /*
        |--------------------------------------------------------------------------
        | Form Submission
        |--------------------------------------------------------------------------
        */

        form.addEventListener('submit', function () {

            markFormAsClean();

            allowNavigation = true;

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Browser Refresh / Close Protection
    |--------------------------------------------------------------------------
    */

    window.addEventListener('beforeunload', function (event) {

        if (
            unsavedChanges &&
            !allowNavigation
        ) {

            event.preventDefault();

            event.returnValue = '';

        }

    });


    /*
    |--------------------------------------------------------------------------
    | Internal Link Navigation Protection
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', async function (event) {

        const link = event.target.closest('a');


        if (!link) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Ignore New Tab Links
        |--------------------------------------------------------------------------
        */

        if (
            link.target === '_blank' ||
            link.hasAttribute('download')
        ) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Ignore JavaScript Links
        |--------------------------------------------------------------------------
        */

        if (
            link.href.startsWith('javascript:')
        ) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Ignore Anchor Links
        |--------------------------------------------------------------------------
        */

        const currentUrl =
            window.location.href.split('#')[0];

        const linkUrl =
            link.href.split('#')[0];


        if (currentUrl === linkUrl) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | No Unsaved Changes
        |--------------------------------------------------------------------------
        */

        if (!unsavedChanges) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Stop Normal Navigation
        |--------------------------------------------------------------------------
        */

        event.preventDefault();

        event.stopPropagation();


        /*
        |--------------------------------------------------------------------------
        | SweetAlert Confirmation
        |--------------------------------------------------------------------------
        */

        const result = await Swal.fire({

            icon: 'warning',

            title: 'Unsaved Changes',

            html: `
                <p class="mb-1">
                    You have unsaved changes in this form.
                </p>

                <strong>
                    If you leave now, your changes will be lost.
                </strong>
            `,

            showCancelButton: true,

            confirmButtonText: 'Discard Changes',

            cancelButtonText: 'Stay on Page',

            confirmButtonColor: '#dc3545',

            cancelButtonColor: '#6c757d',

            reverseButtons: true,

            allowOutsideClick: false

        });


        /*
        |--------------------------------------------------------------------------
        | Discard Changes
        |--------------------------------------------------------------------------
        */

        if (result.isConfirmed) {

            allowNavigation = true;

            markFormAsClean();

            window.location.href = link.href;

        }

    });

});

</script>

</body>

</html>