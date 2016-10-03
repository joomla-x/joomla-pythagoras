<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM\Event;

use \Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Joomla\Extension\Article\Entity\Article;
use Joomla\Extension\Workflow\Listener\QueryDatabaseListener;
use Joomla\ORM\Event\QueryDatabaseEvent;
use PHPUnit\Framework\TestCase;

class OrmEventTest extends TestCase
{
	/** @var  Connection */
	private $connection;

	public function setUp()
	{
		$config = parse_ini_file('tests/unit/ORM/data/entities.doctrine.ini', true);

		$this->connection = DriverManager::getConnection(['url' => $config['databaseUrl']]);
	}

	public function dataStatesQuery()
	{
		return [
			'All states' => [
				'states' => [],
				'query'  => 'SELECT a.* FROM articles a'
			],
			'One state' => [
				'states' => [1],
				'query'  => 'SELECT a.* FROM articles a INNER JOIN states_entities b ON a.id=b.entity_id WHERE b.state_id=?'
			],
			'Multiple states' => [
				'states' => [1,4],
				'query'  => 'SELECT a.* FROM articles a INNER JOIN states_entities b ON a.id=b.entity_id WHERE b.state_id IN (?,?)'
			],
		];
	}

	/**
	 * @dataProvider dataStatesQuery
	 */
	public function testListenerModifiesQueryAccordingly($states, $expected)
	{
		$query = $this->connection->createQueryBuilder();
		$query
			->select('a.*')
			->from('articles', 'a');

		$this->assertEquals('SELECT a.* FROM articles a', $query->getSQL());

		$event    = new QueryDatabaseEvent(Article::class, $query);
		$listener = new QueryDatabaseListener;

		$listener->allowStates($states);
		$listener->addStateConstraint($event);

		$this->assertEquals($expected, $query->getSQL());
	}
}
