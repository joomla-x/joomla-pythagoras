<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Parser;

/**
 * Class Fieldset
 *
 * @package  joomla/orm
 * @since    1.0
 */
class Fieldset extends Field
{
    /** @var  Field[]  The field list */
    public $fields = [];

    /**
     * Set the fields
     *
     * @param   Field[] $values The fields
     *
     * @return  void
     */
    protected function setField($values)
    {
        foreach ($values as $name => $field) {
            $this->fields[$name] = $field;
        }
    }
}
