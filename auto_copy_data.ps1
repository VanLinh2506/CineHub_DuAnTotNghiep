# Script tự động tìm và copy dữ liệu từ base cũ

Write-Host "🔍 Tìm kiếm CINEHUB base cũ..." -ForegroundColor Cyan

# Các đường dẫn có thể có base cũ
$possiblePaths = @(
    "C:\xampp\htdocs\CINEHUB",
    "C:\xampp\htdocs\cinehub",
    "C:\wamp\www\CINEHUB",
    "C:\wamp64\www\CINEHUB",
    "D:\xampp\htdocs\CINEHUB"
)

$oldBasePath = $null

foreach ($path in $possiblePaths) {
    if (Test-Path "$path\data\img") {
        $oldBasePath = $path
        Write-Host "✅ Tìm thấy base cũ: $path" -ForegroundColor Green
        break
    }
}

if (-not $oldBasePath) {
    Write-Host "❌ Không tìm thấy base cũ tự động!" -ForegroundColor Red
    Write-Host ""
    $oldBasePath = Read-Host "Nhập đường dẫn đến thư mục CINEHUB cũ"
    
    if (-not (Test-Path "$oldBasePath\data\img")) {
        Write-Host "❌ Đường dẫn không hợp lệ!" -ForegroundColor Red
        pause
        exit
    }
}

Write-Host ""
Write-Host "📁 Base cũ: $oldBasePath" -ForegroundColor Yellow
Write-Host "📁 Base mới: $PSScriptRoot\public\data" -ForegroundColor Yellow
Write-Host ""

$confirmation = Read-Host "Bắt đầu copy? (y/n)"

if ($confirmation -ne 'y') {
    Write-Host "Đã hủy." -ForegroundColor Yellow
    pause
    exit
}

Write-Host ""
Write-Host "🚀 Bắt đầu copy dữ liệu..." -ForegroundColor Cyan
Write-Host ""

# Copy images
if (Test-Path "$oldBasePath\data\img") {
    Write-Host "📸 Copying images..." -ForegroundColor Yellow
    $imgCount = (Get-ChildItem "$oldBasePath\data\img" -Recurse -File).Count
    Write-Host "   Tìm thấy $imgCount files"
    
    Copy-Item "$oldBasePath\data\img\*" "$PSScriptRoot\public\data\img\" -Recurse -Force -ErrorAction SilentlyContinue
    Write-Host "   ✅ Done!" -ForegroundColor Green
} else {
    Write-Host "   ⚠️  Không tìm thấy thư mục img" -ForegroundColor Yellow
}

Write-Host ""

# Copy avatars
if (Test-Path "$oldBasePath\data\avatars") {
    Write-Host "👤 Copying avatars..." -ForegroundColor Yellow
    $avatarCount = (Get-ChildItem "$oldBasePath\data\avatars" -File).Count
    Write-Host "   Tìm thấy $avatarCount files"
    
    Copy-Item "$oldBasePath\data\avatars\*" "$PSScriptRoot\public\data\avatars\" -Recurse -Force -ErrorAction SilentlyContinue
    Write-Host "   ✅ Done!" -ForegroundColor Green
} else {
    Write-Host "   ⚠️  Không tìm thấy thư mục avatars" -ForegroundColor Yellow
}

Write-Host ""

# Optional: Copy videos
Write-Host "🎬 Copy videos? (y/n) - Rất nặng, có thể bỏ qua" -ForegroundColor Yellow
$copyVideos = Read-Host

if ($copyVideos -eq 'y' -and (Test-Path "$oldBasePath\data\phim")) {
    Write-Host "   Copying videos... (Có thể mất vài phút)" -ForegroundColor Yellow
    Copy-Item "$oldBasePath\data\phim\*" "$PSScriptRoot\public\data\phim\" -Recurse -Force -ErrorAction SilentlyContinue
    Write-Host "   ✅ Done!" -ForegroundColor Green
}

Write-Host ""
Write-Host "════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "✅ HOÀN THÀNH!" -ForegroundColor Green
Write-Host "════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""

# Test files
Write-Host "🧪 Kiểm tra một số file..." -ForegroundColor Cyan
$testFiles = @(
    "public\data\img\breaking_bad.jpg",
    "public\data\img\game_of_thrones.jpg",
    "public\data\img\sherlock.jpg"
)

$foundCount = 0
foreach ($file in $testFiles) {
    if (Test-Path $file) {
        Write-Host "   ✅ $file" -ForegroundColor Green
        $foundCount++
    } else {
        Write-Host "   ❌ $file" -ForegroundColor Red
    }
}

Write-Host ""
if ($foundCount -gt 0) {
    Write-Host "✅ Copy thành công! Tìm thấy $foundCount/$($testFiles.Count) ảnh test" -ForegroundColor Green
    Write-Host ""
    Write-Host "🌐 Bây giờ mở browser: http://localhost:8000" -ForegroundColor Yellow
    Write-Host "   Slider và phim sẽ hiển thị ảnh đầy đủ!" -ForegroundColor Yellow
} else {
    Write-Host "⚠️  Không tìm thấy ảnh test. Kiểm tra lại đường dẫn base cũ." -ForegroundColor Yellow
}

Write-Host ""
pause
