<?php

namespace Joomla\Tests;

use PDO;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit_Extensions_Database_TestCase;

/**
 * Class DatabaseTestCase
 *
 * @package Joomla\Tests
 */
abstract class DatabaseTestCase extends PHPUnit_Extensions_Database_TestCase
{
    static private $pdo = null;

    private $conn = null;

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new PDO($_ENV['DB_DSN'], $_ENV['DB_USER'], $_ENV['DB_PASSWD']);
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $_ENV['DB_DBNAME']);
        }

        return $this->conn;
    }
}
