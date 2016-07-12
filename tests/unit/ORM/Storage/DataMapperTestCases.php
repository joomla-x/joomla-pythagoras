<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM\Storage;

use Doctrine\DBAL\Connection;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Entity\EntityRegistry;
use Joomla\ORM\IdAccessorRegistry;
use Joomla\ORM\Storage\CollectionFinderInterface;
use Joomla\ORM\Storage\Csv\CsvDataGateway;
use Joomla\ORM\Storage\Csv\CsvDataMapper;
use Joomla\ORM\Storage\Doctrine\DoctrineDataMapper;
use Joomla\ORM\Storage\EntityFinderInterface;
use Joomla\Tests\Unit\ORM\Mocks\Article;
use PHPUnit\Framework\TestCase;

class DataMapperTestCases extends TestCase
{
	/** @var  CsvDataMapper|DoctrineDataMapper */
	protected $dataMapper;

	/** @var  \PHPUnit_Framework_MockObject_MockObject|CsvDataGateway|Connection */
	protected $connection;

	/** @var  \PHPUnit_Framework_MockObject_MockObject|EntityBuilder */
	protected $builder;

	/** @var  \PHPUnit_Framework_MockObject_MockObject|EntityRegistry */
	protected $entityRegistry;

	/** @var  \PHPUnit_Framework_MockObject_MockObject|IdAccessorRegistry */
	protected $idAccessorRegistry;

	/** @var array  */
	protected $articles = [
		1 => [
			'id'        => 1,
			'title'     => 'One',
			'teaser'    => 'Teaser 1',
			'body'      => 'Body 1',
			'author'    => 'Author 1',
			'license'   => 'CC',
			'parent_id' => 0
		]
	];

	public function setUp()
	{
		$this->idAccessorRegistry = $this->createMock(IdAccessorRegistry::class);
		$this->entityRegistry     = $this->createMock(EntityRegistry::class);
		$this->builder            = $this->createMock(EntityBuilder::class);

		$this->builder
			->expects($this->any())
			->method('reduce')
			->willReturnCallback(
				function ($entity) {
					return get_object_vars($entity);
				}
			);
	}

	/**
	 * @testdox Entities fetched by id are turned into objects using the EntityBuilder
	 */
	public function testGetById()
	{
		$this->builder
			->expects($this->any())
			->method('castToEntity')
			->with([$this->articles[1]])
			->willReturn([(object) $this->articles[1]]);

		$article = $this->dataMapper->getById(1);

		$this->assertEquals(1, $article->id);
	}

	/**
	 * findOne() returns an EntityFinder
	 */
	public function testFindOne()
	{
		$this->assertInstanceOf(EntityFinderInterface::class, $this->dataMapper->findOne());
	}

	/**
	 * findAll() returns a CollectionFinder
	 */
	public function testFindAll()
	{
		$this->assertInstanceOf(CollectionFinderInterface::class, $this->dataMapper->findAll());
	}

	/**
	 * insert() delegates insertion to connection
	 */
	public function testInsert()
	{
		$this->connection
			->expects($this->once())
			->method('insert')
			->with($this->anything());

		$this->dataMapper->insert(new Article(), $this->idAccessorRegistry);
	}

	/**
	 * update() delegates insertion to connection
	 */
	public function testUpdate()
	{
		$this->connection
			->expects($this->once())
			->method('update');

		$this->dataMapper->update(new Article(), $this->idAccessorRegistry);
	}

	/**
	 * delete() delegates insertion to connection
	 */
	public function testDelete()
	{
		$this->connection
			->expects($this->once())
			->method('delete')
			->willReturn(1);

		$this->dataMapper->delete(new Article(), $this->idAccessorRegistry);
	}
}
