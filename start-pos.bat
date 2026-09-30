@echo off
title Tokomu - Sistem Kasir Pintar
color 0A
echo ========================================================
echo        TOKOMU - SISTEM KASIR PINTAR & MULTI-TOKO
echo ========================================================
echo.

set PATH=C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64;%PATH%

echo Memulai Server Tokomu di Port 8097...
echo.
echo URL: http://localhost:8097
echo.
echo Tekan Ctrl+C untuk menghentikan server jika sudah selesai.
echo ========================================================
echo.

start "" "http://localhost:8097"
php -S localhost:8097 index.php
pause
