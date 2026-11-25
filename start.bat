@echo off
setlocal
pushd "%~dp0" || exit /b 1

where php >nul 2>&1
if errorlevel 1 (
    echo PHP belum ada di PATH. Aktifkan PHP dari Laravel Herd.
    goto :failed
)

if not exist ".env" (
    echo File .env belum ada. Siapkan konfigurasi lokal dari .env.example.
    goto :failed
)

if not exist "vendor\autoload.php" (
    echo Dependensi PHP belum ada. Jalankan composer install dulu.
    goto :failed
)

if not exist "public\build\manifest.json" (
    if not exist "node_modules\.bin\vite.cmd" (
        echo Aset frontend belum ada. Jalankan npm ci dulu.
        goto :failed
    )
    call npm.cmd run build
    if errorlevel 1 goto :failed
)

set "APP_URL=http://127.0.0.1:8766"
set "WADAHNGOPI_USE_BUILD=true"
echo WadahNgopi: %APP_URL%
echo Tunggu Laravel siap. Tekan Ctrl+C untuk berhenti.
echo.

call php artisan serve --host=127.0.0.1 --port=8766 --tries=1 --no-reload
if errorlevel 1 goto :failed

popd
exit /b 0

:failed
echo.
pause
popd
exit /b 1
