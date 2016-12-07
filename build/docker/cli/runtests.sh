#!/usr/bin/env bash

echo "Preparing ..."

WORK_DIR=/var/test

cd $WORK_DIR
cp /usr/local/lib/php/prepend.php .
cp /usr/local/lib/php/append.php .
cp /usr/local/lib/php/phpunit_coverage.php .
cp tests/cli/codeception.yml .

echo "Running tests ..."

./libraries/vendor/bin/codecept run --coverage=coverage.cli.php cli
return_code=$?

echo "Collecting results ..."

php phpunit_coverage.php

rm prepend.php
rm append.php
rm phpunit_coverage.php
rm codeception.yml

echo "Done."

exit $return_code
