# Commandes
## Installation de l'envionnement
### Prérequis
 - the latest version of Docker
 - the latest version of composer

### Lancer l'environnement de développement
docker-compose up 
## Avoir accès au conteneur
Avec cette commande vous pourrez vous connecter au conteneur : 
docker exec -it php bash

### Ressource utilisée
https://medium.com/@meherbensalah4/how-to-dockerize-symfony-project-f06bcd735308
## Commande pour créer la base de données
## Créer la bdd 
php bin/console doctrine:database:create
## Faire la migration
php bin/console make:migration
php bin/console doctrine:migrations:migrate
## Créer des données en base avec les fixtures
php bin/console doctrine:fixtures:load
## Commande pour exécuter le projet 
symfony server:start

# Dépôt Git du prof :
https://github.com/Nvmeless/bien-etre




