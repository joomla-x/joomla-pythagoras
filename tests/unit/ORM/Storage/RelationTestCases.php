<?php
namespace Joomla\Tests\Unit\ORM\Storage;

use Doctrine\DBAL\Connection;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Entity\EntityRegistry;
use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\IdAccessorRegistry;
use Joomla\ORM\Operator;
use Joomla\ORM\Repository\Repository;
use Joomla\ORM\Repository\RepositoryInterface;
use Joomla\ORM\Service\RepositoryFactory;
use Joomla\ORM\Storage\Csv\CsvDataGateway;
use Joomla\ORM\UnitOfWork\TransactionInterface;
use Joomla\ORM\UnitOfWork\UnitOfWorkInterface;
use Joomla\Tests\Unit\DataTrait;
use Joomla\Tests\Unit\DumpTrait;
use Joomla\Tests\Unit\ORM\Mocks\Detail;
use Joomla\Tests\Unit\ORM\Mocks\Extra;
use Joomla\Tests\Unit\ORM\Mocks\Master;
use Joomla\Tests\Unit\ORM\Mocks\Tag;
use PHPUnit\Framework\TestCase;

abstract class RelationTestCases extends TestCase
{
	/** @var  array */
	protected $config;

	/** @var  CsvDataGateway|Connection */
	protected $connection;

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
	use DataTrait;

	public function setUp()
	{
		$this->onBeforeSetup();

		$repositoryFactory        = new RepositoryFactory($this->config, $this->connection, $this->transactor);
		$this->entityRegistry     = $repositoryFactory->getEntityRegistry();
		$this->builder            = $repositoryFactory->getEntityBuilder();
		$this->unitOfWork         = $repositoryFactory->getUnitOfWork();
		$this->idAccessorRegistry = $repositoryFactory->getIdAccessorRegistry();

		$this->onAfterSetUp();
	}

	public function testRelatedEntitiesAreRegistered()
	{
		$this->restoreData(['details', 'extras']);

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
		$this->restoreData(['details', 'extras']);

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
		$this->restoreData(['details', 'extras']);

		$repo   = $this->repo[Detail::class];
		$detail = $repo->getById(2);

		$this->assertFalse(isset($detail->extra), 'Detail record #2 should not have an initial Extra record.');

		$newValue      = 'New info for Detail 2';
		$detail->extra = new Extra($newValue);

		$this->unitOfWork->commit();
		$this->entityRegistry->clear();

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
		$this->restoreData(['details', 'extras']);

		$repo   = $this->repo[Detail::class];
		$detail = $repo->getById(1);

		$this->assertNotEmpty($detail->extra);
		$detail->extra->info = 'Changed information';

		$this->unitOfWork->commit();
		$this->entityRegistry->clear();

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
		$this->restoreData(['details', 'extras']);

		$detailRepo = $this->repo[Detail::class];
		$detail     = $detailRepo->getById(1);

		$this->assertNotEmpty($detail->extra);
		$this->assertEquals(1, $detail->extra->detailId);

		$detail->extra = null;

		$this->unitOfWork->commit();
		$this->entityRegistry->clear();

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
		$this->restoreData(['details', 'extras']);

		$detailRepo = $this->repo[Detail::class];
		$detail     = $detailRepo->getById(1);

		$this->assertNotEmpty($detail->extra);
		$this->assertEquals(1, $detail->extra->detailId);

		unset($detail->extra);

		$this->unitOfWork->commit();
		$this->entityRegistry->clear();

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
		$this->restoreData(['details', 'extras']);

		$detailRepo = $this->repo[Detail::class];
		$detail     = $detailRepo->getById(1);

		$this->assertNotEmpty($detail->extra);
		$this->assertEquals(1, $detail->extra->detailId);

		$detailRepo->remove($detail);

		$this->unitOfWork->commit();
		$this->entityRegistry->clear();

		$extraRepo = $this->repo[Extra::class];
		$extraRepo->findOne()->with('detail_id', Operator::EQUAL, 1)->getItem();
	}

	/**
	 * Read the details of a master
	 *
	 * The master record is read from the database, and a Master object is created and populated with the data.
	 * The virtual details property is populated with a Repository for Detail objects, instead of the related details
	 * themselves.
	 * The repository gives access to the related objects and allows all kind of filtering.
	 */
	public function testReadTheDetailsOfAMaster()
	{
		$this->restoreData(['masters', 'details']);

		$repo   = $this->repo[Master::class];
		$master = $repo->getById(1);

		$this->assertInstanceOf(Repository::class, $master->details);
		$this->assertEquals(Detail::class, $master->details->getEntityClass());

		$details = $master->details->findAll()->getItems();

		$this->assertEquals(2, count($details));
	}

	/**
	 * Create a detail for a master
	 *
	 * The system will store the detail automatically.
	 */
	public function testCreateADetailForAMaster()
	{
		$this->restoreData(['masters', 'details']);

		$repo    = $this->repo[Master::class];
		$master  = $repo->getById(1);
		$details = $master->details->findAll()->getItems();

		$this->assertEquals(2, count($details));

		$master->details->add(new Detail);

		$this->unitOfWork->commit();
		$this->entityRegistry->clear();

		$master  = $repo->getById(1);
		$details = $master->details->findAll()->getItems();

		$this->assertEquals(3, count($details));
	}

	/**
	 * Update a detail of a master
	 *
	 * The system will detect the change and save just the detail2.
	 */
	public function testUpdateADetailOfAMaster()
	{
		$this->restoreData(['masters', 'details']);

		$repo   = $this->repo[Master::class];
		$master = $repo->getById(1);

		$detail         = $master->details->findOne()->with('id', Operator::EQUAL, 3)->getItem();
		$detail->field1 = 'Changed content';

		$this->unitOfWork->commit();
		$this->entityRegistry->clear();

		$repo   = $this->repo[Detail::class];
		$detail = $repo->getById(3);
		$this->assertEquals('Changed content', $detail->field1);
	}

	/**
	 * Delete a detail of a master
	 *
	 * The system will detect the change and delete the detail.
	 */
	public function testDeleteADetailOfAMaster()
	{
		$this->restoreData(['masters', 'details']);

		$repo   = $this->repo[Master::class];
		$master = $repo->getById(1);

		$detail = $master->details->findOne()->with('id', Operator::EQUAL, 3)->getItem();
		$master->details->remove($detail);

		$this->unitOfWork->commit();
		$this->entityRegistry->clear();

		$this->expectException(EntityNotFoundException::class);
		$repo = $this->repo[Detail::class];
		/** @noinspection PhpUnusedLocalVariableInspection */
		$detail = $repo->getById(3);
	}

	/**
	 * Read the master of a detail
	 *
	 * The detail record is read from the database, and a Detail object is created and populated with the data.
	 * The virtual master property is populated, and will contain a Master object.
	 */
	public function testReadTheMasterOfADetail()
	{
		$this->restoreData(['masters', 'details']);

		$repo   = $this->repo[Detail::class];
		$detail = $repo->getById(2);

		$this->assertInstanceOf(Master::class, $detail->master);
		$this->assertEquals('Title 2', $detail->master->title);
	}

	/**
	 * Create the master of a detail
	 *
	 * The system will detect the change, create the master and update the foreign key in the detail.
	 * The original master will not be affected.
	 */
	public function testCreateTheMasterOfADetail()
	{
		$this->restoreData(['masters', 'details']);

		$repo           = $this->repo[Detail::class];
		$detail         = $repo->getById(2);
		$master         = new Master();
		$master->title  = 'New Master for Detail 2';
		$detail->master = $master;

		$this->unitOfWork->commit();
		$this->entityRegistry->clear();

		$detail = $repo->getById(2);
		$this->assertEquals('New Master for Detail 2', $detail->master->title);

		$oldMaster = $this->repo[Master::class]->getById(2);
		$this->assertEquals('Title 2', $oldMaster->title);
	}

	/**
	 * Update the master of a detail
	 *
	 * The system will detect the change and save the master.
	 */
	public function testUpdateTheMasterOfADetail()
	{
		$this->restoreData(['masters', 'details']);

		$repo                   = $this->repo[Detail::class];
		$detail                 = $repo->getById(2);
		$detail->master->fieldA = 'Changed data';

		$this->unitOfWork->commit();
		$this->entityRegistry->clear();

		$master = $this->repo[Master::class]->getById(2);
		$this->assertEquals('Changed data', $master->fieldA);
	}

	/**
	 * Delete the master of a detail
	 *
	 * If master_id is not required, it will be set to null on the detail. Otherwise, an exception is thrown.
	 * The associated master will not be affected.
	 */
	public function testDeleteTheMasterOfADetail()
	{
		$this->restoreData(['masters', 'details']);

		$repo           = $this->repo[Detail::class];
		$detail         = $repo->getById(2);
		$detail->master = null;

		$this->unitOfWork->commit();
		$this->entityRegistry->clear();

		$detail = $repo->getById(2);
		$this->assertNull($detail->masterId);
		$this->assertEmpty($detail->master);

		// Expect no exception, because old master still exists
		/** @noinspection PhpUnusedLocalVariableInspection */
		$master = $this->repo[Master::class]->getById(2);
	}

	/**
	 * Unset the master of a detail
	 *
	 * If master_id is not required, it will be set to null on the detail. Otherwise, an exception is thrown.
	 * The associated master will not be affected.
	 */
	public function testUnsetTheMasterOfADetail()
	{
		$this->restoreData(['masters', 'details']);

		$repo   = $this->repo[Detail::class];
		$detail = $repo->getById(2);
		unset($detail->master);

		$this->unitOfWork->commit();
		$this->entityRegistry->clear();

		$detail = $repo->getById(2);
		$this->assertNull($detail->masterId);
		$this->assertEmpty($detail->master);

		// Expect no exception, because old master still exists
		/** @noinspection PhpUnusedLocalVariableInspection */
		$master = $this->repo[Master::class]->getById(2);
	}

	/**
	 * Read the tags of a master
	 *
	 * The master record is read from the database, and a Master object is created and populated with the data.
	 * The virtual tags property is populated with a Repository for Tag objects, instead of the related tags themselves.
	 * The repository gives access to the objects and allows all kind of filtering.
	 */
	public function testReadTheTagsOfAMaster()
	{
		$this->restoreData(['masters', 'maps', 'tags']);

		$repo   = $this->repo[Master::class];
		$master = $repo->getById(1);

		$this->assertInstanceOf(RepositoryInterface::class, $master->tags);
		$this->assertEquals(Tag::class, $master->tags->getEntityClass());

		$tags = $master->tags->findAll()->getItems();

		$this->assertEquals(3, count($tags));
	}

	/**
	 * Add an existing tag for a master
	 */
	public function testAddAnExistingTagForAMaster()
	{
		$this->restoreData(['masters', 'maps', 'tags']);

		$repo   = $this->repo[Master::class];
		$master = $repo->getById(4);
		$tags   = $master->tags->findAll()->columns('tag')->getItems();

		$this->assertEquals(['Tag 1', 'Tag 2'], $tags);

		$tag = $this->repo[Tag::class]->getById(3);
		$master->tags->add($tag);

		$this->unitOfWork->commit();
		$this->entityRegistry->clear();

		$master = $repo->getById(1);
		$tags   = $master->tags->findAll()->columns('tag')->getItems();

		$this->assertEquals(['Tag 1', 'Tag 2', 'Tag 3'], $tags);
	}

	/**
	 * Create a tag for a master
	 *
	 * The system will store the tag automatically.
	 */
	public function testCreateATagForAMaster()
	{
		$this->restoreData(['masters', 'maps', 'tags']);

		$repo   = $this->repo[Master::class];
		$master = $repo->getById(4);
		$tags = $master->tags->findAll()->columns('tag')->getItems();

		$this->assertEquals(['Tag 1', 'Tag 2'], $tags);

		$tag = new Tag('Tag 4', 'Newly defined tag');
		$master->tags->add($tag);

		$this->unitOfWork->commit();
		$this->entityRegistry->clear();

		$master = $repo->getById(4);
		$tags   = $master->tags->findAll()->columns('tag')->getItems();

		$this->assertEquals(['Tag 1', 'Tag 2', 'Tag 4'], $tags);
	}

	/**
	 * Update a tag for a master
	 *
	 * The system will detect the change2 and save just the tag.
	 * After this action, all masters associated with the tag 'Old Label' will show 'Changed Label'.
	 */
	public function testUpdateATagForAMaster()
	{
		$this->restoreData(['masters', 'maps', 'tags']);

		$repo   = $this->repo[Master::class];
		$master = $repo->getById(1);
		$tag    = $master->tags->getById(3);

		$this->assertEquals('Tag 3', $tag->tag);

		$tag->tag = 'Changed Label';

		$this->unitOfWork->commit();
		$this->entityRegistry->clear();

		$master = $repo->getById(1);
		$tags   = $master->tags->findAll()->columns('tag')->getItems();

		$this->assertEquals(['Tag 1', 'Tag 2', 'Changed Label'], $tags);
	}

	/**
	 * Delete a tag for a master
	 *
	 * The system will detect the change and delete the entry in the map.
	 * The tag itself will not be affected.
	 */
	public function testDeleteATagForAMaster()
	{
		$this->restoreData(['masters', 'maps', 'tags']);

		$repo   = $this->repo[Master::class];
		$master = $repo->getById(1);
		$tag = $master->tags->getById(1);

		$master->tags->remove($tag);

		$this->unitOfWork->commit();
		$this->entityRegistry->clear();

		$master = $repo->getById(1);
		$tags   = $master->tags->findAll()->columns('tag')->getItems();

		$this->assertEquals(['Tag 2', 'Tag 3'], $tags);

		$tagRepo = $this->repo[Tag::class];
		$tag     = $tagRepo->getById(1);

		$this->assertEquals('Tag 1', $tag->tag);
	}
}
