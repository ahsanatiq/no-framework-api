#!/bin/bash

echo "copying environment files..."
printf "\n\n\n"
cp recipe-service/.env.dev recipe-service/.env
cp oauth-service/.env.dev oauth-service/.env
cp search-service/.env.dev search-service/.env
cp web-service/.env.dev web-service/.env
echo "building containers..."
printf "\n\n\n"
docker-compose up -d
echo "installing project dependencies..."
printf "\n\n\n"
docker exec -it ahsanatiqapitest_recipe-service_1 composer install
docker exec -it ahsanatiqapitest_oauth-service_1 composer install
docker exec -it ahsanatiqapitest_search-service_1 composer install
docker exec -it ahsanatiqapitest_web-service_1 composer install
echo "creating test database in recipe-service..."
printf "\n\n\n"
docker exec -it ahsanatiqapitest_recipe-postgres_1 createdb -U hellofresh -O hellofresh hellofresh_testing
echo "gnerating private and public keys for oauth2 authentication..."
printf "\n\n\n"
docker exec -it ahsanatiqapitest_oauth-service_1 openssl genrsa -out /server/keys/id_rsa 2048
docker exec -it ahsanatiqapitest_oauth-service_1 openssl rsa -in /server/keys/id_rsa -pubout -out /server/keys/id_rsa.pub
echo "migrating & seeding the required databases..."
printf "\n\n\n"
docker exec -it ahsanatiqapitest_recipe-service_1 php vendor/bin/phinx migrate
docker exec -it ahsanatiqapitest_oauth-service_1 php vendor/bin/phinx migrate
docker exec -it ahsanatiqapitest_oauth-service_1 php vendor/bin/phinx seed:run
