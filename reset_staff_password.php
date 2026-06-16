<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'staff@test.com')->first();

if ($user) {
    $newPassword = 'staff123456';
    $user->password = Hash::make($newPassword);
    $user->save();
    
    echo "✓ Password updated successfully!\n";
    echo "Email: staff@test.com\n";
    echo "New Password: {$newPassword}\n";
    echo "\nPlease try logging in with the new password.\n";
} else {
    echo "❌ User not found!\n";
}
