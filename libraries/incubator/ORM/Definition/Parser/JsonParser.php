<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Parser;

use Joomla\ORM\Definition\Locator\LocatorInterface;
use Joomla\ORM\Definition\Parser\Entity as EntityStructure;

/**
 * Class JsonParser
 *
 * @package  Joomla/ORM
 *
 * @since    1.0
 */
class JsonParser implements ParserInterface
{
	private $data = null;

	/**
	 * Open the description of the entity
	 *
	 * @param string $descriptionFile
	 *
	 * @return mixed
	 */
	public function open($descriptionFile)
	{
		$this->data = json_decode(file_get_contents($descriptionFile), true);
	}

	/**
	 * Parse the entity definition
	 *
	 * @param   Callable[]       $callbacks Hooks for pre- and postprocessing of elements
	 * @param   LocatorInterface $locator   The description file locator for related entities
	 *
	 * @return  EntityStructure
	 */
	public function parse($callbacks, LocatorInterface $locator)
	{
		$attributes         = [];
		$attributes['name'] = $this->data['name'];

		if (isset($this->data['extends']))
		{
			$attributes['extends'] = $this->data['extends'];
		}

		if (isset($callbacks['onBeforeEntity']))
		{
			call_user_func($callbacks['onBeforeEntity'], $attributes);
		}

		$structure = new EntityStructure($attributes);

		foreach ($this->data['fields'] as $fieldData)
		{
			if (isset($callbacks['onBeforeField']))
			{
				call_user_func($callbacks['onBeforeField'], $fieldData);
			}

			if (isset($fieldData['validation']))
			{
				foreach ($fieldData['validation'] as $key => $validation)
				{
					$fieldData['validation'][$key] = (object)$validation;
				}
			}

			if (isset($fieldData['option']))
			{
				foreach ($fieldData['option'] as $key => $option)
				{
					$fieldData['option'][$key] = (object)$option;
				}
			}

			$field = new Field($fieldData);

			if (isset($callbacks['onAfterField']))
			{
				call_user_func($callbacks['onAfterField'], $field);
			}

			$structure->fields[$fieldData['name']] = $field;
		}

		return $structure;
	}
}
