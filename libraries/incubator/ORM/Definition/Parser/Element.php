<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Parser;

/**
 * Class Element
 *
 * @package  joomla/orm
 * @since    1.0
 */
class Element
{
    /**
     * Constructor
     *
     * @param   array $attributes The data to populate the element with
     */
    public function __construct($attributes)
    {
        foreach ($attributes as $name => $value) {
            $method = 'set' . ucfirst($name);

            if (is_callable([$this, $method])) {
                $this->$method($value);
            } else {
                $this->$name = $value;
            }
        }
    }
}
