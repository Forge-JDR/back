## Dépôt Git du prof :
https://github.com/Nvmeless/bien-etre

## Commande pour exécuter le projet 
symfony server:start

## Commande doker pour lancer la bdd :
docker run --name mariadbtest -e MYSQL_ROOT_PASSWORD=password -e MYSQL_USER=maria -e MYSQL_PASSWORD=password -p 3306:3306 -d mariadb:10.6docker run --name mariadbtest -e MYSQL_ROOT_PASSWORD=password -e MYSQL_USER=maria -e MYSQL_PASSWORD=password -p 3306:3306 -d mariadb:10.6

## A ajouter au .env si vous utilisez la bdd dockerisé
DATABASE_URL="mysql://root:password@127.0.0.1:3306/app?serverVersion=16&charset=utf8"

