#!/usr/bin/env bash

cd tests/acceptance

docker-compose up -d
#docker-compose scale firefox=5
sleep 1

cd ../..
