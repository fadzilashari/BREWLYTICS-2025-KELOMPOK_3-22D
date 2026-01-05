docker compose exec app php artisan migrate:fresh
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed --class=UserSeeder
