sudo docker compose build
sudo docker compose up -d
sudo docker compose exec app composer install --optimize-autoloader --no-dev
sudo docker compose exec app php artisan key:generate
sudo docker compose exec app sh deploy.sh
