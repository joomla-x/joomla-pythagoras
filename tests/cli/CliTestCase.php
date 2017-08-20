<?php
/**
 * Part of the Joomla Command Line Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Cli;

class CliTestCase extends \PHPUnit_Framework_TestCase
{
    const STDIN  = 0;
    const STDOUT = 1;
    const STDERR = 2;

    protected function runInShell($command, $bail = true)
    {
        putenv('PHPUNIT_TEST_ID=' . xdebug_call_class() . '::' . xdebug_call_function());
        $proc = proc_open(
            'php -c build/config/php_coverage.ini ' . $command,
            [
                self::STDOUT => ["pipe", "w"],
                self::STDERR => ["pipe", "w"],
            ],
            $pipes
        );

        if (!is_resource($proc)) {
            $this->fail('Unable to start process.');
        }

        $stdout = stream_get_contents($pipes[self::STDOUT]);
        fclose($pipes[self::STDOUT]);

        $stderr = stream_get_contents($pipes[self::STDERR]);
        fclose($pipes[self::STDERR]);

        $return = proc_close($proc);

        if ($bail && $return != 0) {
            $this->fail("$stderr\nCommand exited with a non-zero value ({$return}).");
        }

        return [
            'return' => $return,
            'stdout' => $stdout,
            'stderr' => $stderr,
        ];
    }
}
