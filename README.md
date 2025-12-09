# PHP_Laravel12_Implement_SweetAlert


<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-f72c1f?style=for-the-badge&logo=laravel" />
  <img src="https://img.shields.io/badge/PHP-8.2-blue?style=for-the-badge&logo=php" />
  <img src="https://img.shields.io/badge/SweetAlert2-Alerts-green?style=for-the-badge" />
</p>

---

##  Overview  
This project is a **Laravel 12 CRUD Application** with **SweetAlert2** for Success Alerts and Delete Confirmations.

---

##  Features
- SweetAlert2 Success Message  
- SweetAlert2 Delete Confirmation  
- Full CRUD for Posts  
- Laravel 12 + Blade UI  
- Clean Layout System  
- Bootstrap UI  

---

##  Folder Structure  

```
app/
│── Http/
│   └── Controllers/
│       ├── Controller.php
│       └── PostController.php
│
├── Models/
│   ├── Post.php
│   └── User.php
│
resources/
└── views/
    ├── layouts/
    │   └── app.blade.php
    ├── posts/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   └── edit.blade.php
    └── welcome.blade.php

routes/
├── web.php
└── console.php

database/
└── migrations/
    └── create_posts_table.php
```

---

##  Step 1 — Install Laravel 12

```bash
composer create-project laravel/laravel sweetalert "12.*"
```

---

##  Step 2 — Database Configuration

Update `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sweetalert
DB_USERNAME=root
DB_PASSWORD=
```

---

##  Step 3 — Create Migration

```bash
php artisan make:migration create_posts_table --create=posts
```

Migration:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('content')->nullable();
    $table->timestamps();
});
```

Run migration:

```bash
php artisan migrate
```

---

##  Step 4 — Create Model

```bash
php artisan make:model Post
```

###  `app/Models/Post.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
    ];
}
```

---

##  Step 5 — Create Controller

```bash
php artisan make:controller PostController
```

Key methods:

```php
public function index() {
    $posts = Post::all();
    return view('posts.index', compact('posts'));
}

public function create() {
    return view('posts.create');
}

public function store(Request $request) {
    $request->validate(['title' => 'required']);
    Post::create($request->all());
    return redirect()->route('posts.index')->with('success','Post Added Successfully');
}

public function edit($id) {
    $post = Post::findOrFail($id);
    return view('posts.edit', compact('post'));
}

public function update(Request $request, $id) {
    $post = Post::findOrFail($id);
    $post->update($request->all());
    return redirect()->route('posts.index')->with('success','Post Updated Successfully');
}

public function destroy($id) {
    $post = Post::findOrFail($id);
    $post->delete();
    return redirect()->route('posts.index')->with('success','Post Deleted Successfully');
}
```

---

##  Step 6 — Add Routes

```php
Route::get('/posts', [PostController::class,'index'])->name('posts.index');
Route::get('/posts/create', [PostController::class,'create'])->name('posts.create');
Route::post('/posts/store', [PostController::class,'store'])->name('posts.store');

Route::get('/posts/edit/{id}', [PostController::class,'edit'])->name('posts.edit');
Route::post('/posts/update/{id}', [PostController::class,'update'])->name('posts.update');

Route::delete('/posts/delete/{id}', [PostController::class,'destroy'])->name('posts.destroy');
```

---

##  Step 7 — Layout File

`resources/views/layouts/app.blade.php`

Includes:

- Bootstrap  
- SweetAlert2  
- `@yield('content')`  

---

##  Step 8 — Create Views

###  index.blade.php  
List all posts + delete button with SweetAlert.

###  create.blade.php  
Add new post form.

###  edit.blade.php  
Edit post form.

---

##  SweetAlert Integration

###  Success Alert

```blade
@if(session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: '{{ session("success") }}',
    timer: 2000,
    showConfirmButton: false
})
</script>
@endif
```

---

###  Delete Confirmation

```javascript
function confirmDelete(event) {
    event.preventDefault();
    let form = event.target.form;

    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Delete"
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
```

---

##  Run Application

```bash
php artisan serve
```

Visit:

```
http://localhost:8000/posts
```

---

##  Screenshots
CREATE POST:-

<img width="869" height="648" alt="image" src="https://github.com/user-attachments/assets/aeca28fd-f12b-4cfd-85cb-beb5bf991021" />


DELETE POST:-

<img width="975" height="468" alt="image" src="https://github.com/user-attachments/assets/cd599b76-8117-42ea-ae78-24e61b669e9f" />

