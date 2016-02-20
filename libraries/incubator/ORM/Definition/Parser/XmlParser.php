<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Parser;

use Joomla\ORM\Definition\Locator\LocatorInterface;
use Joomla\ORM\Exception\InvalidElementException;

/**
 * Class XmlParser
 *
 * @package  Joomla/orm
 * @since    1.0
 */
class XmlParser extends \XMLReader
{
	/**
	 * Parse the entity XML.
	 *
	 * @param   Callable[]       $callbacks Hooks for pre- and postprocessing of elements
	 * @param   LocatorInterface $locator   The XML description file locator
	 *
	 * @return  Entity
	 */
	public function parse($callbacks, LocatorInterface $locator)
	{
		$data = null;

		while ($this->read())
		{
			if ($this->nodeType == \XMLReader::ELEMENT)
			{
				$child = $this->parseElement($this->name, $callbacks, $locator);
				$data  = $child;
				break;
			}
		}

		return $data;
	}

	/**
	 * Recursively parse the element tree.
	 *
	 * @param   string           $name      Name of the current element
	 * @param   Callable[]       $callbacks Hooks for pre- and postprocessing of elements
	 * @param   LocatorInterface $locator   The XML description file locator
	 *
	 * @return  Element  The parsed structure
	 */
	protected function parseElement($name, $callbacks, LocatorInterface $locator)
	{
		$hasChildren = !$this->isEmptyElement;
		$attributes  = $this->hasAttributes ? $this->getAttributes() : [];
		$children    = [];

		$callback = 'onBefore' . ucfirst($name);

		if (isset($callbacks[$callback]))
		{
			call_user_func($callbacks[$callback], $attributes);
		}

		if ($hasChildren)
		{
			while ($this->read())
			{
				$currentName = $this->name;

				if ($this->nodeType == \XMLReader::END_ELEMENT && $currentName == $name)
				{
					break;
				}

				if ($this->nodeType == \XMLReader::ELEMENT)
				{
					$child = $this->parseElement($currentName, $callbacks, $locator);

					if (isset($child->name))
					{
						$children[$currentName][$child->name] = $child;
					}
					else
					{
						$children[$currentName][] = $child;
					}
				}
				elseif ($currentName[0] == '#')
				{
					$text = trim($this->value);

					if (!empty($text))
					{
						$children[$currentName] = $text;
					}
				}
				else
				{
					throw new InvalidElementException("Unknown element {$currentName} => {$this->value}");
				}
			}
		}

		$className = __NAMESPACE__ . '\\' . ucfirst($name);

		if (!class_exists($className))
		{
			$className = __NAMESPACE__ . '\\Element';
		}

		$element = new $className(array_merge($attributes, $children));

		$callback = 'onAfter' . ucfirst($name);

		if (isset($callbacks[$callback]))
		{
			call_user_func($callbacks[$callback], $element, $locator);
		}

		return $element;
	}

	/**
	 * Get element attributes
	 *
	 * @return  array  The attributes
	 */
	protected function getAttributes()
	{
		$attributes = [];
		$this->moveToFirstAttribute();

		do
		{
			$attributes[$this->name] = $this->value;
		}
		while ($this->moveToNextAttribute());

		return $attributes;
	}
}
