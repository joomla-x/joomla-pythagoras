<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM\Entity;

use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Definition\Parser\Field;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Entity\EntityRegistry;
use Joomla\ORM\IdAccessorRegistry;
use Joomla\ORM\Repository\RepositoryInterface;
use Joomla\ORM\Service\RepositoryFactory;
use Joomla\ORM\UnitOfWork\TransactionInterface;
use Joomla\Tests\Unit\ORM\Mocks\Article;
use Joomla\Tests\Unit\ORM\Mocks\Detail;
use Joomla\Tests\Unit\ORM\Mocks\Master;
use PHPUnit\Framework\TestCase;

class EntityBuilderTest extends TestCase
{
	/** @var  EntityBuilder */
	protected $builder;

	/** @var  IdAccessorRegistry|\PHPUnit_Framework_MockObject_MockObject */
	#protected $idAccessorRegistry;

	/** @var  TransactionInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $transactor;

	/** @var  EntityRegistry */
	#protected $entityRegistry;

	/** @var  array */
	protected $config = [
		'dataPath'                            => "tests/unit/ORM/data",
		'definitionPath'                      => "tests/unit/ORM/Mocks",
		'databaseUrl'                         => null,
		'Joomla\Tests\Unit\ORM\Mocks\Article' => [
			'dataMapper' => "Joomla\\ORM\\Storage\\Csv\\CsvDataMapper",
			'definition' => "Article.xml",
			'data'       => "articles.csv"
		],
		'Joomla\Tests\Unit\ORM\Mocks\Master'  => [
			'dataMapper' => "Joomla\\ORM\\Storage\\Csv\\CsvDataMapper",
			'definition' => "Master.xml",
			'data'       => "masters.csv"
		],
		'Joomla\Tests\Unit\ORM\Mocks\Extra'   => [
			'dataMapper' => "Joomla\\ORM\\Storage\\Csv\\CsvDataMapper",
			'definition' => "Extra.xml",
			'data'       => "extras.csv"
		]
	];

	/** @var array */
	private $articles = [
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
		$this->transactor = $this->createMock(TransactionInterface::class);

		$repositoryFactory = new RepositoryFactory($this->config, $this->transactor);
		$strategy          = new RecursiveDirectoryStrategy($this->config['definitionPath']);
		$locator           = new Locator([$strategy]);
		$this->builder     = new EntityBuilder($locator, $this->config, $repositoryFactory);
	}

	/**
	 * @testdox The meta data contains the name
	 */
	public function testGetMetaForArticleName()
	{
		$meta = $this->builder->getMeta(Article::class);

		$this->assertEquals('Article', $meta->name);
		$this->assertEmpty($meta->extends);
	}

	/**
	 * @testdox The meta data contains field descriptions
	 */
	public function testGetMetaForArticleFields()
	{
		$meta = $this->builder->getMeta(Article::class);

		$this->assertTrue(is_array($meta->fields));
		$this->assertArrayHasKey('id', $meta->fields);
		$this->assertArrayHasKey('title', $meta->fields);
		$this->assertArrayHasKey('teaser', $meta->fields);
		$this->assertArrayHasKey('body', $meta->fields);
		$this->assertArrayHasKey('author', $meta->fields);
		$this->assertArrayHasKey('license', $meta->fields);
		$this->assertArrayNotHasKey('parentId', $meta->fields, 'parent_id should be identified as foreign key, not as a standard field.');
	}

	/**
	 * @testdox The meta data field description is a Field with information needed for use in forms
	 */
	public function testGetMetaForArticleFieldId()
	{
		$meta = $this->builder->getMeta(Article::class);

		$field = $meta->fields['id'];

		$this->assertInstanceOf(Field::class, $field);
		$this->assertObjectHasAttribute('name', $field);
		$this->assertObjectHasAttribute('type', $field);
		$this->assertObjectHasAttribute('validation', $field);
		$this->assertObjectHasAttribute('options', $field);
		$this->assertObjectHasAttribute('value', $field);
		$this->assertObjectHasAttribute('label', $field);
		$this->assertObjectHasAttribute('description', $field);
		$this->assertObjectHasAttribute('default', $field);
		$this->assertObjectHasAttribute('hint', $field);
	}

	/**
	 * @testdox Existing language strings are utilised, prefix for missing strings is generated from *_LABEL
	 */
	public function testGetMetaForArticleFieldIdLanguage()
	{
		$meta = $this->builder->getMeta(Article::class);

		$field = $meta->fields['id'];

		$this->assertEquals('JGLOBAL_FIELD_ID_LABEL', $field->label);
		$this->assertEquals('JGLOBAL_FIELD_ID_DESC', $field->description);
		$this->assertEquals('JGLOBAL_FIELD_ID_HINT', $field->hint);
	}

	/**
	 * @testdox Missing language strings are generated as COM_<entity>_FIELD_<field>_*
	 */
	public function testGetMetaForArticleFieldTeaser()
	{
		$meta = $this->builder->getMeta(Article::class);

		$field = $meta->fields['teaser'];

		$this->assertEquals('COM_ARTICLE_FIELD_TEASER_LABEL', $field->label);
		$this->assertEquals('COM_ARTICLE_FIELD_TEASER_DESC', $field->description);
		$this->assertEquals('COM_ARTICLE_FIELD_TEASER_HINT', $field->hint);
	}

	/**
	 * @testdox The meta data contains relation information
	 */
	public function testGetMetaForArticleRelations()
	{
		$meta = $this->builder->getMeta(Article::class);

		$this->assertTrue(is_array($meta->relations));
		$this->assertArrayHasKey('belongsTo', $meta->relations);
		$this->assertArrayHasKey('parent_id', $meta->relations['belongsTo']);
		$this->assertArrayHasKey('hasMany', $meta->relations);
		$this->assertArrayHasKey('children', $meta->relations['hasMany']);
	}

	/**
	 * @testdox The meta data contains storage details
	 */
	public function testGetMetaForArticleStorage()
	{
		$meta = $this->builder->getMeta(Article::class);

		$this->assertTrue(is_array($meta->storage));
		$this->assertEquals('default', $meta->storage['type']);
		$this->assertEquals('articles', $meta->storage['table']);
	}

	/**
	 * @testdox castToEntity() creates objects from arrays, only using the appropriate data
	 */
	public function testCastToEntity()
	{
		$row        = $this->articles[1];
		$row['foo'] = 'bar';

		/** @var Article[] $articles */
		$articles = $this->builder->castToEntity([$row], Article::class);
		$article  = $articles[0];

		$this->assertInstanceOf(Article::class, $article);
		$this->assertEquals(1, $article->id);
		$this->assertEquals('One', $article->title);
		$this->assertEquals('Teaser 1', $article->teaser);
		$this->assertEquals('Body 1', $article->body);
		$this->assertEquals('Author 1', $article->author);
		$this->assertEquals('CC', $article->license);

		$this->assertObjectNotHasAttribute('foo', $article);
	}

	/**
	 * @testdox castToEntity() resolves relations automatically
	 */
	public function testCastToEntityRelations()
	{
		/** @var Article[] $articles */
		$articles = $this->builder->castToEntity($this->articles, Article::class);
		$article  = $articles[0];

		$this->assertObjectHasAttribute('parent', $article);
		/** @noinspection PhpUndefinedFieldInspection */
		$this->assertNull($article->parent);

		$this->assertObjectHasAttribute('children', $article);
		/** @noinspection PhpUndefinedFieldInspection */
		$this->assertInstanceOf(RepositoryInterface::class, $article->children);
		/** @noinspection PhpUndefinedFieldInspection */
		$this->assertEquals(Article::class, $article->children->getEntityClass());
	}

	/**
	 * @testdox getRepository() returns a repository for any defined entity
	 */
	public function testGetRepository()
	{
		$repository = $this->builder->getRepository(Master::class);

		$this->assertInstanceOf(RepositoryInterface::class, $repository);
		$this->assertEquals(Master::class, $repository->getEntityClass());
	}

	/**
	 * @testdox getRepository() throws an EntityNotDefinedException for undefined entities
	 * @expectedException \Joomla\ORM\Exception\EntityNotDefinedException
	 */
	public function testGetRepositoryFail()
	{
		$this->builder->getRepository(Detail::class);
	}

	/**
	 * resolve() adds the relations to an entity
	 */
	public function testResolve()
	{
		$article = new Article;
		foreach ($this->articles[1] as $key => $value)
		{
			/** @noinspection PhpVariableVariableInspection */
			$article->$key = $value;
		}

		$this->builder->resolve($article);

		$this->assertObjectHasAttribute('parent', $article);
		/** @noinspection PhpUndefinedFieldInspection */
		$this->assertNull($article->parent);

		$this->assertObjectHasAttribute('children', $article);
		/** @noinspection PhpUndefinedFieldInspection */
		$this->assertInstanceOf(RepositoryInterface::class, $article->children);
		/** @noinspection PhpUndefinedFieldInspection */
		$this->assertEquals(Article::class, $article->children->getEntityClass());

		return $article;
	}

	/**
	 * @param $article
	 *
	 * @depends testResolve
	 * @testdox reduce() extracts the database structure from an entity
	 */
	public function testReduce($article)
	{
		$row = $this->builder->reduce($article);

		$this->assertEquals($this->articles[1], $row);
	}
}
