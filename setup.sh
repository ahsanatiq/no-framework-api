#!/bin/bash

printf "\n\n\n"
echo "copying environment files..."
printf "\n\n\n"
cp recipe-service/.env.dev recipe-service/.env
cp oauth-service/.env.dev oauth-service/.env
cp search-service/.env.dev search-service/.env
cp web-service/.env.dev web-service/.env
printf "\n\n\n"
echo "building containers..."
printf "\n\n\n"
docker-compose up -d
printf "\n\n\n"
echo "installing project dependencies..."
printf "\n\n\n"
docker exec -it ahsanatiq-recipe-service composer install
docker exec -it ahsanatiq-oauth-service composer install
docker exec -it ahsanatiq-search-service composer install
docker exec -it ahsanatiq-web-service composer install
printf "\n\n\n"
echo "creating test database in recipe-service..."
printf "\n\n\n"
docker exec -it ahsanatiq-recipe-postgres createdb -U hellofresh -O hellofresh hellofresh_testing
printf "\n\n\n"
echo "gnerating private and public keys for oauth2 authentication..."
printf "\n\n\n"
docker exec -it ahsanatiq-oauth-service openssl genrsa -out /server/keys/id_rsa 2048
docker exec -it ahsanatiq-oauth-service openssl rsa -in /server/keys/id_rsa -pubout -out /server/keys/id_rsa.pub
printf "\n\n\n"
echo "migrating & seeding the required databases..."
printf "\n\n\n"
docker exec -it ahsanatiq-recipe-service php vendor/bin/phinx migrate
docker exec -it ahsanatiq-oauth-service php vendor/bin/phinx migrate
docker exec -it ahsanatiq-oauth-service php vendor/bin/phinx seed:run
