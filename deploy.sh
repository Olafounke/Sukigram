#!/bin/bash


echo "Début du déploiement..."


composer install --no-dev --optimize-autoloader


php bin/console doctrine:migrations:migrate --no-interaction


APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
APP_ENV=prod APP_DEBUG=0 php bin/console cache:warmup


php bin/console asset-mapper:compile

echo "Déploiement terminé !"
