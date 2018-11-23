#!/bin/bash

printf "\n"
echo "copying environment files..."
printf "\n"
cp recipe-service/.env.dev recipe-service/.env
cp oauth-service/.env.dev oauth-service/.env
cp search-service/.env.dev search-service/.env
cp web-service/.env.dev web-service/.env
printf "\n"
echo "building containers..."
printf "\n"
docker-compose up --build -d
printf "\n"
echo "installing dependencies..."
printf "\n"
docker exec -it ahsanatiq-recipe-service composer install
docker exec -it ahsanatiq-oauth-service composer install
docker exec -it ahsanatiq-search-service composer install
docker exec -it ahsanatiq-web-service composer install
printf "\n"
echo "creating test database in recipe-service..."
printf "\n"
docker exec -it ahsanatiq-recipe-postgres createdb -U hellofresh -O hellofresh hellofresh_testing
docker exec -it ahsanatiq-postgres-oauth createdb -U hellofresh -O hellofresh hellofresh_testing
printf "\n"
echo "gnerating private and public keys for oauth2 authentication..."
printf "\n"
docker exec -it ahsanatiq-oauth-service openssl genrsa -out /server/keys/id_rsa 2048
docker exec -it ahsanatiq-oauth-service openssl rsa -in /server/keys/id_rsa -pubout -out /server/keys/id_rsa.pub
printf "\n"
echo "migrating & seeding the required databases..."
printf "\n"
docker exec -it ahsanatiq-recipe-service php vendor/bin/phinx migrate
docker exec -it ahsanatiq-oauth-service php vendor/bin/phinx migrate
docker exec -it ahsanatiq-oauth-service php vendor/bin/phinx seed:run
docker exec -it ahsanatiq-oauth-service php vendor/bin/phinx migrate -e testing
docker exec -it ahsanatiq-oauth-service php vendor/bin/phinx seed:run -e testing
printf "\n"
echo "run consumer in the search service..."
printf "\n"
docker exec ahsanatiq-search-service nohup /usr/bin/php /server/http/console.php consume:recipes &
printf "\n"
echo "done..."
printf "\n"
