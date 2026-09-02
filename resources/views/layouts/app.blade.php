<!DOCTYPE html>
<html id="htmlRoot">
<head>
    <title>Laravel CRUD + SweetAlert</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body.dark-mode { background:#1a1a2e !important; color:#e0e0e0; }
        body.dark-mode .table { color:#e0e0e0; }
        body.dark-mode .card, body.dark-mode .table { background:#16213e !important; border-color:#0f3460; }
        body.dark-mode .form-control, body.dark-mode .form-select { background:#0f3460; color:#e0e0e0; border-color:#1a1a2e; }
        body.dark-mode .btn-light { background:#0f3460; color:#e0e0e0; border-color:#1a1a2e; }
        #spinner-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999; align-items:center; justify-content:center; }
        #spinner-overlay.show { display:flex; }
    </style>
</head>
<body class="bg-light" id="bodyEl">

<div id="spinner-overlay">
    <div class="spinner-border text-light" style="width:3rem;height:3rem;"></div>
</div>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3 mb-4">
    <a class="navbar-brand" href="{{ route('posts.index') }}">📝 Posts App</a>
    <div class="ms-auto d-flex gap-2">
        <a href="{{ route('posts.trash') }}" class="btn btn-sm btn-outline-warning">🗑 Trash</a>
        <button onclick="toggleDark()" class="btn btn-sm btn-outline-light" id="darkBtn">🌙 Dark</button>
    </div>
</nav>

@yield('content')

<script>
// Dark mode
function toggleDark() {
    document.body.classList.toggle('dark-mode');
    const on = document.body.classList.contains('dark-mode');
    localStorage.setItem('dark', on ? '1' : '0');
    document.getElementById('darkBtn').textContent = on ? '☀️ Light' : '🌙 Dark';
}
if (localStorage.getItem('dark') === '1') {
    document.body.classList.add('dark-mode');
    document.getElementById('darkBtn').textContent = '☀️ Light';
}

// Loading spinner on form submit
document.addEventListener('submit', function(e) {
    if (!e.target.classList.contains('no-spinner')) {
        document.getElementById('spinner-overlay').classList.add('show');
    }
});

// Toast-style SweetAlert (used for success)
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2500,
    timerProgressBar: true,
});
</script>

</body>
</html>
