php artisan down &
php artisan optimize:clear &
wait
composer install --optimize-autoloader --no-dev &
yarn install --production &
yarn clean &
wait
yarn build &
php artisan optimize &
wait
php artisan up
