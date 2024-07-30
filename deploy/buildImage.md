docker rm -f phpbackforgejdr
docker rm -f mariadb
docker network create dev-environment
docker volume create db-data
docker run -d \
  --name mariadb \
  --restart always \
  -e MYSQL_ROOT_PASSWORD='password' \
  -e MYSQL_USER='maria' \
  -e MYSQL_PASSWORD='password' \
  -p 3306:3306 \
  --network dev-environment \
  rg.fr-par.scw.cloud/registryforgejdr/mariadb:10.11.2

docker build -t rg.fr-par.scw.cloud/registryforgejdr/backforgejdr:0.1 -f docker/php/Dockerfile .


docker run -d \
  --name phpbackforgejdr \
  --restart always \
  -e DATABASE_URL='mysql://admin:zoKzum-1supsi-xatkov@51.159.74.117:8867/app?serverVersion=8.0.0&charset=utf8' \
  -p 8741:8000 \
  --network dev-environment \
  rg.fr-par.scw.cloud/registryforgejdr/backforgejdr:0.1



