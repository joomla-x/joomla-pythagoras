<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer\Event;

use Joomla\Event\Event;

/**
 * Class RenderContentTypeFailureEvent
 *
 * @package Joomla\Renderer
 *
 * @since  1.0
 */
class RenderContentTypeFailureEvent extends Event
{
    /**
     * RenderContentTypeFailureEvent constructor.
     *
     * @param   string      $type       The name of the content type
     * @param   \Exception  $exception  The exception
     */
    public function __construct($type, $exception)
    {
        parent::__construct(
            'onRender' . $type . 'Failure',
            [
                'type'      => $type,
                'exception' => $exception
            ]
        );
    }
}
