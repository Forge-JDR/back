# Forge JDR — API

Branche de travail : `reprise`. Le MVP couvre les comptes, les JDR personnalisés, les modèles de fiche, les parties, les personnages et le journal de campagne. Toutes les parties sont privées et gérées par leur créateur.

## Installation locale

Prérequis : Git, Docker et Docker Compose. Cloner les deux dépôts sur `reprise`.

Dans ce dépôt, sous Linux/macOS :

```sh
export LOCAL_UID=$(id -u) LOCAL_GID=$(id -g)
docker compose build api
docker compose up -d
# Installer les dépendances depuis le lock versionné, avec le propriétaire des fichiers.
docker compose exec --user "$LOCAL_UID:$LOCAL_GID" api composer install --no-interaction
docker compose exec --user "$LOCAL_UID:$LOCAL_GID" api php bin/console lexik:jwt:generate-keypair --skip-if-exists
docker compose exec --user "$LOCAL_UID:$LOCAL_GID" api php bin/console doctrine:migrations:migrate --no-interaction
```

L'API écoute sur `http://localhost:8741`. La base MariaDB et ses données restent dans un volume ; aucun port SQL n'est publié. Les valeurs de `.env` sont uniquement des défauts locaux. Utiliser `.env.local` ou les variables de déploiement pour les remplacer. Les clés JWT restent hors Git. Pour un hôte utilisant un autre UID, les variables ci-dessus permettent au processus Apache de lire les fichiers montés.

Dans le dépôt front, suivre son README puis ouvrir `http://localhost:3000`. L'origine API ne doit pas se terminer par `/api`.

## Tests isolés

Les anciens tests de `tests/Controller` contactaient une API distante et écrivaient dans sa base. Ils ne font plus partie de la suite exécutée par défaut. Les tests actuels utilisent uniquement une base SQLite de test, vérifiée avant toute réinitialisation.

```sh
docker compose exec --user "$LOCAL_UID:$LOCAL_GID" -e APP_ENV=test -e DATABASE_URL='sqlite:///%kernel.project_dir%/var/test.db' api php bin/console lexik:jwt:generate-keypair --skip-if-exists
docker compose exec --user "$LOCAL_UID:$LOCAL_GID" -e APP_ENV=test -e DATABASE_URL='sqlite:///%kernel.project_dir%/var/test.db' api vendor/bin/phpunit
python3 tests/reprise/startup_test.py
```

Les clés de test sont séparées des clés de développement, dans `var/jwt-test`. La CI exécute les tests et construit l'image de production ; elle ne déploie pas automatiquement.

## Migrations et données existantes

Deux migrations sont versionnées : `Version20261012000100` établit le schéma historique sur une base vierge ; `Version20261012000200` ajoute les parties/fiches et agrandit le contenu des wikis sans effacer leurs données.

Pour une base existante : sauvegarder et restaurer une copie isolée, comparer son schéma avec la migration historique, puis seulement si ce schéma est équivalent, marquer la première migration comme appliquée avec `doctrine:migrations:version 'DoctrineMigrations\Version20261012000100' --add`. Appliquer ensuite la seconde migration. Ne pas exécuter la migration initiale sur une base peuplée. Les différences éventuelles doivent être réconciliées sur la copie avant l'opération réelle. Les migrations MySQL/MariaDB utilisent du DDL non transactionnel ; le retour arrière passe par une restauration vérifiée.

## Préparer la production

```sh
docker build --target production -f docker/php/Dockerfile -t forge-jdr-api:reprise .
```

L'image ne contient ni fixtures ni clés privées et utilise Composer sans dépendances de développement. Le démarrage ne lance aucune opération sur la base. Avant de démarrer : fournir `APP_ENV=prod`, `APP_DEBUG=0`, `APP_SECRET`, `DATABASE_URL`, `JWT_SECRET_KEY`, `JWT_PUBLIC_KEY`, `JWT_PASSPHRASE` et `CORS_ALLOW_ORIGIN` ; provisionner une paire de clés persistante lisible par `www-data`, appliquer les migrations explicitement et prévoir un stockage persistant pour `public/uploads/images`. Publier le port interne **80** derrière HTTPS. `CORS_ALLOW_ORIGIN` est une expression régulière ancrée sur l'origine exacte du front.

Dans l'environnement cloud avec interception TLS, les builds acceptent un secret BuildKit `proxy_ca` contenant le certificat fourni par la plateforme. La vérification TLS reste activée. Ce paramètre n'est pas nécessaire dans un environnement Docker habituel.

Le domaine, le compte d'hébergement et ses accès ne sont pas encore configurés. Voir [le plan de reprise](docs/REPRISE.md) et [le contrat MVP](docs/MVP_API.md).
