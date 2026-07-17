@echo off
echo ========================================
echo Nettoyage et optimisation Laravel
echo ========================================
echo.

cd /d "%~dp0"

echo 1. Nettoyage du cache...
php artisan cache:clear

echo.
echo 2. Nettoyage de la configuration...
php artisan config:clear

echo.
echo 3. Nettoyage de la vue...
php artisan view:clear

echo.
echo 4. Nettoyage des routes...
php artisan route:clear

echo.
echo 5. Nettoyage des events...
php artisan event:clear

echo.
echo 6. Optimisation...
php artisan optimize

echo.
echo ========================================
echo Nettoyage termine !
echo ========================================
echo.
pause