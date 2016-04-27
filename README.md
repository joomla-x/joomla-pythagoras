# Joomlax structure

# This branch is pre alpha, it is far from finished!!

## Todo's
Some more work needs to be done:

- Connection sharing
- Better test coverage
- Real world example
- Doctrine finder's need to work per entity

## Installation
To make it running execute the following command on your command line within the root directory:

`composer install`

## Demo

#### Setup
We prepared a little script which creates a sqlite database file with an article table and one entry with the id 1. On the command line run:

`php installation/demo.php`

This will create the database file demo.sqlite in the installation folder. Make sure the database settings in the file config/database.ini do pint to that file.

#### Browser
Open your browser with the following url:

*http://{{address}}/{{path}}/?id=1&entity=article*

## PHPUnit
You can run the tests with codeception like `./libraries/vendor/bin/robo test:unit` or good old `phpunit`.