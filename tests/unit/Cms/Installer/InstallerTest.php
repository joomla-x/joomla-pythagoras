<?php
/**
 * Part of the Joomla CMS Installer Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Cms\Installer;

use DOMDocument;
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

	/**
	 * @testdox Without relations, an entity definition is cached as is
	 */
	public function testInstallASingleExtension()
	{
		$this->installer->install(__DIR__ . '/data/ext_article');
		$this->installer->finish();

		$this->assertFileExists($this->dataDirectory . '/entities/Article.xml');

		$expected = new DOMDocument;
		$expected->load(__DIR__ . '/data/ext_article/entities/Article.xml');
		$actual   = new DOMDocument;
		$actual->load($this->dataDirectory . '/entities/Article.xml');

		$this->assertEquals($this->getStructure($expected->documentElement), $this->getStructure($actual->documentElement));
	}

	/**
	 * @testdox belongsTo relations from one entity create a hasMany relationship on the other.
	 */
	public function testResolveBelongsTo()
	{
		$this->installer->install(__DIR__ . '/data/ext_article');
		$this->installer->install(__DIR__ . '/data/ext_extra');
		$this->installer->finish();

		$expected = new DOMDocument;
		$expected->load(__DIR__ . '/data/ext_article/entities/Article.xml');
		$expectedStructure = $this->getStructure($expected->documentElement);

		$this->assertFalse(in_array('extras', $expectedStructure['relations']['hasMany']));

		$actual = new DOMDocument;
		$actual->load($this->dataDirectory . '/entities/Article.xml');
		$actualStructure = $this->getStructure($actual->documentElement);

		$this->assertTrue(in_array('extras', $actualStructure['relations']['hasMany']));
	}

	protected function getStructure(\DOMElement $node)
	{
		$structure = [];

		if ($node->hasChildNodes())
		{
			foreach ($node->childNodes as $child)
			{
				if ($child->nodeType != XML_ELEMENT_NODE)
				{
					continue;
				}

				/** @var \DOMElement $child */
				$name = null;

				if ($child->hasAttributes())
				{
					$attribute = $child->attributes->getNamedItem('name');
					$name      = !empty($attribute) ? $attribute->nodeValue : null;
				}

				if (!empty($name))
				{
					$structure[$child->nodeName][] = $name;
					continue;
				}

				$structure[$child->nodeName] = $this->getStructure($child);
			}
		}

		return $structure;
	}
}
