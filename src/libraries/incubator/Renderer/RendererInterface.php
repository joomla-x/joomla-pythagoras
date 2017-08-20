<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

use Joomla\Content\ContentTypeVisitorInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Renderer Interface
 *
 * @package  Joomla/Renderer
 *
 * @since    __DEPLOY_VERSION__
 */
interface RendererInterface extends ContentTypeVisitorInterface, StreamInterface
{
    /**
     * @param   string                $type    The content type
     * @param   callable|array|string $handler The handler for that type
     *
     * @return  void
     */
    public function registerContentType($type, $handler);

    /**
     * @return string
     */
    public function getClass();
}
