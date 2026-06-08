# Implementation Action Plan

## Phase 1: Verify Current Setup ✅ DONE

**Completed:**
- Created main layout template (`layouts/app.blade.php`)
- Created header component (`components/header.blade.php`)
- Created footer component (`components/footer.blade.php`)
- Created movie card component (`components/movie-card.blade.php`)
- Converted 10 major view files to Blade
- Created comprehensive documentation

---

## Phase 2: Test Current Conversions (TODAY)

### Step 1: Verify Laravel Routes
```php
// routes/web.php - Add these routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/movie', [MovieController::class, 'index'])->name('movie.index');
Route::get('/movie/{id}', [MovieController::class, 'show'])->name('movie.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/booking/my-tickets', [BookingController::class, 'myTickets'])->name('booking.myTickets');
});
```

### Step 2: Update HomeController
```php
// app/Http/Controllers/HomeController.php
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

### Step 3: Test in Browser
```
1. Run: php artisan serve
2. Visit: http://localhost:8000
3. Check for errors in console/terminal
4. Test navigation & authentication
```

---

## Phase 3: Complete Core Controllers (Days 1-2)

### Update These Controllers:

#### 1. MovieController
```php
public function index()
{
    $movies = Movie::with('category')->paginate(12);
    return view('movie.index', compact('movies'));
}

public function show($id)
{
    $movie = Movie::with('category', 'episodes', 'reviews.user')->find($id);
    $similarMovies = Movie::where('category_id', $movie->category_id)
        ->where('id', '!=', $movie->id)
        ->limit(8)
        ->get();
    
    return view('movie.show', compact('movie', 'similarMovies'));
}
```

#### 2. ProfileController
```php
public function index()
{
    $user = auth()->user();
    return view('user.index', compact('user'));
}

public function update(Request $request)
{
    $user = auth()->user();
    $user->update($request->validated());
    return back()->with('success', 'Profile updated');
}
```

#### 3. BookingController
```php
public function index()
{
    $movie = Movie::find(request('movie_id'));
    $theaters = Theater::all();
    return view('booking.index', compact('movie', 'theaters'));
}

public function myTickets()
{
    $tickets = auth()->user()->tickets()
        ->with('showtime.movie', 'showtime.screen.theater')
        ->get();
    return view('booking.my-tickets', compact('tickets'));
}
```

---

## Phase 4: Convert Remaining Views (Days 3-4)

### Priority Order:

**HIGH** (Start these first):
1. Admin Dashboard (`admin/dashboard.php`)
2. Movie Management (`admin/movies.php`)
3. User Management (`admin/users.php`)

**MEDIUM** (Do after high priority):
1. Moderator Dashboard
2. Theater Management
3. Showtime Management

**LOW** (Can do later):
1. Counter Staff views
2. Analytics/Reports
3. Support/Logs

### Template for Each Conversion:

1. **Read existing PHP file** (old view)
2. **Create Blade version** using template patterns
3. **Update controller** to pass required data
4. **Test in browser** before moving to next
5. **Check: No PHP tags, No raw includes, Uses @directives**

---

## Phase 5: Full Testing & Cleanup (Day 5)

### Testing Checklist:
```
[ ] Home page loads
[ ] Navigation works
[ ] User authentication works
[ ] Profile page accessible
[ ] Booking flow complete
[ ] Movie details display
[ ] Admin pages work
[ ] Moderator pages work
[ ] No 404 errors
[ ] All links work
[ ] Form submissions work
[ ] Database queries work
```

### Cleanup:
```bash
# After verification, delete old PHP files
rm resources/views/home/index.php
rm resources/views/user/index.php
rm resources/views/booking/*.php
rm resources/views/movie/*.php
# etc. for all converted files

# Commit to git
git add .
git commit -m "Complete Blade template migration"
```

---

## Estimated Timeline

| Phase | Task | Days | Status |
|-------|------|------|--------|
| 1 | Create templates & documentation | 1 | ✅ Done |
| 2 | Test current views & setup | 0.5 | ⏳ Today |
| 3 | Update core controllers | 1 | ⏳ Tomorrow |
| 4 | Convert remaining views | 2 | ⏳ Next |
| 5 | Full testing & cleanup | 1 | ⏳ Final |
| **Total** | | **5.5 days** | |

---

## Error Handling During Migration

### Common Issues & Fixes:

**Issue 1: View not found**
```
Error: View [home.index] not found
Fix: Ensure file is resources/views/home/index.blade.php
```

**Issue 2: auth()->user() null**
```
Error: Call to null member function
Fix: Check if user is authenticated before accessing
Use: @auth ... @else ... @endauth
```

**Issue 3: Undefined variable**
```
Error: Undefined variable: $movies
Fix: Verify controller returns view() with data
Use: return view('page', compact('movies'));
```

**Issue 4: Asset paths broken**
```
Error: 404 on stylesheet/image
Fix: Use asset() helper for public files
Wrong: href="style.css"
Right: href="{{ asset('style.css') }}"
```

---

## File Organization Reference

After migration, your structure should be:

```
resources/views/
├── layouts/
│   └── app.blade.php
├── components/
│   ├── header.blade.php
│   ├── footer.blade.php
│   └── movie-card.blade.php
├── home/
│   └── index.blade.php
├── user/
│   └── index.blade.php
├── booking/
│   ├── index.blade.php
│   ├── my-tickets.blade.php
│   └── verify-tickets.blade.php
├── movie/
│   ├── index.blade.php
│   └── show.blade.php
├── admin/
│   ├── dashboard.blade.php
│   ├── movies.blade.php
│   ├── users.blade.php
│   └── (other admin pages).blade.php
├── moderator/
│   ├── dashboard.blade.php
│   ├── screens.blade.php
│   └── (other moderator pages).blade.php
└── counter_staff/
    ├── dashboard.blade.php
    └── (other counter staff pages).blade.php
```

---

## Quick Commands Reference

```bash
# Generate controller
php artisan make:controller MovieController

# Generate model with migration
php artisan make:model Movie -m

# Run migrations
php artisan migrate

# Clear cache
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Start dev server
php artisan serve

# Test routes
php artisan route:list
```

---

## Success Metrics

✅ Migration is successful when:
- All `.blade.php` files render without errors
- No `<?php` tags appear in views
- All navigation works
- Authentication flows properly
- Database queries execute
- All data displays correctly
- No console errors
- Links and forms work

---

## Next Immediate Actions

### TODAY:
1. ✅ Read CONVERSION_SUMMARY.md (what's done)
2. ✅ Read BLADE_CONVERSION_GUIDE.md (detailed reference)
3. ✅ Read BLADE_QUICK_REFERENCE.md (quick examples)
4. [ ] Test home page: `php artisan serve`
5. [ ] Check for errors

### TOMORROW:
1. [ ] Update HomeController (if needed changes)
2. [ ] Test all converted pages
3. [ ] Update ProfileController
4. [ ] Fix any bugs

### THIS WEEK:
1. [ ] Update all core controllers
2. [ ] Convert admin views
3. [ ] Convert moderator views
4. [ ] Full testing

---

## Get Help

1. **Blade Documentation**: https://laravel.com/docs/blade
2. **Eloquent ORM**: https://laravel.com/docs/eloquent
3. **Routing**: https://laravel.com/docs/routing
4. **Authentication**: https://laravel.com/docs/authentication

---

**You're on track! 60% complete. Keep going! 🚀**
