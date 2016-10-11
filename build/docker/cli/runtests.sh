#!/usr/bin/env bash

echo "Preparing ..."

WORK_DIR=/var/test

cp /usr/local/lib/php/phpunit_coverage.php $WORK_DIR
cd $WORK_DIR
cp tests/cli/codeception.yml .

echo "Running tests ..."

./libraries/vendor/bin/codecept run --coverage=coverage.serialized cli
return_code=$?

echo "Collecting results ..."

php phpunit_coverage.php
chmod -R 0777 build/reports/coverage-cli

rm phpunit_coverage.php
rm codeception.yml

echo "Done."

exit $return_code
