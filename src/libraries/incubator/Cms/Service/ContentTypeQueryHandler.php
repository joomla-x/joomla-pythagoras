<?php
/**
 * Part of the Joomla Cms Service Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\Service;

use Joomla\Service\QueryHandler;

/**
 * Content Type Query Handler
 *
 * @package  Joomla/Cms/Service
 *
 * @since    __DEPLOY_VERSION__
 */
class ContentTypeQueryHandler extends QueryHandler
{
    /**
     * Handle the query.
     *
     * @param   ContentTypeQuery $query The query
     *
     * @return  array
     */
    public function handle(ContentTypeQuery $query)
    {
        $entity   = $query->entity;
        $elements = $query->elements;

        return [
            $elements
        ];
    }
}
