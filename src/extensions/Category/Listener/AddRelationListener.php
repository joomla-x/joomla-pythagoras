<?php
/**
 * Part of the Joomla! Category Extension Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension\Category\Listener;

use Joomla\Extension\Article\Entity\Article;
use Joomla\Extension\Category\Entity\Category;
use Joomla\ORM\Definition\Parser\BelongsTo;
use Joomla\ORM\Definition\Parser\HasMany;
use Joomla\ORM\Event\DefinitionCreatedEvent;

/**
 * Class AddRelationListener
 *
 * @package Joomla\Extension\Content
 *
 * @since   1.0
 */
class AddRelationListener
{
    /**
     * Event handler
     *
     * @param   DefinitionCreatedEvent $event The event
     *
     * @return  void
     */
    public function addCategoryRelation(DefinitionCreatedEvent $event)
    {
        $entityClass = $event->getEntityClass();
        $definition  = $event->getDefinition();
        $builder     = $event->getEntityBuilder();

        if (!$this->hasCategories($entityClass)) {
            return;
        }

        $definition->addRelation(
            new BelongsTo(
                [
                    'name'      => 'category_id',
                    'entity'    => 'Category',
                    'reference' => 'id'
                ]
            )
        );

        $meta = $builder->getMeta(Category::class);

        $meta->addRelation(
            new HasMany(
                [
                    'name'      => $definition->columnName($definition->name),
                    'entity'    => $definition->name,
                    'reference' => 'category_id'
                ]
            )
        );
    }

    /**
     * @param   string  $entityClass  The entity class
     *
     * @return  boolean
     */
    private function hasCategories($entityClass)
    {
        return $entityClass == Article::class;
    }
}
