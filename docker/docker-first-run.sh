#!/bin/bash

echo "Installing vendors ..."
docker exec -it -u www-data nrdb-dev bash -c "composer install"

echo "Initializing the database and importing the card data ..."
docker exec -it -u www-data nrdb-dev bash -c "php bin/console doctrine:schema:update --force; php bin/console app:import:std -f cards"

echo "TODO: import card images"

echo "All done!"
