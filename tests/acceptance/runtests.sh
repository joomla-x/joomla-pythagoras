#!/usr/bin/env bash

docker-compose up -d
#docker-compose scale firefox=5
sleep 1

CURRENT=`pwd`

cd ../..

./libraries/vendor/bin/codecept run -c codeception.yml.dist acceptance

cd $CURRENT

docker-compose stop
docker-compose rm --force
