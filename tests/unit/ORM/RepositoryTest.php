<?php

use Joomla\ORM\DataMapper\CsvDataMapper;
use Joomla\ORM\Repository\Repository;
use Joomla\Tests\Unit\ORM\TestData\Article;

require_once __DIR__ . '/data/Article.php';

class RepositoryTest extends \PHPUnit\Framework\TestCase
{
	private $articleData = [
		[
			[
				'id'        => 1,
				'title'     => "First Article",
				'teaser'    => "Look at the first example",
				'body'      => "This first example is cool",
				'author'    => "John Doe",
				'license'   => "CC",
				'parent_id' => 0
			],
			[
				'id'        => 2,
				'title'     => "Second Article",
				'teaser'    => "Look at another example",
				'body'      => "This provides child elements",
				'author'    => "Doctor",
				'license'   => "CC",
				'parent_id' => 0
			],
			[
				'id'        => 3,
				'title'     => "Part One",
				'teaser'    => "No. 1 of 2. article's children",
				'body'      => "This is the first child element",
				'author'    => "Rose Tyler",
				'license'   => "CC",
				'parent_id' => 2
			],
			[
				'id'        => 4,
				'title'     => "Part Two",
				'teaser'    => "No. 2 of 2. article's children",
				'body'      => "This is the second child element",
				'author'    => "Doctor",
				'license'   => "CC",
				'parent_id' => 2
			]
		]
	];

	public function provideArticleData()
	{
		return $this->articleData;
	}

	/**
	 * @dataProvider provideArticleData
	 */
	public function testGetOne($articleData)
	{
		$dataMapper = new CsvDataMapper(Article::class, __DIR__ . '/data/Article.xml', __DIR__ . '/data/articles.csv');
		$repo       = new Repository(Article::class, $dataMapper);

		$post = $repo->getById($articleData['id']);

		$this->assertInstanceOf(Article::class, $post);
		$this->assertEquals($articleData['title'], $post->title);
	}

	public function testGetAll()
	{
		$dataMapper = new CsvDataMapper(Article::class, __DIR__ . '/data/Article.xml', __DIR__ . '/data/articles.csv');
		$repo       = new Repository(Article::class, $dataMapper);

		$posts = $repo->findAll()->getItems();

		$this->assertEquals(4, count($posts));
		foreach ($posts as $post)
		{
			$this->assertInstanceOf(Article::class, $post);
		}
	}
}
