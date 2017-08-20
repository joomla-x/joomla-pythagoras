<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Content\Type;

use Joomla\Content\CompoundTypeInterface;
use Joomla\Content\ContentTypeInterface;

/**
 * Abstract ContentType
 *
 * @package  Joomla/Content
 * @since    __DEPLOY_VERSION__
 */
abstract class AbstractCompoundType extends AbstractContentType implements CompoundTypeInterface
{
    /** @var  ContentTypeInterface[] Content elements */
    public $elements = [];

    /**
     * AbstractCompoundType Constructor.
     *
     * @param   string                 $title    The title
     * @param   string                 $id       The identifier
     * @param   \stdClass              $params   The parameters
     * @param   ContentTypeInterface[] $elements Content elements
     */
    public function __construct($title, $id, $params, $elements = [])
    {
        parent::__construct($title, $id, $params);

        foreach ($elements as $element) {
            $this->add($element);
        }
    }

    /**
     * Add a content element as a child
     *
     * @param   ContentTypeInterface $content  The content element
     *
     * @return  void
     */
    public function add(ContentTypeInterface $content)
    {
        $this->elements[] = $content;
    }
}
