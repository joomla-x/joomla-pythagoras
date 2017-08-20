<?php
/**
 * Part of the Joomla! Installer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\Installer;

use Doctrine\DBAL\Schema\Table;
use Psr\Container\ContainerInterface;
use Joomla\DI\Container;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Definition\Parser\BelongsTo;
use Joomla\ORM\Definition\Parser\Entity as EntityStructure;
use Joomla\ORM\Definition\Parser\HasMany;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Service\RepositoryFactory;
use Joomla\String\Inflector;
use Joomla\String\Normalise;

/**
 * Class Installer
 *
 * @package  Joomla\Cms\Installer
 *
 * @since    __DEPLOY_VERSION__
 */
class Installer
{
    /** @var  string[] */
    private $extensions;

    /** @var EntityBuilder The entity builder */
    private $builder;

    /** @var Container The container */
    private $container;

    /** @var  string The data directory */
    private $dataDirectory;

    /** @var  RepositoryFactory The repository factory */
    private $repositoryFactory;

    /** @var EntityStructure[] The entity structures */
    private $entityDefinitions = [];

    /** @var Inflector The inflector */
    private $inflector;

    /** @var string[] */
    private $dataDirectories = [];

    /**
     * Installer constructor.
     *
     * @param   string             $dataDirectory The data directory
     * @param   ContainerInterface $container     The container
     */
    public function __construct($dataDirectory, ContainerInterface $container)
    {
        $this->container         = $container;
        $this->dataDirectory     = $dataDirectory;
        $this->repositoryFactory = $this->container->get('Repository');
        $this->builder           = $this->repositoryFactory->getEntityBuilder();
        $this->inflector         = Inflector::getInstance();

        $this->loadInstalledExtensions();
        $this->loadExistingEntities();
    }

    /**
     * @return  void
     */
    private function loadInstalledExtensions()
    {
        $config           = $this->getExtensionIniFilename();
        $this->extensions = file_exists($config) ? parse_ini_file($config, true) : [];
    }

    /**
     * @return string
     */
    private function getExtensionIniFilename()
    {
        return $this->container->get('ConfigDirectory') . '/config/extensions.ini';
    }

    /**
     * @return  void
     */
    private function loadExistingEntities()
    {
        $this->importDefinition($this->dataDirectory . '/entities/*.xml');
    }

    /**
     * @param   string $pattern A filename pattern
     *
     * @return  string[]  A list of entity names
     */
    private function importDefinition($pattern)
    {
        $entityNames = [];

        foreach ($this->findFiles($pattern) as $file) {
            $basename   = basename($file, '.xml');
            $definition = $this->builder->getMeta($basename);

            if (!empty($this->entityDefinitions[$basename])) {
                if ($this->entityDefinitions[$basename]->class == $definition->class) {
                    // Might be an update
                    continue;
                }

                throw new \RuntimeException("Another definition for $basename already exists!");
            }

            $this->entityDefinitions[$basename] = $definition;
            $entityNames[]                      = $basename;
        }

        return $entityNames;
    }

    /**
     * @param   string $pattern A filename pattern
     *
     * @return  string[]  A list of file names
     */
    private function findFiles($pattern)
    {
        return glob($pattern);
    }

    /**
     * Installs an extension
     *
     * @param   string $source The path to the extension
     *
     * @return  string[]
     */
    public function install($source)
    {
        $pattern                             = chr(1) . '^' . preg_quote(JPATH_ROOT) . '/' . chr(1);
        $this->extensions[basename($source)] = preg_replace($pattern, '', $source);

        $xmlDirectory = $source . '/entities';
        $strategy     = new RecursiveDirectoryStrategy($xmlDirectory);
        $this->builder->addLocatorStrategy($strategy);
        $entityNames = $this->importDefinition($xmlDirectory . '/*.xml');

        foreach ($entityNames as $entityName) {
            $this->dataDirectories[$entityName] = $source . '/data';
        }

        return $entityNames;
    }

    /**
     * Finishes the installation
     *
     * @return  void
     */
    public function finish()
    {
        $this->resolveRelations();
        $this->writeXmlFiles();
        $this->createTables();
        $this->import();
        $this->writeExtensionIni();
    }

    /**
     * Resolve all counter-relations
     *
     * @return  void
     */
    private function resolveRelations()
    {
        foreach ($this->entityDefinitions as $definition) {
            $this->resolveBelongsTo($definition);
            $this->resolveHasOneOrMany($definition);
            $this->resolveHasManyThrough($definition);
        }
    }

    /**
     * @param   EntityStructure $definition The entity structure
     *
     * @return  void
     */
    private function resolveBelongsTo($definition)
    {
        foreach ($definition->relations['belongsTo'] as $relation) {
            $counterMeta      = $this->entityDefinitions[$relation->entity];

            if ($counterMeta->role == 'lookup') {
                continue;
            }

            $counterRelations = $counterMeta->relations;

            foreach (array_merge($counterRelations['hasOne'], $counterRelations['hasMany']) as $counterRelation) {
                if ($counterRelation->entity == $definition->name && $counterRelation->reference == $relation->reference) {
                    break 2;
                }
            }

            // No existing counter-relation found; create it.
            $counterRelation = new HasMany(
                [
                    'name'      => $this->normalise($this->inflector->toPlural($definition->name)),
                    'entity'    => $definition->name,
                    'reference' => $relation->reference,
                ]
            );

            $this->entityDefinitions[$relation->entity]->relations['hasMany'][$counterRelation->name] = $counterRelation;
        }
    }

    /**
     * @param   string $word A word
     *
     * @return  string
     */
    private function normalise($word)
    {
        return Normalise::toUnderscoreSeparated(strtolower(Normalise::fromCamelCase($word)));
    }

    /**
     * @param   EntityStructure $definition The entity structure
     *
     * @return  void
     */
    private function resolveHasOneOrMany($definition)
    {
        foreach (array_merge($definition->relations['hasMany'], $definition->relations['hasOne']) as $relation) {
            $counterEntity    = $relation->entity;
            $counterMeta      = $this->entityDefinitions[$counterEntity];
            $counterRelations = $counterMeta->relations;

            foreach ($counterRelations['belongsTo'] as $counterRelation) {
                if ($counterRelation->entity == $definition->name && $counterRelation->reference == $relation->reference) {
                    break 2;
                }
            }

            // No existing counter-relation found; create it.
            $counterRelation = new BelongsTo(
                [
                    'name'   => $this->normalise($definition->name),
                    'entity' => $definition->name,
                    'reference' => $relation->reference,
                ]
            );

            $this->entityDefinitions[$counterEntity]->relations['belongsTo'][$counterRelation->name] = $counterRelation;
        }
    }

    /**
     * @param   EntityStructure $definition The entity structure
     *
     * @return  void
     */
    private function resolveHasManyThrough($definition)
    {
        foreach ($definition->relations['hasManyThrough'] as $relation) {
            // @todo Implement HasManyThrough handling
        }
    }

    /**
     * Store XML files in a central place
     *
     * @return  void
     */
    private function writeXmlFiles()
    {
        foreach ($this->entityDefinitions as $definition) {
            $definition->writeXml($this->dataDirectory . "/entities/{$definition->name}.xml");
        }
    }

    /**
     * @return  void
     */
    private function createTables()
    {
        foreach ($this->dataDirectories as $entityName => $csvDirectory) {
            $this->createTable($entityName);
        }
    }

    /**
     * @param   string $entityName The name of the entity
     *
     * @return  void
     */
    private function createTable($entityName)
    {
        $prefix        = '';
        $schemaManager = $this->repositoryFactory->getSchemaManager();

        if ($schemaManager !== null) {
            $meta      = $this->entityDefinitions[$entityName];
            $tableName = $prefix . $meta->storage['table'];
            $table     = new Table($tableName);

            foreach ($meta->fields as $field) {
                // @todo add proper type handling
                $type = 'string';

                if (in_array($field->type, ['int', 'integer', 'id'])) {
                    $type = 'integer';
                }

                $table->addColumn($field->columnName($field->name), $type, ['notNull' => false]);
            }

            $primary = explode(',', $meta->primary);
            $table->setPrimaryKey($primary);

            $schemaManager->dropAndCreateTable($table);
        }
    }

    /**
     * @return  void
     */
    private function import()
    {
        foreach ($this->dataDirectories as $entityName => $csvDirectory) {
            $this->importInitialData($entityName, $csvDirectory);
        }

        $this->repositoryFactory->getUnitOfWork()->commit();
    }

    /**
     * Import data, if present
     *
     * @param   string $entityName   The name of the entity
     * @param   string $csvDirectory The directory containing the sample data
     *
     * @return  void
     */
    private function importInitialData($entityName, $csvDirectory)
    {
        $tableName = $this->entityDefinitions[$entityName]->storage['table'];
        $dataFile  = $csvDirectory . '/' . $tableName . '.csv';

        if (!file_exists($dataFile)) {
            return;
        }

        $entityClass = $this->entityDefinitions[$entityName]->class;
        $repo        = $this->repositoryFactory->forEntity($entityClass);

        foreach ($this->loadData($dataFile) as $row) {
            $entity = $repo->createFromArray($row);
            $repo->add($entity);
        }
    }

    /**
     * Load the data from the file
     *
     * @param   string $dataFile A filename
     *
     * @return  array   The data
     */
    private function loadData($dataFile)
    {
        $fh          = fopen($dataFile, 'r');
        $keys        = fgetcsv($fh);
        $columnCount = count($keys);

        $rows = [];

        while (!feof($fh)) {
            $row = fgetcsv($fh);

            if ($row === false) {
                break;
            }

            if (count($row) != $columnCount) {
                echo "\n$dataFile: problem with row " . implode(', ', $row) . "\n";
            }
            $rows[] = array_combine($keys, $row);
        }

        fclose($fh);

        return $rows;
    }

    /**
     * @return  void
     */
    private function writeExtensionIni()
    {
        $ini = "; This file is auto-generated during the installation process. Don't change it manually.\n\n";

        foreach ($this->extensions as $extension => $path) {
            $ini .= sprintf("%s=\"%s\"\n", $extension, $path);
        }

        file_put_contents($this->getExtensionIniFilename(), $ini);
    }
}
