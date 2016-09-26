<?php
/**
 * Part of the Joomla CMS Installer Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Cms\Installer;

use Joomla\Cms\Installer\Installer;

class InstallerTest extends \PHPUnit_Framework_TestCase
{
	/** @var  Installer */
	private $installer;

	/** @var  string */
	private $dataDirectory;

	public function setUp()
	{
		$this->dataDirectory = __DIR__ . '/tmp';
		mkdir($this->dataDirectory);
		mkdir($this->dataDirectory . '/entities');

		$this->installer = new Installer($this->dataDirectory);
	}

	public function tearDown()
	{

		foreach (glob($this->dataDirectory . '/entities/*') as $file)
		{
			unlink($file);
		}
		rmdir($this->dataDirectory . '/entities');
		rmdir($this->dataDirectory);
	}

	public function testInstallASingleExtension()
	{
		$this->installer->install(__DIR__ . '/data/ext_article');
		$this->installer->finish();

		$this->assertFileExists($this->dataDirectory . '/entities/Article.xml');
	}
}
