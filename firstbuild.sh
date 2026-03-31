docker compose build
docker compose up -d
docker compose exec app composer install --optimize-autoloader --no-dev
docker compose exec app php artisan key:generate
docker compose exec app sh deploy.sh
