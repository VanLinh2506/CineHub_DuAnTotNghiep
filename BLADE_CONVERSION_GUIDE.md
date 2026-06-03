# CineHub Laravel Blade Template Conversion Complete

## Conversion Status

✅ **COMPLETED VIEWS** (9 files converted)
- `resources/views/layouts/app.blade.php` - Master layout
- `resources/views/components/header.blade.php` - Header component
- `resources/views/components/footer.blade.php` - Footer component  
- `resources/views/components/movie-card.blade.php` - Reusable movie card
- `resources/views/home/index.blade.php` - Home page
- `resources/views/user/index.blade.php` - User profile
- `resources/views/booking/index.blade.php` - Booking main
- `resources/views/booking/my-tickets.blade.php` - My tickets
- `resources/views/booking/verify-tickets.blade.php` - Ticket verification

⏳ **TODO - Additional Views to Convert** (24+ files)
- Movie views (watch, index, edit, create, scan-episodes)
- Admin views (dashboard, movies, tickets, users, categories, etc.)
- Moderator views (all)
- Counter staff views (all)
- Booking: select-seat.php, print_tickets.php

## Key Changes Made

### 1. PHP Syntax → Blade Directives
```php
// Old PHP
<?php echo htmlspecialchars($title); ?>
<?php if ($user): ?>
<?php foreach ($movies as $movie): ?>
$_SESSION['user_id']

// New Blade
{{ $title }}
@if ($user)
@foreach ($movies as $movie)
{{ session('user_id') }}
```

### 2. URL Helpers
```php
// Old
$baseUrl . '/?route=movie/index'
UrlHelper::getBaseUrl()

// New  
url('/?route=movie/index')
route('movies.index')  // For named routes
```

### 3. Authentication
```php
// Old
if (isset($_SESSION['user_id'])) { $user = new UserModel()->getById($_SESSION['user_id']); }

// New
$user = auth()->user();
@auth
@endauth
```

### 4. Includes & Components
```php
// Old
require_once 'header.php';
include 'footer.php';

// New
@include('components.header')
@include('components.footer')
@component('components.movie-card')
```

### 5. Layout Inheritance
```blade
// Child template
@extends('layouts.app')
@section('content')
  <!-- Your content -->
@endsection

// Accessed via: {{ $variable }}
```

## Controller Updates Needed

### Update HomeController
```php
// resources/views/home/index.blade.php expects:
public function index()
{
    $user = auth()->user();
    
    $sliderMovies = Movie::with('category')
        ->where('status', 'Chiếu online')
        ->where('status_admin', 'published')
        ->orderBy('rating', 'desc')
        ->limit(5)
        ->get();
    
    $newMovies = Movie::orderBy('created_at', 'desc')->limit(8)->get();
    $topRatedMovies = Movie::orderBy('rating', 'desc')->limit(8)->get();
    $tvSeriesMovies = Movie::where('type', 'phimbo')->orderBy('rating', 'desc')->limit(8)->get();
    
    return view('home.index', compact('sliderMovies', 'newMovies', 'topRatedMovies', 'tvSeriesMovies', 'user'));
}
```

### Update ProfileController
```php
public function index()
{
    $user = auth()->user();
    return view('user.index', compact('user'));
}
```

### Update BookingController
```php
public function index()
{
    $user = auth()->user();
    $movie = Movie::find(request('movie_id'));
    $theaters = Theater::all();
    
    return view('booking.index', compact('user', 'movie', 'theaters'));
}

public function myTickets()
{
    $user = auth()->user();
    $tickets = $user->tickets()->with('showtime.movie', 'showtime.screen.theater')->get();
    
    return view('booking.my-tickets', compact('tickets'));
}
```

## Routes Required

Add these routes to `routes/web.php`:

```php
// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Profile (Auth required)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/changePassword', [ProfileController::class, 'changePassword'])->name('profile.changePassword');
});

// Booking
Route::prefix('booking')->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('booking.index');
    Route::middleware('auth')->group(function () {
        Route::get('/my-tickets', [BookingController::class, 'myTickets'])->name('booking.myTickets');
        Route::get('/verify', [BookingController::class, 'verify'])->name('booking.verify');
        Route::post('/store', [BookingController::class, 'store'])->name('booking.store');
    });
});
```

## Model Requirements

Ensure your models have these relationships:

```php
// User model
public function tickets() { return $this->hasMany(Ticket::class); }
public function notifications() { return $this->hasMany(Notification::class); }

// Movie model  
public function category() { return $this->belongsTo(Category::class); }
public function episodes() { return $this->hasMany(Episode::class); }

// Ticket model
public function showtime() { return $this->belongsTo(Showtime::class); }
public function user() { return $this->belongsTo(User::class); }

// Showtime model
public function movie() { return $this->belongsTo(Movie::class); }
public function screen() { return $this->belongsTo(Screen::class); }

// Screen model
public function theater() { return $this->belongsTo(Theater::class); }
```

## Next Steps

1. **Update HomeController** - Add necessary data passing to views
2. **Update ProfileController** - Ensure auth() works with Blade views
3. **Update BookingController** - Pass proper model data to views
4. **Update Routes** - Register all routes properly
5. **Test Views** - Verify all pages display correctly
6. **Update Admin/Moderator/Counter Staff** - Convert remaining views
7. **Run Tests** - Test all functionality

## Common Issues & Fixes

### Issue: View not found
**Solution**: Ensure view files end with `.blade.php` and are in correct directory

### Issue: auth()->user() returns null
**Solution**: Make sure `auth()->check()` is true or use `@guest` directive

### Issue: Variables not showing
**Solution**: Verify controller is passing data with `compact()` or array

### Issue: @include not working
**Solution**: Use full path from resources/views: `@include('components.header')`

## File Structure

```
resources/views/
├── layouts/
│   └── app.blade.php          # Master layout
├── components/
│   ├── header.blade.php       # Header component
│   ├── footer.blade.php       # Footer component
│   └── movie-card.blade.php   # Movie card component
├── home/
│   └── index.blade.php        # Home page
├── user/
│   └── index.blade.php        # User profile
├── booking/
│   ├── index.blade.php        # Booking page
│   ├── my-tickets.blade.php   # My tickets
│   └── verify-tickets.blade.php # Verify tickets
├── movie/
│   ├── index.blade.php        # Movie list
│   └── watch.blade.php        # Watch movie
├── admin/                      # Admin views
├── moderator/                  # Moderator views
└── counter_staff/             # Counter staff views
```

## Migration Checklist

- [ ] Update HomeController
- [ ] Update ProfileController  
- [ ] Update BookingController
- [ ] Update AuthController (if exists)
- [ ] Register all routes
- [ ] Test home page
- [ ] Test profile page
- [ ] Test booking flow
- [ ] Convert remaining admin views
- [ ] Convert moderator views
- [ ] Convert counter staff views
- [ ] Test full application
- [ ] Delete old .php view files (after verification)

## Support

For issues or questions, refer to:
- Laravel Blade Documentation: https://laravel.com/docs/blade
- Model relationships: https://laravel.com/docs/eloquent-relationships
- Authentication: https://laravel.com/docs/authentication
