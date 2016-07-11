<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM\Repository;

use Joomla\ORM\Operator;
use Joomla\ORM\Repository\Repository;
use Joomla\ORM\Repository\RepositoryInterface;
use Joomla\ORM\Storage\CollectionFinderInterface;
use Joomla\ORM\Storage\DataMapperInterface;
use Joomla\ORM\Storage\EntityFinderInterface;
use Joomla\ORM\UnitOfWork\UnitOfWorkInterface;
use Joomla\Tests\Unit\ORM\Mocks\Article;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
	/** @var  EntityFinderInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $entityFinder;

	/** @var  CollectionFinderInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $collectionFinder;

	/** @var  DataMapperInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $dataMapper;

	/** @var  UnitOfWorkInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $unitOfWork;

	/** @var  RepositoryInterface */
	protected $repo;

	public function setUp()
	{
		$this->entityFinder = $this->createMock(EntityFinderInterface::class);

		$this->entityFinder
			->expects($this->any())
			->method('with')
			->willReturnSelf();

		$this->collectionFinder = $this->createMock(CollectionFinderInterface::class);

		$this->collectionFinder
			->expects($this->any())
			->method('with')
			->willReturnSelf();

		$this->dataMapper = $this->createMock(DataMapperInterface::class);

		$this->dataMapper
			->expects($this->any())
			->method('findOne')
			->willReturn($this->entityFinder);

		$this->dataMapper
			->expects($this->any())
			->method('findAll')
			->willReturn($this->collectionFinder);

		$this->unitOfWork = $this->createMock(UnitOfWorkInterface::class);

		$this->repo = new Repository(Article::class, $this->dataMapper, $this->unitOfWork);
	}

	/**
	 * @testdox getById() uses findOne() on the data mapper
	 */
	public function testGetById()
	{
		$this->dataMapper
			->expects($this->once())
			->method('findOne');

		$article = $this->repo->getById(1);
	}

	/**
	 * @testdox findOne() is delegated to the data mapper
	 */
	public function testFindOne()
	{
		$this->dataMapper
			->expects($this->once())
			->method('findOne');

		$article = $this->repo->findOne()->getItem();
	}

	/**
	 * @testdox findOne() respects given restrictions
	 */
	public function testFindOneRestricted()
	{
		$this->dataMapper
			->expects($this->once())
			->method('findOne');

		$this->entityFinder
			->expects($this->once())
			->method('with')
			->with('key', Operator::EQUAL, 'value');

		$this->repo->restrictTo('key', Operator::EQUAL, 'value');

		$article = $this->repo->findOne()->getItem();
	}

	/**
	 * @testdox findAll() is delegated to the data mapper
	 */
	public function testFindAll()
	{
		$this->dataMapper
			->expects($this->once())
			->method('findAll');

		$article = $this->repo->findAll()->getItems();
	}

	/**
	 * @testdox findAll() respects given restrictions
	 */
	public function testFindAllRestricted()
	{
		$this->dataMapper
			->expects($this->once())
			->method('findAll');

		$this->collectionFinder
			->expects($this->once())
			->method('with')
			->with('key', Operator::EQUAL, 'value');

		$this->repo->restrictTo('key', Operator::EQUAL, 'value');

		$article = $this->repo->findAll()->getItems();
	}

	/**
	 * @testdox add() schedules the object for insertion on the unit of work
	 */
	public function testAdd()
	{
		$this->unitOfWork
			->expects($this->once())
			->method('scheduleForInsertion');

		$this->repo->add(new Article);
	}

	/**
	 * @testdox remove() schedules the object for deletion on the unit of work
	 */
	public function testRemove()
	{
		$this->unitOfWork
			->expects($this->once())
			->method('scheduleForDeletion');

		$this->repo->remove(new Article);
	}

	/**
	 * @testdox commit() is a proxy for commit() on the unit of work
	 */
	public function testCommit()
	{
		$this->unitOfWork
			->expects($this->once())
			->method('commit');

		$this->repo->commit();
	}
}
