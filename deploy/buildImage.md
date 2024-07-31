# Build le back 
docker build -t rg.fr-par.scw.cloud/registryforgejdr/backforgejdr:0.1 -f docker/php/Dockerfile .

# Push l'image
docker push rg.fr-par.scw.cloud/registryforgejdr/backforgejdr:0.2

# Déployer le back
scw container container create name=container-back-forge-jdr registry-image=rg.fr-par.scw.cloud/registryforgejdr/backforgejdr:0.1 min-scale=1 max-scale=1 memory-limit=128 cpu-limit=70 timeout=300s namespace-id=940522cc-2a4a-4ead-86a0-a37225e447c3 max-concurrency=1 protocol=unknown_protocol sandbox=v1 privacy=public port=8000 environment-variables.DATABASE_URL=mysql://USER:PASSWORD@51.159.74.117:8867/app?serverVersion=8.0.0&charset=utf8 environment-variables.JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem environment-variables.JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem environment-variables.APP_ENV=dev environment-variables.JWT_PASSPHRASE='Amodifier'


# Reset 
docker rm -f phpbackforgejdr
docker rm -f mariadb
docker network create dev-environment
docker volume create db-data

# Lancer sans docker compose les images
docker run -d \
  --name mariadb \
  --restart always \
  -e MYSQL_ROOT_PASSWORD='password' \
  -e MYSQL_USER='maria' \
  -e MYSQL_PASSWORD='password' \
  -p 3306:3306 \
  --network dev-environment \
  rg.fr-par.scw.cloud/registryforgejdr/mariadb:10.11.2

docker run -d \
  --name phpbackforgejdr \
  --restart always \
  -e DATABASE_URL='mysql://admin:zoKzum-1supsi-xatkov@51.159.74.117:8867/app?serverVersion=8.0.0&charset=utf8' \
  -p 8741:8000 \
  --network dev-environment \
  rg.fr-par.scw.cloud/registryforgejdr/backforgejdr:0.1



