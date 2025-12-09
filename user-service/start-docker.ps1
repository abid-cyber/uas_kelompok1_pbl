# Script untuk memeriksa dan memulai Docker Desktop

Write-Host "Checking Docker Desktop status..." -ForegroundColor Cyan

# Cek apakah Docker sudah berjalan
try {
    $dockerInfo = docker info 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Docker Desktop is running!" -ForegroundColor Green
        Write-Host "`nStarting docker-compose..." -ForegroundColor Cyan
        docker-compose up -d
    }
} catch {
    Write-Host "✗ Docker Desktop is not running" -ForegroundColor Red
    Write-Host "`nPlease do one of the following:" -ForegroundColor Yellow
    Write-Host "1. Open Docker Desktop from Start Menu" -ForegroundColor White
    Write-Host "2. Wait until Docker Desktop is fully started" -ForegroundColor White
    Write-Host "3. Then run: docker-compose up -d" -ForegroundColor White
    Write-Host "`nOr use Laragon instead (no Docker needed):" -ForegroundColor Yellow
    Write-Host "   php artisan serve" -ForegroundColor White
}

