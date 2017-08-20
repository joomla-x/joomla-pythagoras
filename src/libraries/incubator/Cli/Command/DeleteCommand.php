<?php
/**
 * Part of the Joomla Command Line Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cli\Command;

use Joomla\Cli\EntityAwareCommand;
use Joomla\ORM\Storage\CollectionFinderInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * The Delete command deletes entities.
 *
 * @package     Joomla\Cli
 * @since       __DEPLOY_VERSION__
 */
class DeleteCommand extends EntityAwareCommand
{
    /**
     * Configure the options for the version command
     *
     * @return  void
     */
    protected function configure()
    {
        $this
            ->setName('delete')
            ->setDescription('Delete entities');
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
        $count              = 0;
        $force              = $input->getOption('no-interaction');
        $repository         = $this->repositoryFactory->forEntity($entity);
        $idAccessorRegistry = $this->repositoryFactory->getIdAccessorRegistry();

        foreach ($this->getRecords($finder) as $record) {
            $id     = $idAccessorRegistry->getEntityId($record);
            $choice = 'no';

            if (!$force) {
                $question = new Question(
                    "Delete $entity #$id (yes,No,all)? ",
                    'no'
                );
                $question->setAutocompleterValues(['yes', 'no', 'all']);

                $choice = $this->ask($input, $output, $question);

                if ($choice == 'all') {
                    $force = true;
                }
            }

            if ($force || $choice == 'yes') {
                $repository->remove($record);
                $count++;
            }
        }

        $repository->commit();
        $this->writeln($output, "Deleted $count $entity item(s).");
    }
}
