<?php
/**
 * Part of the Joomla CMS Build Environment
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Table;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use Symfony\Component\Yaml\Yaml;
use SebastianBergmann\CodeCoverage\Report\Clover as XmlReport;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as HtmlReport;

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 * @codingStandardsIgnoreStart
 */
class RoboFile extends \Robo\Tasks
{
	private $config = [
		'title'    => "Joomla X (Pythagoras)",
		'reports'  => 'build/reports',
		'apidocs'  => 'build/docs',
		'userdocs' => 'docs',
		'toolcfg'  => 'build/config'
	];

	private $ignoredDirs = [
		'build',
		'cache',
		'docs',
		'logs',
		'tests',
		'tmp',
		'libraries/vendor',
	];
	private $ignoredFiles = [
		'RoboFile.php',
	];

	private $vendorDir;
	private $binDir;

	use \Robo\Task\Testing\loadTasks;

	/**
	 * RoboFile constructor.
	 */
	public function __construct()
	{
		$config          = json_decode(file_get_contents(__DIR__ . '/composer.json'), true);
		$this->vendorDir = isset($config['config']['vendor-dir']) ? __DIR__ . '/' . $config['config']['vendor-dir'] : $this->vendorDir = __DIR__ . '/vendor';
		$this->binDir    = $this->vendorDir . '/bin';
	}

	/**
	 * Measures the size and analyses the structure of the project.
	 */
	public function checkLoc()
	{
		$this->initReports();
		$phploc = $this->taskExec($this->binDir . '/phploc')
		               ->arg('--names-exclude=' . implode(',', $this->ignoredFiles))
		               ->arg('--log-xml=' . $this->config['reports'] . '/phploc.xml');

		foreach ($this->ignoredDirs as $dir)
		{
			$phploc->arg('--exclude=' . $dir);
		}

		$phploc->arg('.')->run();
	}

	/**
	 * Detects duplicate code.
	 */
	public function checkCpd()
	{
		$this->initReports();
		$phploc = $this->taskExec($this->binDir . '/phpcpd')
		               ->arg('--names-exclude=' . implode(',', $this->ignoredFiles))
		               ->arg('--log-pmd=' . $this->config['reports'] . '/pmd-cpd.xml')
		               ->arg('--fuzzy');

		foreach ($this->ignoredDirs as $dir)
		{
			$phploc->arg('--exclude=' . $dir);
		}

		$phploc->arg('.')->run();
	}

	/**
	 * Performs static code analysis and calculates software metrics.
	 */
	public function checkDepend()
	{
		$this->initReports();
		$pdepend = $this->taskExec($this->binDir . '/pdepend')
		                ->arg('--dependency-xml=' . $this->config['reports'] . '/dependency.xml')
		                ->arg('--jdepend-chart=' . $this->config['reports'] . '/jdepend.svg')
		                ->arg('--jdepend-xml=' . $this->config['reports'] . '/jdepend.xml')
		                ->arg('--overview-pyramid=' . $this->config['reports'] . '/pyramid.svg')
		                ->arg('--summary-xml=' . $this->config['reports'] . '/summary.xml')
		                ->arg('--ignore=' . implode(',', $this->ignoredDirs));

		if (file_exists('' . $this->config['reports'] . '/coverage.xml'))
		{
			$pdepend->arg('--coverage-report=' . $this->config['reports'] . '/coverage.xml');
		}

		$pdepend->arg('.')->run();
	}

	/**
	 * Detects violations of the coding standard.
	 */
	public function checkStyle()
	{
		$this->initReports();
		$this->stopOnFail();
		$this->taskStyle($this->binDir . '/phpcs')
		     ->arg('--report=full')
		     ->arg('--report-checkstyle=' . $this->config['reports'] . '/checkstyle.xml')
		     ->run();
	}

	/**
	 * Sets the common parameters for CodeSniffer and CodeBeautifier
	 *
	 * @param   string $bin One of 'phpcs' or 'phpcbf'
	 *
	 * @return \Robo\Task\Base\Exec
	 */
	private function taskStyle($bin)
	{
		return $this->taskExec($bin)
		            ->arg('--standard=' . $this->vendorDir . '/greencape/coding-standards/src/Joomla')
		            ->arg('--ignore=' . implode(',', $this->ignoredDirs))
		            ->arg(__DIR__);
	}

	/**
	 * Generates `api`, `full`, and `style` documentation.
	 */
	public function document()
	{
		$this->documentApi();
		$this->documentFull();
		$this->documentStyle();
	}

	/**
	 * Generates API documentation.
	 */
	public function documentApi()
	{
		$this->initApiDocs();
		$this->taskApiGen($this->binDir . '/apigen')
		     ->arg('generate')
		     ->config($this->config['toolcfg'] . '/apigen.api.yml')
		     ->arg('--title "' . $this->config['title'] . ' API Documentation"')
		     ->arg('--destination "' . $this->config['apidocs'] . '/api"')
		     ->run();
	}

	/**
	 * Generates developer documentation.
	 * The documentation not only contains the API, but also protected members, and members marked as `@internal`.
	 */
	public function documentFull()
	{
		$this->initApiDocs();
		$this->taskApiGen($this->binDir . '/apigen')
		     ->arg('generate')
		     ->config($this->config['toolcfg'] . '/apigen.full.yml')
		     ->arg('--title="' . $this->config['title'] . ' Developer Documentation"')
		     ->arg('--destination="' . $this->config['apidocs'] . '/full"')
		     ->arg('--annotation-groups=package')
		     ->run();
	}

	/**
	 * Generates a coding standard description.
	 */
	public function documentStyle()
	{
		$this->initApiDocs();
		$this->taskStyle($this->binDir . '/phpcs')
		     ->arg('--generator=Markdown')
		     ->arg('> "' . $this->config['apidocs'] . '/coding-standard.md"')
		     ->run();
	}

	/**
	 * Automatically corrects coding standard violations.
	 */
	public function fixStyle()
	{
		$this->taskStyle($this->binDir . '/phpcbf')
		     ->run();
	}

	/**
	 * Creates a code listing with syntax highlighting and colored error-sections found by QA tools.
	 */
	public function reportCb()
	{
		$this->initReports();
		$phpcb = $this->taskExec($this->binDir . '/phpcb')
		              ->arg('--log "' . $this->config['reports'] . '"')
		              ->arg('--source .')
		              ->arg('--extensions ".php"')
		              ->arg('--exclude "*.md"')
		              ->arg('--exclude "*.dtd"')
		              ->arg('--output "' . $this->config['reports'] . '/code"');

		foreach (array_merge($this->ignoredDirs, $this->ignoredFiles) as $dir)
		{
			$phpcb->arg('--ignore ' . $dir);
		}

		$phpcb->run();
	}

	/**
	 * Creates a report with some software metrics.
	 */
	public function reportMetrics()
	{
		$this->initReports();
		$this->taskExec($this->binDir . '/phpmetrics')
		     ->arg('--config="' . $this->config['toolcfg'] . '/phpmetrics.yml"')
		     ->arg('.')
		     ->run();
	}

	/**
	 * Performs the tests from the `unit` suite.
	 *
	 * **Note**: The `unit` suite contains not only unit tests, but all tests,
	 * that can be conducted without external services like database or webserver.
	 *
	 * @param array $option
	 *
	 * @option $coverage Whether or not to generate a code coverage report
	 */
	public function testUnit($option = [
		'coverage' => false
	])
	{
		$this->runTest('unit', $option);
	}

	/**
	 * Performs the tests from the `cli` suite.
	 *
	 * @param array $option
	 *
	 * @option $coverage Whether or not to generate a code coverage report
	 */
	public function testCli($option = [
		'coverage' => false
	])
	{
		$inDocker = file_exists('/usr/local/lib/php/prepend.php');

		if (!$inDocker)
		{
			$containerInfo = json_decode(`docker inspect cli_cli`);
			$build         = false;

			if (empty($containerInfo))
			{
				$this->say('Container not found.');
				$build = true;
			}
			else
			{
				$dateOfContainerDefinition = $this->getMaxDate('build/docker/cli');
				$dateOfContainerCreation   = strtotime(preg_replace('~\.\d+Z$~', 'Z', $containerInfo[0]->Created));

				if ($dateOfContainerCreation < $dateOfContainerDefinition)
				{
					$this->say('Container definition has changed.');
					$build = true;
				}
			}

			if ($build)
			{
				$this->say('Building container.');
				`docker-compose -f tests/cli/docker-compose.yml build`;
			}

			$this->say(`docker-compose -f tests/cli/docker-compose.yml up`);

			$this->remap('/var/test', __DIR__, 'build/reports/coverage.cli.php');

			return;
		}

		$this->runTest('cli', $option);
	}

	public function test($option = [
		'coverage' => false
	])
	{
		$this->testUnit($option);
		$this->testCli($option);

		if ($option['coverage'])
		{
			$coverage = $this->getCoverage('unit');
			$this->mergeCoverage($coverage, $this->getCoverage('cli'));

			$this->say("Writing XML report ...");
			$writer = new XmlReport;
			$writer->process($coverage, 'build/reports/junit.xml');

			$this->say("Writing HTML report ...");
			$writer = new HtmlReport;
			$writer->process($coverage, 'build/reports/coverage');
		}
	}

	/**
	 * @param      $suite
	 * @param bool $unlink
	 *
	 * @return CodeCoverage
	 */
	private function getCoverage($suite, $unlink = false)
	{
		$coverage = null;
		$filename = "build/reports/coverage.$suite.php";
		include $filename;

		if ($unlink)
		{
			unlink($filename);
		}

		return $coverage;
	}

	/**
	 * Performs the tests from the selected suite.
	 *
	 * @param string $suite The test suite to conduct
	 * @param array  $option
	 *
	 * @option $coverage Whether or not to generate a code coverage report
	 */
	protected function runTest($suite, $option)
	{
		$this->stopOnFail();
		$this->initReports();
		$this->createTestdata();

		$tempConfigFile = $this->buildConfig($this->config['toolcfg'], $option['coverage']);

		try
		{
			$codecept = $this
				->taskCodecept($this->binDir . '/codecept')
				->configFile($tempConfigFile);

			$codecept->suite($suite);

			if ($option['coverage'])
			{
				$codecept
					->coverage('coverage.' . $suite . '.php');
			}

			$codecept->option('verbose')->run();

			$return = 0;
		}
		catch (\Exception $e)
		{
			$return = 1;
		}
		finally
		{
			$this->_remove($tempConfigFile);
		}

		return $return;
	}

	/**
	 * @param $dir
	 * @param $enableCoverage
	 *
	 * @return string
	 */
	protected function buildConfig($dir, $enableCoverage)
	{
		$file = 'codeception.yml';

		if (!file_exists($dir . '/' . $file))
		{
			$file = 'codeception.yml.dist';
		}

		$configFile = $dir . '/' . $file;
		$config     = Yaml::parse(file_get_contents($configFile));

		$config['coverage']['enabled']   = $enableCoverage;
		$config['coverage']['whitelist'] = [
			'include' => [
				'administrator/*.php',
				'bin/*.php',
				'cli/*.php',
				'components/*.php',
				'installation/*.php',
				'layouts/*.php',
				'libraries/incubator/*.php',
				'modules/*.php',
				'plugins/*.php',
			],
		];
		$config['paths']['log']          = $this->config['reports'];

		$tempConfigFile = 'codeception.yml';
		file_put_contents($tempConfigFile, Yaml::dump($config));

		return $tempConfigFile;
	}

	/**
	 * Performs the tests from the `acceptance` suite.
	 *
	 * **Note**: The `acceptance` suite contains all tests,
	 * that involve a browser.
	 *
	 * @param array $option
	 *
	 * @option $coverage Whether or not to generate a code coverage report
	 */
	public function testSystem($option = [
		'coverage' => false
	])
	{
		$this->runTest('acceptance', $option);
	}

	/**
	 * Disabled due to MD 2.4.3 internal problems
	 */
	protected function checkMd()
	{
		$this->initReports();
		$this->taskExec($this->binDir . '/phpmd')
		     ->arg(__DIR__)
		     ->arg('xml')
		     ->arg($this->config['toolcfg'] . '/phpmd.xml')
		     ->arg('--reportfile=' . $this->config['reports'] . '/pmd.xml')
		     ->arg('--exclude=' . implode(',', $this->ignoredDirs))
		     ->run();
	}

	private function initApiDocs()
	{
		if (!file_exists($this->config['apidocs']))
		{
			$this->_mkdir($this->config['apidocs']);
		}
	}

	private function initReports()
	{
		if (!file_exists($this->config['reports']))
		{
			$this->_mkdir($this->config['reports']);
		}
	}

	/**
	 * Creates the sqlite unit test database
	 */
	public function createSqlData()
	{
		$dataDir  = __DIR__ . '/tests/unit/ORM/data';
		$database = $dataDir . '/original/sqlite.test.db';

		$this->say('Creating test database in ' . $database);

		if (file_exists($database))
		{
			$this->say('Removing old test database');
			$this->_remove($database);
		}

		$connection = DriverManager::getConnection(['url' => 'sqlite:///' . $database]);

		$files = glob($dataDir . '/original/*.csv');

		foreach ($files as $file)
		{
			$tableName = basename($file, '.csv');
			$table     = new Table($tableName);
			$records   = $this->loadData($file);
			$columns   = array_keys(reset($records));

			foreach ($columns as $column)
			{
				$type = preg_match('~\bid$~i', $column) ? 'integer' : 'string';
				$table->addColumn(
					$column,
					$type,
					[
						'Notnull' => false,
					]
				);
			}

			if ($table->hasColumn('id'))
			{
				$table->setPrimaryKey(['id']);
			}

			$this->say('Creating table ' . $tableName);
			$connection->getSchemaManager()->createTable($table);

			$this->say('Adding ' . count($records) . ' records');
			foreach ($records as $record)
			{
				$connection->insert($tableName, $record);
			}
		}
	}

	/**
	 * Creates the unit test database
	 */
	public function createTestdata()
	{
		$dataDir  = __DIR__ . '/tests/unit/ORM/data';
		$database = 'sqlite.test.db';

		$originalDatabase = $dataDir . '/original/' . $database;
		$workingDatabase  = $dataDir . '/' . $database;

		if (!file_exists($originalDatabase))
		{
			$this->createSqlData();
		}

		$this->say('Creating test database in ' . $database);

		if (file_exists($workingDatabase))
		{
			#$this->_remove($workingDatabase);
		}

		$this->_copy($originalDatabase, $workingDatabase);

		$files = glob($dataDir . '/original/*.csv');

		foreach ($files as $file)
		{
			$csvFilename = $dataDir . '/' . basename($file);
			$this->_remove($csvFilename);
			$this->_copy($file, $csvFilename);
		}
	}

	/**
	 * Load the data from the file
	 *
	 * @return  array
	 */
	private function loadData($dataFile)
	{
		$fh   = fopen($dataFile, 'r');
		$keys = fgetcsv($fh);

		$rows = [];

		while (!feof($fh))
		{
			$row = fgetcsv($fh);

			if ($row === false)
			{
				break;
			}

			$rows[] = array_combine($keys, $row);
		}

		fclose($fh);

		return $rows;
	}

	private function getMaxDate($directory)
	{
		$maxDate = 0;

		foreach (glob($directory . '/*') as $file)
		{
			$date = is_dir($file) ? $this->getMaxDate($file) : filemtime($file);

			if ($date > $maxDate)
			{
				$maxDate = $date;
			}
		}

		return $maxDate;
	}

	/**
	 * @param $old
	 * @param $new
	 * @param $file
	 */
	private function remap($old, $new, $file)
	{
		$search  = preg_quote("'$old/", '~');
		$replace = preg_quote("'$new/", '~');
		$cmd     = "sed --in-place s~{$search}~{$replace}~ {$file}";
		`$cmd`;
	}

	/**
	 * @param CodeCoverage $coverage
	 * @param CodeCoverage $that
	 */
	private function mergeCoverage(CodeCoverage $coverage, CodeCoverage $that)
	{
		$filter = $coverage->filter();
		$filter->setWhitelistedFiles(
			array_merge($filter->getWhitelistedFiles(), $that->filter()->getWhitelistedFiles())
		);

		$thisData = $coverage->getData(true);
		$thatData = $that->getData(true);

		foreach ($thatData as $file => $lines)
		{
			if (!$this->hasCoverage($thatData, $file))
			{
				continue;
			}

			if (!$this->hasCoverage($thisData, $file))
			{
				if (!$filter->isFiltered($file))
				{
					$thisData[$file] = $lines;
				}

				continue;
			}

			foreach ($lines as $line => $data)
			{
				if ($data !== null)
				{
					if (!isset($thisData[$file][$line]))
					{
						$thisData[$file][$line] = $data;
					}
					else
					{
						$thisData[$file][$line] = array_unique(
							array_merge($thisData[$file][$line], $data)
						);
					}
				}
			}
		}
		$coverage->setData($thisData);
		$coverage->setTests(array_merge($coverage->getTests(), $that->getTests()));
	}

	/**
	 * @param $coverageData
	 * @param $file
	 *
	 * @return boolean
	 */
	private function hasCoverage($coverageData, $file)
	{
		if (!isset($coverageData[$file]))
		{
			return false;
		}

		$hasData = false;

		foreach ($coverageData[$file] as $lines)
		{
			foreach ($lines as $data)
			{
				if (!is_array($data) || !empty($data))
				{
					$hasData = true;
					break 2;
				}
			}
		}

		return $hasData;
	}
}
