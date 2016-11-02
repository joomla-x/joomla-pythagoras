# Chris Davenport Happiness Milestone

Based on the [requirements](j4cdhms.md), this milestone shows some of the basic concepts of Joomla! X.

## Article (Vertical Component)

The **Article** component consists of just three (!) files:

  - `extensions/Article/data/articles.csv` - the sample data or initial database content
  - `extensions/Article/entities/Article.xml` - the entity definition
  - `extensions/Article/Entity/Article.php` - the entity class

It is located in the `extensions` directory, but could as well live in `libraries` or even in `libraries/vendor`.

### The Entity Class

The entity class is used as a _typed data container_. It does not contain any logic (validation may be provided with magic setters, though).

```php
<?php
/**
 * Part of the Joomla Article Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension\Article\Entity;

/**
 * Class Article
 *
 * @package  Joomla\Extension\Article
 *
 * @since    __DEPLOY_VERSION__
 */
class Article
{
	/** @var  integer  The ID */
	public $id;

	/** @var  string  The title */
	public $title;

	/** @var  string  A category */
	public $category;

	/** @var  string  The (relative) URL */
	public $alias;

	/** @var  string  The teaser text */
	public $teaser;

	/** @var  string  The article's copy text */
	public $body;

	/** @var  string  The author's name */
	public $author;

	/** @var  string  The license of the article */
	public $license;
}
```

### The Entity Definition

The structure of an entity and all of its relations are defined in the entity definition file.

```xml
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE entity SYSTEM
    "https://github.com/nibralab/joomla-architecture/blob/master/code/Joomla/ORM/Definition/entity.dtd">
<entity name="Joomla\Extension\Article\Entity\Article">

    <storage>
        <default table="articles"/>
    </storage>

    <fields>
        <field name="id"
               type="id"
               label="JGLOBAL_FIELD_ID_LABEL"
               description="JGLOBAL_FIELD_ID_DESC"
               default="null"
        >
            <validation rule="positive"/>
            <validation rule="integer"/>
        </field>

        <field name="title"
               type="title"
               label="JGLOBAL_TITLE"
        >
            <validation rule="maxlen" value="64"/>
        </field>

        <field name="category"
               type="text"
        />

        <field name="alias"
               type="text"
               label="JGLOBAL_FIELD_ALIAS_LABEL"
               description="JGLOBAL_FIELD_ALIAS_DESC"
        />

        <field name="teaser"
               type="richtext"
               label="COM_CONTENT_FIELD_ARTICLETEXT_LABEL"
               description="COM_CONTENT_FIELD_ARTICLETEXT_DESC"
        />

        <field name="body"
               type="richtext"
               label="COM_CONTENT_FIELD_ARTICLETEXT_LABEL"
               description="COM_CONTENT_FIELD_ARTICLETEXT_DESC"
        />

        <field name="author"
               type="text"
               label="COM_CONTENT_FIELD_CREATED_BY_ALIAS_LABEL"
               description="COM_CONTENT_FIELD_CREATED_BY_ALIAS_DESC"
               default=""
        />

        <field name="license"
               type="text"
               label="JFIELD_META_RIGHTS_LABEL"
               description="JFIELD_META_RIGHTS_DESC"
        >
            <validation rule="regex" value="copy(right|left)"/>
        </field>

    </fields>

    <relations>
        <belongsTo name="parent_id"
                   entity="Article"
                   label="JFIELD_PARENT_LABEL"
                   description="JFIELD_PARENT_DESC"
        />
        <hasMany name="children"
                 entity="Article"
                 reference="parent_id"
        />
    </relations>

</entity>
```

The DTD is temporarily located in the `nibralab/joomla-architecture` repository, until it gets a more stable state and can be moved to `joomla/schemes`.

The `name` parameter of the `entity` tag defines the fully qualified classname of the entity.

The definition contains three sections, `storage`, `fields`, and `relations`.

#### Storage

Normally, a component will use the installation's database (`default`), so providing the table name is sufficient at this point. Other possible tags would be `api` and `special`, allowing to use any kind of data sources and sinks.

#### Fields

The field definition is very similar to what is used with JForms in the current versions. This is intended, so this part does not need to be changed, when porting an extension to Joomla! X.

#### Relations

Each entity can be related to itself or other entities in many ways.
There are four types of relations: `belongsTo`, `hasOne`, `hasMany`, and `hasManyThrough`.
In this section, information is provided on how to link those entities together.

## Universal Content Types

A couple of predefined content types have been defined. The list still needs to be extended.

  - Accordion
  - Article
  - Attribution
  - Columns
  - Compound
  - DefaultMenu
  - Dump
  - Headline
  - Image
  - Paragraph
  - Rows
  - Slider
  - Tabs
  - Teaser
  - Tree

Their HTML counterparts can be found in `layouts/bootstrap-3`, which, as the name says, are based on Bootstrap 3. Any JS/CSS framework can be supported by providing the suitable layouts. Work on Bootstrap 4 and UIKit has already started.

Custom content types can be defined, as done with the TemplateSelector of the PageBuilder component. A custom type consists of two parts: the content type class, and the suitable handler, a callback for the renderer.

```php
/**
 * Part of the Joomla Framework PageBuilder Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\PageBuilder\ContentType;

use Joomla\Content\ContentTypeInterface;
use Joomla\Content\ContentTypeVisitorInterface;
use Joomla\Content\Type\AbstractContentType;

/**
 * Compound ContentType
 *
 * @package  Joomla/Content
 * @since    __DEPLOY_VERSION__
 *
 * @property string                 $type
 * @property ContentTypeInterface[] $elements
 */
class TemplateSelector extends AbstractContentType
{
	/**
	 * Compound constructor.
	 *
	 * @param   string $type The type represented by this class.
	 */
	public function __construct($type)
	{
		$this->type = $type;
	}

	/**
	 * Visits the content type.
	 *
	 * @param   ContentTypeVisitorInterface $visitor The Visitor
	 *
	 * @return  mixed
	 */
	public function accept(ContentTypeVisitorInterface $visitor)
	{
		return $visitor->visitTemplateSelector($this);
	}
}
```

The handler is registered with the renderer.

```php
	private function registerContentTypes()
	{
		$container = $this->container;
		$output    = $this->output;

		$this->output->registerContentType('TemplateSelector', function (TemplateSelector $selector) use ($container, $output)
		{
			/** @var RepositoryInterface $repo */
			$repo      = $container->get('Repository')->forEntity(Template::class);
			$templates = $repo->getAll();

			// @todo Grouping
			$groups    = [
				'Available Templates' => $templates,
			];
			$accordion = new Accordion('Accordion Title');

			foreach ($groups as $title => $group)
			{
				$compound = new Compound('div');

				foreach ($group as $item)
				{
					$imageData      = new ImageEntity();
					$imageData->url = '/' . $item->path . '/preview.png';
					$image          = new Image($imageData, $item->path);
					$compound->add($image, null, "javascript:alert('Select template');");
				}
				$accordion->add($compound, $title);
			}

			$accordion->accept($output);
		});
	}
```

As you can see, the new content type is composed from standard content types; it's an _Accordion_ containing *Compound*s, which again contain _Image_ links.

## Renderer

HTML rendering is closely related to the (yet to build) PageBuilder. The data structures already exists, and sample data is provided in the PageBuilder's `data` directory.

To see the renderer in action, you need to install the demo. Check out the `master` branch, and enter

```bash
$ composer install
$ docker-compose up -d
$ ./install.sh
```

on the console. This will install a couple of components, and register their entities. This may take a while on first call (especially the `docker-compose` command).

```
Joomla! installation expected in your/path/to/joomla-pythagoras
 - installing extensions/Article
   - Article
 - installing libraries/incubator/Media
   - Image
 - installing libraries/incubator/PageBuilder
   - Content
   - Layout
   - Page
   - Template
 - finishing
Installed 3 extension(s)
```

Together with the PageBuilder, some sample pages were installed.

```bash
./joomla show pages
```
```
+----+----------------+-------------+-----------+-----------+
| id | title          | url         | parent_id | layout_id |
+----+----------------+-------------+-----------+-----------+
| 1  | PageBuilder    | pagebuilder | 2         | 2         |
| 2  | Administration | admin       | 0         | 2         |
| 3  | Home           |             | 0         | 3         |
| 4  | About          | about       | 3         | 3         |
| 5  | FAQ            | faq         | 3         | 3         |
| 6  | Blog           | blog        | 3         | 3         |
| 7  | :title         | :alias      | 6         | 3         |
+----+----------------+-------------+-----------+-----------+
```

The 'Blog' page uses the layout with `id` 3.
 
```bash
./joomla show layouts --filter="id=3"
```
```
+----+------------------+-----------+-------------+
| id | title            | parent_id | template_id |
+----+------------------+-----------+-------------+
| 3  | Corporate Design | 1         | 2           |
+----+------------------+-----------+-------------+
```

This layout, 'Corporate Design', uses the template with `id` 2.

```bash
./joomla show templates --filter="id=2"
```
```
+----+----------------------+-------------+
| id | path                 | scripting   |
+----+----------------------+-------------+
| 2  | templates/bootstrap3 | bootstrap-3 |
+----+----------------------+-------------+
```

So, the template is located in `templates/bootstrap3`, and uses Bootstrap 3 as its JS/CSS framework.

Content can be assigned to pages and layouts (for re-use).

The content of the layout with `id` 3 is

```bash
./joomla show contents --filter="layout_id=3"
```
```
+----+-------------+---------------------------------+-------------------+----------+-----------+-----------+-----------+----------------------------------------------+-----------+-----------+---------+
| id | name        | content_type                    | content_args      | ordering | component | reference | selection | params                                       | parent_id | layout_id | page_id |
+----+-------------+---------------------------------+-------------------+----------+-----------+-----------+-----------+----------------------------------------------+-----------+-----------+---------+
| 23 | body        | Joomla\Content\Type\Rows        | {"type":"div"}    | 1        |           |           | null      | null                                         | 0         | 3         | 0       |
| 24 | header      | Joomla\Content\Type\Columns     | {"type":"header"} | 1        |           |           | null      | null                                         | 23        | 3         | 0       |
| 25 | logo        | Joomla\Content\Type\Compound    | {"type":"div"}    | 1        |           |           | null      | null                                         | 24        | 3         | 0       |
| 27 | main        | Joomla\Content\Type\Columns     | {"type":"div"}    | 2        |           |           | null      | null                                         | 23        | 3         | 0       |
| 28 | footer      | Joomla\Content\Type\Columns     | {"type":"footer"} | 3        |           |           | null      | null                                         | 23        | 3         | 0       |
| 32 | logo-image  | Joomla\Content\Type\Image       | null              | 1        | Image     |           | {"id":5}  | {"class":"pull-left"}                        | 25        | 3         | 0       |
| 33 | menu        | Joomla\Content\Type\DefaultMenu | null              | 1        | Page      |           | {"id":3}  | {"levels":2,"class":"navbar navbar-inverse"} | 27        | 3         | 0       |
| 34 | content     | Joomla\Content\Type\Compound    | {"type":"main"}   | 2        |           |           | null      | null                                         | 27        | 3         | 0       |
| 35 | logo-jd16de | Joomla\Content\Type\Image       | null              | 2        | Image     |           | {"id":6}  | {"class":"pull-right"}                       | 25        | 3         | 0       |
+----+-------------+---------------------------------+-------------------+----------+-----------+-----------+-----------+----------------------------------------------+-----------+-----------+---------+
```

and the content of the page is

```bash
./joomla show contents --filter="page_id=6"
```
```
+----+--------------+----------------------------+--------------+----------+-----------+-----------+---------------------+--------+-----------+-----------+---------+
| id | name         | content_type               | content_args | ordering | component | reference | selection           | params | parent_id | layout_id | page_id |
+----+--------------+----------------------------+--------------+----------+-----------+-----------+---------------------+--------+-----------+-----------+---------+
| 22 | article-blog | Joomla\Content\Type\Teaser | null         | 1        | Article   |           | {"category":"blog"} | null   | 34        | 0         | 6       |
+----+--------------+----------------------------+--------------+----------+-----------+-----------+---------------------+--------+-----------+-----------+---------+
```

These data define the following content tree:

```
Rows (body)
+- Columns (header)
|  +- Compound (logo)
|     +- Image (logo-image)
|     +- Image (logo-jd16de)
+- Columns (main)
|  +- DefaultMenu (menu)
|  +- Compound (content)
|     +- Teaser (article-blog)
|     +- Teaser (article-blog)
|     +- ...
+- Columns (footer)
```

The renderer processes the tree and integrates the result into the template.
To see the result, navigate your browser to `localhost:8080/index.php/blog`.

## Storage

The default storage layer is provided by Doctrine DBAL.
It is accessed through an abstraction layer, the ORM, which handles relations transparently.

As an example on how to implement an alternative to Doctrine DBAL, a CSV based storage layer was implemented.
It is mostly used in unit tests, as it makes it easy to provide test data.

For detailed information about the ORM, please refer to the [description of the associated pull request](orm.md).

## Workflow (Horizontal Component)

The workflow was implemented as an example of a horizontal component. 
The article component is enriched with the horizontal functionality automatically.

The workflow component adds two entities (`State` and `StateEntity`), a content type (`WorkflowState`), and an event listener (`QueryDatabaseListener`) to the system.

For detailed information about the workflow component and how it changes the behaviour of the system, please refer to the [workflow demo instructions](workflow.md).

## Channel independancy border

One of the main goals is to be able to access data from different channels. We've seen both Web and CLI already in the Renderer section.

Each channel needs a separate entry script, as request and response handling as well as routing is quite different for those channels. They all have the same command bus, though (see section CommandBus below).

### Browser Interface

The web interface is provided by `index.php`. It initialises the dependancy injection container, combines the appropriate HTTP middleware and runs the application.

### Command Line Interface

The CLI is provided by `joomla`, a shell script written in PHP. It initialises the dependancy injection container and runs the application, which is based on `Symfony\Component\Console`. A plugin mechanism is used to add sub commands to `joomla` from `libraries/incubator/Cli/Command`.

You can get a list of available commands with

```bash
./joomla list
```

and more detailed information about a sub command `command` with

```bash
./joomla help command
```

It is intended to extract public methods from (business) models in order to create suitable commands on the fly.

### Application Programming Interface

The API is provided by `api.php`. It initialises the dependancy injection container, combines the appropriate HTTP middleware and runs the application.

Examples:

```
GET /api.php/articles
```

```
GET /api.php/articles/2
```

## CommandBus

The internal backbone is the CommandBus, which is based on Tactician. It is provided by Chris Davenport's service layer.

For detailed information about the service layer, please refer to the [documentation on Chris' repository](https://github.com/chrisdavenport/service/blob/master/README.md).
