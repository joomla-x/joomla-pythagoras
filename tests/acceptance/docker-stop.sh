#!/usr/bin/env bash

cd tests/acceptance

docker-compose stop
docker-compose rm --force

cd ../..
