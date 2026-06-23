<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\User;

try {
    // Check if user exists
    $existing = User::where('email', 'user@test.com')->first();
    if ($existing) {
        echo "User already exists: " . $existing->email . "\n";
        exit;
    }
    
    $user = User::create([
        'name' => 'Test User',
        'email' => 'user@test.com',
        'password' => bcrypt('password123'),
    ]);
    
    echo "Created user: " . $user->email . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
