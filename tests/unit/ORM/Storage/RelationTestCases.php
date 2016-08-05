<?php
namespace Joomla\Tests\Unit\ORM\Storage;

use Doctrine\DBAL\DriverManager;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Entity\EntityRegistry;
use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\IdAccessorRegistry;
use Joomla\ORM\Operator;
use Joomla\ORM\Repository\Repository;
use Joomla\ORM\Repository\RepositoryInterface;
use Joomla\ORM\Service\RepositoryFactory;
use Joomla\ORM\UnitOfWork\TransactionInterface;
use Joomla\ORM\UnitOfWork\UnitOfWorkInterface;
use Joomla\Tests\Unit\DumpTrait;
use Joomla\Tests\Unit\ORM\Mocks\Detail;
use Joomla\Tests\Unit\ORM\Mocks\Extra;
use Joomla\Tests\Unit\ORM\Mocks\Master;
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

		$repositoryFactory        = new RepositoryFactory($this->config, $this->transactor);
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
		$repo   = $this->repo[Detail::class];
		$detail = $repo->getById(3);
	}

	private function restoreData($tables = [])
	{
		$dataDir  = realpath(__DIR__ . '/../data');
		$database = $dataDir . '/sqlite.test.db';

		$connection = DriverManager::getConnection(['url' => 'sqlite:///' . $database]);

		$files = glob($dataDir . '/original/*.csv');

		foreach ($files as $file)
		{
			$tableName = basename($file, '.csv');

			if (!empty($tables) && !in_array($tableName, $tables))
			{
				continue;
			}

			$csvFilename = $dataDir . '/' . $tableName . '.csv';
			unlink($csvFilename);
			copy($file, $csvFilename);

			$records = $this->loadData($file);

			$connection->beginTransaction();

			$connection->query('DELETE FROM ' . $tableName);
			foreach ($records as $record)
			{
				$connection->insert($tableName, $record);
			}
			$connection->commit();
		}
	}

	/**
	 * Load the data from the file
	 *
	 * @return  array
	 */
	private function loadData($dataFile)
	{
		static $data = [];

		if (!isset($data[$dataFile]))
		{
			$data[$dataFile] = [];

			$fh   = fopen($dataFile, 'r');
			$keys = fgetcsv($fh);

			while (!feof($fh))
			{
				$row = fgetcsv($fh);

				if ($row === false)
				{
					break;
				}

				$data[$dataFile][] = array_combine($keys, $row);
			}

			fclose($fh);
		}

		return $data[$dataFile];
	}
}
