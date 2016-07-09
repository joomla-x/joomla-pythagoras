<?php
namespace Joomla\Tests\Unit\ORM\Storage;

use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\IdAccessorRegistry;
use Joomla\ORM\Repository\RepositoryInterface;
use Joomla\Tests\Unit\ORM\Mocks\Extra;
use PHPUnit\Framework\TestCase;

class RelationTestCases extends TestCase
{
	/** @var  array */
	protected $config;

	/** @var  RepositoryInterface[] */
	protected $repo;

	/** @var EntityBuilder The entity builder */
	protected $builder;

	/** @var  IdAccessorRegistry */
	protected $idAccessorRegistry;

	public function setUp()
	{
		$this->idAccessorRegistry = new IdAccessorRegistry;
		$strategy                 = new RecursiveDirectoryStrategy(realpath(__DIR__ . '/../Mocks'));
		$locator                  = new Locator([$strategy]);
		$this->builder            = new EntityBuilder($locator, $this->config, $this->idAccessorRegistry);
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
		$repo   = $this->repo['Detail'];
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
	private function testCreateAnExtraForADetail()
	{
		$repo   = $this->repo['Detail'];
		$detail = $repo->getById(2);

		$detail->extra = new Extra('New info for Detail 2');

		$repo->commit();

		// Reload
		$detail = $repo->getById(2);

		$this->assertEquals('New info for Detail 2', $detail->extra->info);
	}

	/**
	 * @param \Exception $e
	 *
	 * @return string
	 */
	protected function dump($e)
	{
		$msg           = '';
		$fmt           = "%s in %s(%d)\n";
		$traceAsString = '';

		while ($e instanceof \Exception)
		{
			$message       = $e->getMessage();
			$file          = $e->getFile();
			$line          = $e->getLine();
			$traceAsString = $e->getTraceAsString();
			$e             = $e->getPrevious();

			$msg .= sprintf($fmt, $message, $file, $line);
		}

		return $msg . "\n" . $traceAsString;
	}
}
