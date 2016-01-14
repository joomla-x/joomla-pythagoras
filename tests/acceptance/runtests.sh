#!/usr/bin/env bash

cd tests/acceptance

docker-compose up -d
#docker-compose scale firefox=5
sleep 1

cd ../..

libraries/vendor/bin/codecept run -c codeception.yml.dist acceptance

cd tests/acceptance

docker-compose stop
docker-compose rm --force

cd ../..
