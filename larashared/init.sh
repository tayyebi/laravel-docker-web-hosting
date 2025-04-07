docker container exec -it larashared cp /app/.env.example /app/.env
docker container exec -it larashared php artisan key:generate
docker container exec -it larashared php artisan telescope:install