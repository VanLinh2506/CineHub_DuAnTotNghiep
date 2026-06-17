<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'staff@test.com')->first();

if ($user) {
    echo "User found: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n";
    echo "Theater ID: {$user->theater_id}\n";
    echo "Password hash starts: " . substr($user->password, 0, 20) . "...\n";
    echo "\nTesting passwords:\n";
    echo "- password123: " . (Hash::check('password123', $user->password) ? '✓ MATCH' : '✗ NO MATCH') . "\n";
    echo "- password: " . (Hash::check('password', $user->password) ? '✓ MATCH' : '✗ NO MATCH') . "\n";
    echo "- 12345678: " . (Hash::check('12345678', $user->password) ? '✓ MATCH' : '✗ NO MATCH') . "\n";
} else {
    echo "User not found!\n";
}
