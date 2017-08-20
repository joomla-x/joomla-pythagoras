<?php
/**
 * Part of the Joomla Command Line Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Cli;

class VersionTest extends CliTestCase
{
    /**
     * @testdox `joomla version` displays the full version info
     */
    public function testWithoutParams()
    {
        $output = $this->runInShell('src/joomla version');
        $this->assertContains('Joomla! X.0.0 Dev', $output['stdout']);
        $this->assertContains('Pythagoras', $output['stdout']);
    }

    public function testHelp()
    {
        $output = $this->runInShell('src/joomla version --help');
        $this->assertContains('version [options]', $output['stdout']);
        $this->assertContains('--long', $output['stdout']);
        $this->assertContains('--short', $output['stdout']);
        $this->assertContains('--release', $output['stdout']);
    }

    public function getOptions()
    {
        $long    = 'Joomla! X.0.0 Dev [ Pythagoras ]';
        $short   = 'X.0.0';
        $release = 'X.0';

        return [
            'long'    => ['--long', $long],
            'l'       => ['-l', $long],
            'short'   => ['--short', $short],
            's'       => ['-s', $short],
            'release' => ['--release', $release],
            'r'       => ['-r', $release],
        ];
    }

    /**
     * @param   string $option
     * @param   string $expected
     *
     * @dataProvider getOptions
     */
    public function testOptions($option, $expected)
    {
        $output = $this->runInShell('src/joomla version ' . $option);
        $this->assertEquals($expected, $output['stdout']);
    }
}
