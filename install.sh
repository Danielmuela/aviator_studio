#!/bin/bash

echo "🎵 Installation d'Aviatrax Studio"
echo "=================================="

# Vérifier si Composer est installé
if ! command -v composer &> /dev/null; then
    echo "❌ Composer n'est pas installé. Veuillez l'installer d'abord."
    exit 1
fi

# Vérifier si PHP est installé
if ! command -v php &> /dev/null; then
    echo "❌ PHP n'est pas installé. Veuillez l'installer d'abord."
    exit 1
fi

echo "📦 Installation des dépendances Composer..."
composer install --no-dev --optimize-autoloader

echo "🔑 Génération de la clé d'application..."
php artisan key:generate

echo "🗄️ Configuration de la base de données..."
echo "La base de données MySQL 'aviator_music_studio' doit être créée manuellement."
echo "Utilisateur: root@localhost"
echo "Mot de passe: 1999"
echo "Base de données: aviator_music_studio"
echo ""

# Demander confirmation pour continuer
read -p "La base de données est-elle prête ? (y/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Installation annulée. Veuillez créer la base de données d'abord."
    exit 1
fi

echo "🔄 Exécution des migrations..."
php artisan migrate --force

echo "🌱 Seeding des données initiales..."
php artisan db:seed --force

echo "📁 Création des dossiers de stockage..."
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

echo "🔒 Configuration des permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

echo "✨ Installation terminée avec succès !"
echo ""
echo "🎯 Prochaines étapes :"
echo "1. Démarrer le serveur : php artisan serve"
echo "2. Accéder à l'application : http://localhost:8000"
echo "3. Se connecter avec :"
echo "   - Email : admin@aviatrax-studio.com"
echo "   - Mot de passe : password"
echo ""
echo "🎵 Bonne utilisation d'Aviatrax Studio !" 