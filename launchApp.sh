#!/bin/sh

# Définir les variables d'environnement pour tout le script
export APP_ENV=prod
export APP_DEBUG=0

echo "Generating JWT keys" 
echo "Secret Key Path: $JWT_SECRET_KEY"
echo "Public Key Path: $JWT_PUBLIC_KEY"
echo "Passphrase: $JWT_PASSPHRASE"


# Définir les permissions pour le répertoire var/
chown -R www-data:www-data /var/www/var
chmod -R 775 /var/www/var

# Installer les dépendances PHP
composer install --no-dev --optimize-autoloader --no-interaction

# Générer les clés JWT
php bin/console lexik:jwt:generate-keypair --overwrite -n

# Définir les permissions pour les clés JWT
chown www-data:www-data config/jwt/private.pem config/jwt/public.pem
chmod 600 config/jwt/private.pem
chmod 644 config/jwt/public.pem

# Vider le cache
php bin/console cache:clear --env=prod

# Créer la base de données si elle n'existe pas déjà
php bin/console doctrine:database:create --if-not-exists

# Mettre à jour le schéma de la base de données (si nécessaire)
php bin/console doctrine:schema:update --force

# Appliquer les migrations s'il y en a
php bin/console doctrine:migrations:migrate -n --allow-no-migration || true

# Charger les fixtures
php bin/console doctrine:fixtures:load -n

# Démarrer Apache
apache2-foreground
