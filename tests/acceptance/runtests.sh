#!/usr/bin/env bash

tests/acceptance/docker-start.sh

libraries/vendor/bin/codecept run -c codeception.yml.dist acceptance
status=$?

tests/acceptance/docker-stop.sh

exit $status
