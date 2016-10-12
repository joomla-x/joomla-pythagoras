<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2010-2013, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPUnit_Selenium
 * @author     Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright  2010-2013 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 1.0.0
 */

require_once 'libraries/vendor/autoload.php';

if (extension_loaded('xdebug'))
{
	if (!getenv('PHPUNIT_COVERAGE_DATA_DIRECTORY'))
	{
		putenv('PHPUNIT_COVERAGE_DATA_DIRECTORY=' . getcwd());
	}

	if (!getenv('PHPUNIT_COVERAGE_WHITELIST'))
	{
		putenv('PHPUNIT_COVERAGE_WHITELIST=' . getcwd());
	}

	if (!getenv('PHPUNIT_TEST_ID'))
	{
		putenv('PHPUNIT_TEST_ID=cli');
	}

	$GLOBALS['PHPUNIT_COVERAGE_COLLECTOR'] = (function() {
		$driver = new \SebastianBergmann\CodeCoverage\Driver\Xdebug;
		$filter = new \SebastianBergmann\CodeCoverage\Filter;

		foreach (preg_split('~\s*,\s*~', getenv('PHPUNIT_COVERAGE_WHITELIST')) as $file)
		{
			if (is_dir($file))
			{
				$filter->addDirectoryToWhitelist($file);
			}
			else
			{
				$filter->addFileToWhitelist($file);
			}
		}

		$codeCoverage = new \SebastianBergmann\CodeCoverage\CodeCoverage($driver, $filter);
		$codeCoverage->setAddUncoveredFilesFromWhitelist(true);
		$codeCoverage->start(getenv('PHPUNIT_TEST_ID'));

		return $codeCoverage;
	})();
}
