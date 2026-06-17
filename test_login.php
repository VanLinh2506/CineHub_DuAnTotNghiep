<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

echo "=== Testing Counter Staff Login ===\n\n";

$email = 'staff@test.com';
$password = 'password123';

// 1. Tìm user
$user = User::where('email', $email)->first();

if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}

echo "✓ User found:\n";
echo "  - Name: {$user->name}\n";
echo "  - Email: {$user->email}\n";
echo "  - Role: {$user->role}\n";
echo "  - Theater ID: {$user->theater_id}\n";
echo "  - Status: {$user->status}\n";
echo "  - Is Active: {$user->is_active}\n\n";

// 2. Check password
$passwordMatch = Hash::check($password, $user->password);
echo "Password check: " . ($passwordMatch ? "✓ MATCH" : "❌ NO MATCH") . "\n\n";

if (!$passwordMatch) {
    echo "❌ Password does not match!\n";
    exit(1);
}

// 3. Attempt authentication
$credentials = [
    'email' => $email,
    'password' => $password,
];

echo "Attempting Auth::attempt()...\n";
$authResult = Auth::attempt($credentials);

if ($authResult) {
    echo "✓ Auth::attempt() successful!\n\n";
    
    $authenticatedUser = Auth::user();
    echo "Authenticated user:\n";
    echo "  - ID: {$authenticatedUser->id}\n";
    echo "  - Name: {$authenticatedUser->name}\n";
    echo "  - Role: {$authenticatedUser->role}\n";
    echo "  - Theater ID: {$authenticatedUser->theater_id}\n\n";
    
    // 4. Check redirect logic
    echo "=== Testing Redirect Logic ===\n";
    
    $redirectUrl = route('home'); // Default
    
    if ($authenticatedUser->role === 'admin' || $authenticatedUser->roles()->whereIn('name', ['Super Admin', 'Admin'])->exists()) {
        $redirectUrl = route('admin.index');
        echo "Should redirect to: ADMIN (/admin)\n";
    }
    else if ($authenticatedUser->role === 'moderator' && !empty($authenticatedUser->theater_id) && $authenticatedUser->theater_id != '') {
        $redirectUrl = route('moderator.index');
        echo "Should redirect to: MODERATOR (/moderator)\n";
    }
    else if ($authenticatedUser->role === 'user' && !empty($authenticatedUser->theater_id) && $authenticatedUser->theater_id != '' && is_numeric($authenticatedUser->theater_id)) {
        $redirectUrl = route('counter.index');
        echo "Should redirect to: COUNTER STAFF (/counter)\n";
    } else {
        echo "Should redirect to: HOME (/)\n";
    }
    
    echo "Redirect URL: {$redirectUrl}\n\n";
    
    // 5. Check middleware
    echo "=== Checking Counter Staff Logic ===\n";
    $isCounterStaff = $authenticatedUser->role === 'user' && 
                     !empty($authenticatedUser->theater_id) && 
                     $authenticatedUser->theater_id != '' &&
                     is_numeric($authenticatedUser->theater_id);
    
    echo "Is Counter Staff: " . ($isCounterStaff ? "✓ YES" : "❌ NO") . "\n";
    echo "  - role = 'user': " . ($authenticatedUser->role === 'user' ? "✓" : "❌") . "\n";
    echo "  - theater_id not empty: " . (!empty($authenticatedUser->theater_id) ? "✓" : "❌") . "\n";
    echo "  - theater_id != '': " . ($authenticatedUser->theater_id != '' ? "✓" : "❌") . "\n";
    echo "  - is_numeric(theater_id): " . (is_numeric($authenticatedUser->theater_id) ? "✓" : "❌") . "\n";
    
    echo "\n✅ ALL TESTS PASSED!\n";
    
} else {
    echo "❌ Auth::attempt() FAILED!\n";
    echo "\nPossible reasons:\n";
    echo "  1. User status is not 'active'\n";
    echo "  2. User is_active is 0\n";
    echo "  3. Some authentication guard issue\n";
    
    exit(1);
}
