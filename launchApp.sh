#composer install
#php bin/console lexik:jwt:generate-keypair --overwrite
php bin/console doctrine:database:create
php bin/console make:migration -n
php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:schema:update --force -n
php bin/console doctrine:fixtures:load -n
symfony server:start 