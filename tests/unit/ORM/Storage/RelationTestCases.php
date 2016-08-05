<?php
namespace Joomla\Tests\Unit\ORM\Storage;

use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Entity\EntityRegistry;
use Joomla\ORM\IdAccessorRegistry;
use Joomla\ORM\Operator;
use Joomla\ORM\Repository\RepositoryInterface;
use Joomla\ORM\Service\RepositoryFactory;
use Joomla\ORM\UnitOfWork\TransactionInterface;
use Joomla\ORM\UnitOfWork\UnitOfWorkInterface;
use Joomla\Tests\Unit\DumpTrait;
use Joomla\Tests\Unit\ORM\Mocks\Detail;
use Joomla\Tests\Unit\ORM\Mocks\Extra;
use PHPUnit\Framework\TestCase;

abstract class RelationTestCases extends TestCase
{
	/** @var  array */
	protected $config;

	/** @var  RepositoryInterface[] */
	protected $repo;

	/** @var EntityBuilder The entity builder */
	protected $builder;

	/** @var  IdAccessorRegistry */
	protected $idAccessorRegistry;

	/** @var  TransactionInterface */
	protected $transactor;

	/** @var  UnitOfWorkInterface */
	protected $unitOfWork;

	/** @var  EntityRegistry */
	protected $entityRegistry;

	abstract protected function onBeforeSetup();

	abstract protected function onAfterSetUp();

	use DumpTrait;

	public function setUp()
	{
		$this->onBeforeSetup();

		$this->idAccessorRegistry = new IdAccessorRegistry;

		$strategy             = new RecursiveDirectoryStrategy($this->config['definitionPath']);
		$locator              = new Locator([$strategy]);
		$repositoryFactory    = new RepositoryFactory($this->config, $this->transactor);
		$this->builder        = new EntityBuilder($locator, $this->config, $repositoryFactory);
		$this->entityRegistry = $repositoryFactory->getEntityRegistry();
		$this->unitOfWork     = $repositoryFactory->getUnitOfWork();

		$this->onAfterSetUp();
	}

	public function testRelatedEntitiesAreRegistered()
	{
		$repo = $this->repo[Detail::class];
		/** @noinspection PhpUnusedLocalVariableInspection */
		$detail = $repo->getById(3);

		$this->assertTrue($this->entityRegistry->isRegistered($detail), "Detail is not registered");
		$this->assertTrue($this->entityRegistry->isRegistered($detail->master), "Master is not registered");
		$this->assertTrue($this->entityRegistry->isRegistered($detail->extra), "Extra is not registered");
	}

	/**
	 * Read the Extra of a Detail
	 *
	 * The detail record is read from the database, and a Detail object is created and populated with the data.
	 * The virtual extra property is populated with an Extra object (if existent).
	 *
	 * @testdox hasOne: Read the Extra of a Detail
	 */
	public function testReadTheExtraOfADetail()
	{
		$repo   = $this->repo[Detail::class];
		$detail = $repo->getById(1);

		$this->assertInstanceOf(Extra::class, $detail->extra);
		$this->assertEquals('Extra info for Detail 1', $detail->extra->info);
	}

	/**
	 * Create an Extra for a Detail
	 *
	 * Since the detail was fetched using the Repository, the object is known to the ORM.
	 * Its changes are tracked internally, and written to disk automatically.
	 *
	 * @testdox hasOne: Create an Extra for a Detail
	 */
	public function testCreateAnExtraForADetail()
	{
		$repo   = $this->repo[Detail::class];
		$detail = $repo->getById(2);

		$this->assertFalse(isset($detail->extra), 'Detail record #2 should not have an initial Extra record.');

		$newValue      = 'New info for Detail 2';
		$detail->extra = new Extra($newValue);

		$this->unitOfWork->commit();

		$detail = $repo->getById(2);
		$this->assertInstanceOf(Extra::class, $detail->extra, "The new Extra did not make it into the storage");
		$this->assertEquals($newValue, $detail->extra->info);
	}

	/**
	 * Update the extra of a detail
	 *
	 * The system will detect the change and save just the extra.
	 *
	 * @testdox Update the Extra of a Detail
	 */
	public function testUpdateTheExtraOfADetail()
	{
		$repo   = $this->repo[Detail::class];
		$detail = $repo->getById(1);

		$this->assertNotEmpty($detail->extra);
		$detail->extra->info = 'Changed information';

		$this->unitOfWork->commit();

		$detail = $repo->getById(1);
		$this->assertInstanceOf(Extra::class, $detail->extra);
		$this->assertEquals('Changed information', $detail->extra->info);
	}

	/**
	 * Delete the extra of a detail
	 *
	 * The system will detect the change and delete the extra.
	 *
	 * @testdox Delete the Extra of a Detail by assigning null
	 * @expectedException \Joomla\ORM\Exception\EntityNotFoundException
	 */
	public function testDeleteNull()
	{
		$detailRepo = $this->repo[Detail::class];
		$detail     = $detailRepo->getById(1);

		$this->assertNotEmpty($detail->extra);
		$this->assertEquals(1, $detail->extra->detailId);

		$detail->extra = null;

		$this->unitOfWork->commit();

		$extraRepo = $this->repo[Extra::class];
		$extraRepo->findOne()->with('detail_id', Operator::EQUAL, 1)->getItem();
	}

	/**
	 * Delete the extra of a detail
	 *
	 * The system will detect the change and delete the extra.
	 *
	 * @testdox Delete the Extra of a Detail with unset()
	 * @expectedException \Joomla\ORM\Exception\EntityNotFoundException
	 */
	public function testDeleteUnset()
	{
		$this->restoreExtra(1, 'Extra info for Detail 1');

		$detailRepo = $this->repo[Detail::class];
		$detail     = $detailRepo->getById(1);

		$this->assertNotEmpty($detail->extra);
		$this->assertEquals(1, $detail->extra->detailId);

		unset($detail->extra);

		$this->unitOfWork->commit();

		$extraRepo = $this->repo[Extra::class];
		$extraRepo->findOne()->with('detail_id', Operator::EQUAL, 1)->getItem();
	}

	/**
	 * Delete the Extra together with the Detail
	 *
	 * When a detail is deleted, the associated extra (if existent) will be deleted as well.
	 *
	 * @testdox Delete the Extra together with the Detail
	 * @expectedException \Joomla\ORM\Exception\EntityNotFoundException
	 */
	public function testDeleteCascade()
	{
		$this->restoreExtra(1, 'Extra info for Detail 1');

		$detailRepo = $this->repo[Detail::class];
		$detail     = $detailRepo->getById(1);

		$this->assertNotEmpty($detail->extra);
		$this->assertEquals(1, $detail->extra->detailId);

		$detailRepo->remove($detail);

		$this->unitOfWork->commit();

		$extraRepo = $this->repo[Extra::class];
		$extraRepo->findOne()->with('detail_id', Operator::EQUAL, 1)->getItem();

		$this->restoreExtra(1, 'Extra info for Detail 1');
	}

	private function restoreExtra($detailId, $info)
	{
		$this->builder->getMeta(Extra::class);
		$this->unitOfWork->dispose();

		$extra = new Extra($info);
		$extra->detailId = $detailId;
		$this->repo[Extra::class]->add($extra);

		$this->unitOfWork->commit();
	}
}
