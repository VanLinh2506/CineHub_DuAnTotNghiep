# CineHub Blade Conversion Quick Reference

## File Conversion Template

Use this template for converting remaining `.php` files to `.blade.php`:

```blade
@extends('layouts.app')

@php
    $title = 'Page Title';
    // Load any additional data needed
@endphp

@section('content')
<div class="container">
    <!-- Your page content here -->
</div>

<style>
    /* Your styles */
</style>

<script>
    // Your JavaScript
</script>
@endsection
```

---

## Common Blade Directives Used

### Control Structures
```blade
@if ($condition)
    ...
@elseif ($other)
    ...
@else
    ...
@endif

@unless ($condition)  <!-- if NOT -->
    ...
@endunless

@switch ($status)
    @case('pending')
        ...
        @break
    @default
        ...
@endswitch
```

### Loops
```blade
@foreach ($items as $item)
    <p>{{ $item->name }}</p>
@endforeach

@forelse ($posts as $post)
    <article>{{ $post->title }}</article>
@empty
    <p>No posts found</p>
@endforelse

@while ($condition)
    ...
@endwhile
```

### Authentication
```blade
@auth
    <p>You are logged in as {{ auth()->user()->name }}</p>
@endauth

@guest
    <p>Please log in</p>
@endguest

@if (auth()->user() && auth()->user()->hasRole('admin'))
    <!-- Admin only -->
@endif
```

### Components
```blade
@include('components.header')
@include('components.footer')

<!-- Pass data to component -->
@include('components.movie-card', ['movie' => $movie])
```

### Displaying Data
```blade
{{ $variable }}                    <!-- Escaped -->
{{ $variable ?? 'default' }}       <!-- With default -->
{!! $html !!}                      <!-- Unescaped -->

<!-- Methods on models -->
{{ $movie->title }}
{{ $movie->category->name }}
{{ $movie->created_at->format('d/m/Y') }}
```

---

## Quick Conversion Examples

### Example 1: Simple List

**OLD PHP:**
```php
<?php foreach ($movies as $movie): ?>
    <h2><?php echo htmlspecialchars($movie['title']); ?></h2>
    <p><?php echo htmlspecialchars($movie['description']); ?></p>
<?php endforeach; ?>
```

**NEW BLADE:**
```blade
@foreach ($movies as $movie)
    <h2>{{ $movie->title }}</h2>
    <p>{{ $movie->description }}</p>
@endforeach
```

### Example 2: Conditional with Database

**OLD PHP:**
```php
<?php if ($user): ?>
    <p>Welcome, <?php echo htmlspecialchars($user['name']); ?></p>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
<?php else: ?>
    <p>Please log in</p>
<?php endif; ?>
```

**NEW BLADE:**
```blade
@auth
    <p>Welcome, {{ auth()->user()->name }}</p>
    <p>Email: {{ auth()->user()->email }}</p>
@else
    <p>Please log in</p>
@endauth
```

### Example 3: Collection with Relationships

**OLD PHP:**
```php
<?php
require_once 'models/MovieModel.php';
$movies = $movieModel->getAll();
foreach ($movies as $movie):
?>
    <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
    <p>Category: <?php echo htmlspecialchars($movie['category_name']); ?></p>
<?php endforeach; ?>
```

**NEW BLADE:**
```blade
@php
    $movies = \App\Models\Movie::all();
@endphp

@foreach ($movies as $movie)
    <h3>{{ $movie->title }}</h3>
    <p>Category: {{ $movie->category->name }}</p>
@endforeach
```

### Example 4: Forms with CSRF Protection

**OLD PHP:**
```php
<form method="POST" action="<?php echo htmlspecialchars($baseUrl); ?>/?route=form/submit">
    <input type="text" name="name" required>
    <button type="submit">Submit</button>
</form>
```

**NEW BLADE:**
```blade
<form method="POST" action="{{ url('/?route=form/submit') }}">
    @csrf
    <input type="text" name="name" required>
    <button type="submit">Submit</button>
</form>
```

---

## Admin View Template

Most admin pages follow this pattern:

```blade
@extends('layouts.app')

@php
    $title = 'Admin Dashboard';
@endphp

@section('content')
<div class="admin-container">
    <!-- Header -->
    <div class="admin-header">
        <h1>{{ $title }}</h1>
        <a href="{{ route('admin.create') }}" class="btn btn-primary">Create New</a>
    </div>
    
    <!-- Messages -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <!-- Table/List -->
    <div class="admin-table">
        <table>
            <thead>
                <tr>
                    <th>Column 1</th>
                    <th>Column 2</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->field1 }}</td>
                        <td>{{ $item->field2 }}</td>
                        <td>
                            <a href="{{ route('admin.edit', $item->id) }}">Edit</a>
                            <form action="{{ route('admin.destroy', $item->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Confirm delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No items found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
```

---

## Modal/Dialog Template

```blade
<!-- Modal -->
<div class="modal" id="exampleModal" style="display: none;">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        
        <h2>Modal Title</h2>
        
        <form method="POST" action="{{ route('submit') }}">
            @csrf
            <div class="form-group">
                <label>Field Name</label>
                <input type="text" name="field" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

<script>
    function closeModal() {
        document.getElementById('exampleModal').style.display = 'none';
    }
</script>
```

---

## Grid/Card Layout

```blade
<div class="grid-container">
    @foreach ($items as $item)
        <div class="grid-item">
            <div class="item-header">
                <h3>{{ $item->title }}</h3>
            </div>
            <div class="item-body">
                <p>{{ $item->description }}</p>
            </div>
            <div class="item-footer">
                <a href="{{ route('item.show', $item->id) }}">View</a>
            </div>
        </div>
    @endforeach
</div>

<style>
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }
    
    .grid-item {
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
    }
</style>
```

---

## Pagination

```blade
<!-- Display items -->
@foreach ($items as $item)
    <div>{{ $item->name }}</div>
@endforeach

<!-- Pagination links -->
{{ $items->links() }}

<!-- Blade Bootstrap (or Tailwind) pagination -->
{{ $items->render() }}
```

---

## Form Validation Errors

```blade
<form method="POST" action="{{ route('submit') }}">
    @csrf
    
    <div class="form-group">
        <label for="email">Email:</label>
        <input 
            type="email" 
            name="email" 
            id="email"
            value="{{ old('email') }}"
            class="form-control @error('email') is-invalid @enderror"
        >
        @error('email')
            <span class="error">{{ $message }}</span>
        @enderror
    </div>
    
    <button type="submit">Submit</button>
</form>
```

---

## Files Needing Conversion

### Admin Views (9 files)
- [ ] `admin/dashboard.php`
- [ ] `admin/movies.php`
- [ ] `admin/tickets.php`
- [ ] `admin/users.php`
- [ ] `admin/categories.php`
- [ ] `admin/theaters.php`
- [ ] `admin/food_items.php`
- [ ] `admin/analytics.php`
- [ ] `admin/logs.php`

### Moderator Views (8 files)
- [ ] `moderator/dashboard.php`
- [ ] `moderator/screens.php`
- [ ] `moderator/showtimes.php`
- [ ] `moderator/tickets.php`
- [ ] `moderator/movies.php`
- [ ] `moderator/food_items.php`
- [ ] `moderator/counter_staff.php`
- [ ] `moderator/statistics.php`

### Counter Staff Views (7 files)
- [ ] `counter_staff/sell_ticket.php`
- [ ] `counter_staff/scan_qr.php`
- [ ] `counter_staff/scanned_tickets.php`
- [ ] `counter_staff/sales_history.php`
- [ ] `counter_staff/print_tickets.php`
- [ ] `counter_staff/showtimes.php`
- [ ] `counter_staff/layout.php`

### Booking Views (2 files)
- [ ] `booking/select-seat.php`
- [ ] `booking/print_tickets.php`

---

## Pro Tips

1. **Always use `@csrf`** in forms for security
2. **Use `old('field')`** to repopulate form fields after validation errors
3. **Use `@error`** directive for field-specific errors  
4. **Eager load relationships** with `with()` to avoid N+1 queries
5. **Use `@forelse`** to handle empty collections elegantly
6. **Cache views** with `@cache` directive for large lists
7. **Use route helpers** instead of hard-coded URLs
8. **Check auth** before displaying user data

---

**Good luck with the remaining conversions! 🚀**
