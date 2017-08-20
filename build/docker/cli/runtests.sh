#!/usr/bin/env bash

echo "Preparing ..."

WORK_DIR=/var/test

cd $WORK_DIR
cp /usr/local/lib/php/prepend.php .
cp /usr/local/lib/php/append.php .
cp /usr/local/lib/php/phpunit_coverage.php .

echo "Running tests ..."

./vendor/bin/robo test:cli --coverage
return_code=$?

echo "Collecting results ..."

php phpunit_coverage.php

rm prepend.php
rm append.php
rm phpunit_coverage.php

echo "Done."

exit $return_code
