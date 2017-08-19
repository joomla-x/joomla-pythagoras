<?php
/**
 * Part of the Joomla CMS Installer Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Cms\Installer;

use Joomla\Cms\Installer\Installer;
use Joomla\Cms\ServiceProvider\EventDispatcherServiceProvider;
use Joomla\Cms\ServiceProvider\ExtensionFactoryServiceProvider;
use Joomla\DI\Container;
use Joomla\ORM\Service\StorageServiceProvider;

class InstallerTest extends \PHPUnit_Framework_TestCase
{
	/** @var  Installer */
	private $installer;

	/** @var  string */
	private $dataDirectory;

	public function setUp()
	{
		$this->dataDirectory = __DIR__ . '/tmp';

		$this->mkdir($this->dataDirectory . '/entities');

		$container       = new Container;
		$container->set('ConfigDirectory', JPATH_ROOT);
		$container->registerServiceProvider(new StorageServiceProvider, 'repository');
		$container->registerServiceProvider(new EventDispatcherServiceProvider, 'dispatcher');
		$container->registerServiceProvider(new ExtensionFactoryServiceProvider, 'extension_factory');

		$this->installer = new Installer($this->dataDirectory, $container);
	}

	public function tearDown()
	{
		$this->rmdir($this->dataDirectory);
	}

	/**
	 * @testdox Without relations, an entity definition is cached as is
	 */
	public function testInstallASingleExtension()
	{
		$this->installer->install(__DIR__ . '/data/ext_article');
		$this->installer->finish();

		$this->assertFileExists($this->dataDirectory . '/entities/Article.xml');

		$originalStructure = $this->getStructureFromFile(__DIR__ . '/data/ext_article/entities/Article.xml');
		$cachedStructure   = $this->getStructureFromFile($this->dataDirectory . '/entities/Article.xml');

		$this->assertEquals($originalStructure, $cachedStructure);
	}

	/**
	 * @testdox belongsTo relations from one entity create a hasMany relationship on the other.
	 */
	public function testResolveBelongsTo()
	{
		$this->installer->install(__DIR__ . '/data/ext_article');
		$this->installer->install(__DIR__ . '/data/ext_extra');
		$this->installer->finish();

		$originalStructure = $this->getStructureFromFile(__DIR__ . '/data/ext_article/entities/Article.xml');
		$this->assertFalse(in_array('extras', $originalStructure['relations']['hasMany']));

		$cachedStructure = $this->getStructureFromFile($this->dataDirectory . '/entities/Article.xml');
		$this->assertTrue(in_array('extras', $cachedStructure['relations']['hasMany']));
	}

	/**
	 * @testdox hasOne and HasMany relations from one entity create a belongsTo relationship on the other.
	 */
	public function testResolveHasOneOrHasMany()
	{
		$this->installer->install(__DIR__ . '/data/ext_article');
		$this->installer->install(__DIR__ . '/data/ext_category');
		$this->installer->finish();

		$originalStructure = $this->getStructureFromFile(__DIR__ . '/data/ext_article/entities/Article.xml');
		$this->assertFalse(in_array('category', $originalStructure['relations']['belongsTo']));

		$cachedStructure = $this->getStructureFromFile($this->dataDirectory . '/entities/Article.xml');
		$this->assertTrue(in_array('category', $cachedStructure['relations']['belongsTo']));
	}

	/**
	 * @param $xmlFile
	 *
	 * @return array
	 */
	private function getStructureFromFile($xmlFile)
	{
		$document = new \DOMDocument;
		$document->load($xmlFile);

		return $this->getStructure($document->documentElement);
	}

	private function getStructure(\DOMElement $node)
	{
		$structure = [];

		if (!$node->hasChildNodes())
		{
			return $structure;
		}

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

		return $structure;
	}

	private function mkdir($dir, $clean = true)
	{
		if (empty($dir))
		{
			return;
		}

		$basedir = dirname($dir);

		if (!file_exists($basedir))
		{
			$this->mkdir($basedir, false);
		}

		if (!file_exists($dir))
		{
			mkdir($dir);
		}

		if ($clean)
		{
			$this->clearDir($dir);
		}
	}

	private function rmdir($dir)
	{
		$this->clearDir($dir);
		rmdir($dir);
	}

	private function clearDir($dir)
	{
		foreach (glob($dir . '/*') as $file)
		{
			if (is_dir($file))
			{
				$this->clearDir($file);
				rmdir($file);
			}
			else
			{
				unlink($file);
			}
		}
	}
}
