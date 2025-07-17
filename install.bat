@echo off
echo 🎵 Installation d'Aviatrax Studio
echo ==================================

REM Vérifier si Composer est installé
composer --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Composer n'est pas installé. Veuillez l'installer d'abord.
    pause
    exit /b 1
)

REM Vérifier si PHP est installé
php --version >nul 2>&1
if errorlevel 1 (
    echo ❌ PHP n'est pas installé. Veuillez l'installer d'abord.
    pause
    exit /b 1
)

echo 📦 Installation des dépendances Composer...
composer install --no-dev --optimize-autoloader

echo 🔑 Génération de la clé d'application...
php artisan key:generate

echo 🗄️ Configuration de la base de données...
echo La base de données MySQL 'aviator_music_studio' doit être créée manuellement.
echo Utilisateur: root@localhost
echo Mot de passe: 1999
echo Base de données: aviator_music_studio
echo.

set /p confirm="La base de données est-elle prête ? (y/n): "
if /i not "%confirm%"=="y" (
    echo ❌ Installation annulée. Veuillez créer la base de données d'abord.
    pause
    exit /b 1
)

echo 🔄 Exécution des migrations...
php artisan migrate --force

echo 🌱 Seeding des données initiales...
php artisan db:seed --force

echo 📁 Création des dossiers de stockage...
if not exist "storage\app\public" mkdir "storage\app\public"
if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "storage\logs" mkdir "storage\logs"

echo ✨ Installation terminée avec succès !
echo.
echo 🎯 Prochaines étapes :
echo 1. Démarrer le serveur : php artisan serve
echo 2. Accéder à l'application : http://localhost:8000
echo 3. Se connecter avec :
echo    - Email : admin@aviatrax-studio.com
echo    - Mot de passe : password
echo.
echo 🎵 Bonne utilisation d'Aviatrax Studio !
pause 