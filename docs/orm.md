# Reworking of the ORM

This PR provides a slightly different implementation of the ORM. The original version had the drawback, that it was not easily possible to inject different data sources (especially needed for testing).

Another important difference is, that entities can be plain objects. No interface needs to be implemented.

## RepositoryFactory

The programmatic entry-point to the ORM is the `RepositoryFactory`. It manages the creation of all necessary ORM internal dependancies. The factory is usually provided by the `StorageServiceProvider` with the key '`Repository`'.

The `RepositoryFactory` exposes one public method, `forEntity()`.

```php
/** @var \Interop\Container\ContainerInterface $container */
$repository = $container->get('Repository')->forEntity($entityClassOrAlias);
```

You can inject a DataMapper, if you want to override the configuration settings.

```php
/** @var \Interop\Container\ContainerInterface $container */
$repository = $container->get('Repository')->forEntity($entityClassOrAlias, $dataMapper);
```

## Repository

A `Repository` is created by the `RepositoryFactory`. It should not be instantiated directly outside the ORM, because that could do harm to the referential integrity.

```php
/** @var \Interop\Container\ContainerInterface $container */
$repository = $container->get('Repository')->forEntity(Article::class);
```

The interface has changed a bit:

```php
interface RepositoryInterface
{
	public function getById($id);
	public function findOne();
	public function findAll();
	public function add($entity);
	public function remove($entity);
	public function commit();
	public function getEntityClass()
	public function restrictTo($lValue, $op, $rValue);
}
```

`commit()` is a proxy to the `UnitOfWork`.
Since the repository is used as a collection in relations, the `restrictTo()` method is used preset conditions, so access is restricted to related entities.

## DataMapper

The `DataMapperInterface` is similar to the `RepositoryInterface`:

```php
interface DataMapperInterface
{
	public function getById($id);
	public function findOne();
	public function findAll();
	public function insert($entity);
	public function update($entity);
	public function delete($entity);
}
```

Current implementations for this interface are `CsvDataMapper` and `DoctrineDataMapper`.

### CsvDataMapper

The `CsvDataMapper`'s constructor takes four arguments:

  - the CsvDataGateway,
  - the class of the entity,
  - the basename of the data file, and
  - the global entity registy.

```php
$dataMapper = new CsvDataMapper(
    $gateway,
    Article::class,
    'articles',
    $entityRegistry
);
```

### DoctrineDataMapper

The `DoctrineDataMapper`'s constructor takes four arguments:

  - the database connection,
  - the class of the entity,
  - the name of the table, and
  - the global entity registy.

```php
$dataMapper = new DoctrineDataMapper(
    $connection,
    Article::class,
    'articles',
    $entityRegistry
);
```

## EntityBuilder, EntityRegistry

The entities and their relations are managed by a couple of ORM internal classes. Userland code should never have to deal with them. 

### Configuration

The setup information for the ORM is currently stored in an `entities.ini` file.

## Testing

The tests have been re-organised, so they can be re-used for any storage type.
They are located in

  - `tests/unit/ORM/StorageTestCases.php`,
    which is extended by `CsvStorageTest` and `DoctrineStorageTest`,
  - `tests/unit/ORM/RelationTestCases.php`,
    extended by `CsvRelationTest` and `DoctrineRelationTest`, and
  - `tests/unit/ORM/DataMapperTestCases.php`,
    extended by `CsvDataMapperTest` and `DoctrineDataMapperTest`.

This way, all tests are run for all data mappers, ensuring identical behaviour.

### Test Data

The test data is provided in `tests/unit/ORM/data/original`. To create an accessible copy for the tests for both CSV and SQLite, just run

```bash
$ ./libraries/vendor/bin/robo create:testdata
```

When running tests using `robo`, the test data is (re)created automatically.

```bash
$ ./libraries/vendor/bin/robo test
```
