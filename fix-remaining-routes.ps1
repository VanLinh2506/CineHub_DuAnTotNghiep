# PowerShell script to fix remaining route URLs in views

Write-Host "🔄 Fixing remaining route URLs..." -ForegroundColor Cyan

$viewsPath = "resources\views"
$files = Get-ChildItem -Path $viewsPath -Filter "*.blade.php" -Recurse

$replacements = @{
    # Movie index patterns
    'url\([''"]\/\?route=movie\/index&type=phimle[''"]\)' = "route('movies.phimle')"
    'url\([''"]\/\?route=movie\/index&type=phimbo[''"]\)' = "route('movies.phimbo')"
    
    # Movie detail with ID (various patterns)
    'url\([''"]\/\?route=movie\/detail&id=[''"]\s*\.\s*\$movie->id\s*\.\s*[''"][''"]\)' = "route('movies.show', \$movie->id)"
    'url\([''"]\/\?route=movie\/detail&id=[''"]\s*\.\s*\$movie\[''id''\]\s*\.\s*[''"][''"]\)' = "route('movies.show', \$movie['id'])"
    
    # Ticket/QR patterns
    'url\([''"]\/\?route=ticket\/qrcode&id=[''"]\s*\.\s*\$ticket->id\s*\.\s*[''"][''"]\)' = "route('booking.history')"
    'url\([''"]\/\?route=ticket\/qrcode&id=[''"]\s*\.\s*\$ticket\[''id''\]\s*\.\s*[''"][''"]\)' = "route('booking.history')"
    
    # Profile patterns  
    'url\([''"]\/\?route=profile\/updatePreferences[''"]\)' = "route('profile.update')"
    'url\([''"]\/\?route=subscription\/upgrade[''"]\)' = "route('profile.subscriptions')"
    'url\([''"]\/\?route=subscription\/plans[''"]\)' = "route('profile.subscriptions')"
    
    # Booking patterns
    'url\([''"]\/\?route=booking\/verifyTicket[''"]\)' = "route('counter.verifyTicket')"
    'url\([''"]\/\?route=booking\/pickupTicket[''"]\)' = "route('counter.pickupTicket')"
    'url\([''"]\/\?route=home[''"]\)' = "route('home')"
    
    # Notifications
    'url\([''"]\/\?route=notifications\/getUnreadCount[''"]\)' = "route('notifications.index')"
}

$totalReplacements = 0
$modifiedFiles = 0

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw -Encoding UTF8
    $originalContent = $content
    $fileReplacements = 0
    
    foreach ($pattern in $replacements.Keys) {
        $replacement = $replacements[$pattern]
        $newContent = $content -replace $pattern, $replacement
        
        if ($newContent -ne $content) {
            $count = ([regex]::Matches($content, $pattern)).Count
            $fileReplacements += $count
            $content = $newContent
        }
    }
    
    if ($content -ne $originalContent) {
        Set-Content -Path $file.FullName -Value $content -Encoding UTF8 -NoNewline
        $modifiedFiles++
        $totalReplacements += $fileReplacements
        $relativePath = $file.FullName -replace [regex]::Escape($PWD.Path + "\"), ""
        Write-Host "  ✓ $relativePath ($fileReplacements replacements)" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "📊 Summary:" -ForegroundColor Cyan
Write-Host "  Modified files: $modifiedFiles" -ForegroundColor White
Write-Host "  Total replacements: $totalReplacements" -ForegroundColor White
Write-Host ""

if ($modifiedFiles -gt 0) {
    Write-Host "✅ Routes updated successfully!" -ForegroundColor Green
} else {
    Write-Host "ℹ️  No files needed updating." -ForegroundColor Yellow
}
