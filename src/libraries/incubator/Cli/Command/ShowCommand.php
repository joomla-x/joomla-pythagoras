<?php
/**
 * Part of the Joomla Command Line Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cli\Command;

use Joomla\Cli\EntityAwareCommand;
use Joomla\ORM\Definition\Parser\Relation;
use Joomla\ORM\Storage\CollectionFinderInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The show command shows a list of entities.
 *
 * @package     Joomla\Cli
 * @since       __DEPLOY_VERSION__
 */
class ShowCommand extends EntityAwareCommand
{
    /**
     * Configure the options for the version command
     *
     * @return  void
     */
    protected function configure()
    {
        $this
            ->setName('show')
            ->setDescription('Show a list of entities')
            ->addOption(
                'label',
                'l',
                InputOption::VALUE_NONE,
                'Use labels as column headers instead of column names.'
            )
            ->addOption(
                'compact',
                null,
                InputOption::VALUE_NONE,
                'Output a compact table.'
            );
    }

    /**
     * @param   InputInterface            $input  An InputInterface instance
     * @param   OutputInterface           $output An OutputInterface instance
     * @param   CollectionFinderInterface $finder The finder
     * @param   string                    $entity The entity name
     *
     * @return  void
     */
    protected function doIt(InputInterface $input, OutputInterface $output, $finder, $entity)
    {
        $entityBuilder = $this->repositoryFactory->getEntityBuilder();
        $meta          = $entityBuilder->getMeta($entity);
        $fields        = array_merge($meta->fields, $meta->relations['belongsTo']);
        $headers       = [];
        $useLabel      = $input->getOption('label');

        foreach ($fields as $field) {
            if (!$useLabel) {
                $headers[] = $field->name;

                continue;
            }

            $headers[] = isset($field->label) ? $field->label : $field->entity;
        }

        $table = $this->createTable($input, $output, $headers);

        foreach ($this->getRecords($finder) as $record) {
            $table->addRow($entityBuilder->reduce($record));
        }

        $table->render();
    }
}
