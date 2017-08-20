<?php
/**
 * Part of the Joomla Command Line Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Cli;

class DockerEnvironmentTest extends CliTestCase
{
    public function testDockerEnvironmentProducesNoError()
    {
        $output = $this->runInShell(__DIR__ . '/Fixture/cli-command', false);

        $this->assertEquals(0, $output['return']);
        $this->assertEquals('', $output['stderr']);
    }

    public function testDockerEnvironmentUsesBuildDirectory()
    {
        $output = $this->runInShell(__DIR__ . '/Fixture/cli-command', false);

        $this->assertContains('PHPUNIT_COVERAGE_DATA_DIRECTORY = /var/test/build', $output['stdout']);
    }

    public function testCodeCoverageIsStarted()
    {
        $output = $this->runInShell(__DIR__ . '/Fixture/cli-command', false);

        $this->assertContains('xdebug_code_coverage_started()  = true', $output['stdout']);
    }

    public function testServerArrayIsNotChanged()
    {
        $server = $_SERVER;

        $this->runInShell(__DIR__ . '/Fixture/cli-command', false);

        $this->assertEquals($server, $_SERVER);
    }

    public function DISABLEDtestDumpStdout()
    {
        $output = $this->runInShell(__DIR__ . '/Fixture/cli-command', false);

        $this->assertEquals('', $output['stdout'], 'No output');
    }
}
