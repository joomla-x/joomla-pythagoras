<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Parser;

use DOMDocument;
use DOMElement;
use DOMImplementation;

/**
 * Class Entity
 *
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
class Entity extends Element
{
	/** @var  string  Fully qualified class name of the entity */
	public $class;

	/** @var  string  Type of the entity (class name without namespace) */
	public $name;

	/** @var  string  Parent type */
	public $extends;

	/** @var  string  Name(s) of the primary key column(s) */
	public $primary = 'id';

	/** @var Relation[][]  List of relations */
	public $relations = [
		'belongsTo'      => [],
		'hasOne'         => [],
		'hasMany'        => [],
		'hasManyThrough' => []
	];

	/** @var  Field[]  List of fields */
	public $fields = [];

	/** @var array */
	public $storage = null;

	/**
	 * Set the fields
	 *
	 * @param   Field[] $values The fields
	 *
	 * @return  void
	 */
	protected function setFields($values)
	{
		foreach ($values[0]->fields as $name => $field)
		{
			$this->fields[$name] = $field;
		}
	}

	/**
	 * Add a relations
	 *
	 * @param   Relation $relation The relation
	 *
	 * @return  void
	 * @throws  \Exception
	 */
	public function addRelation(Relation $relation)
	{
		switch (get_class($relation))
		{
			case BelongsTo::class:
				$this->relations['belongsTo'][] = $relation;
				break;

			case HasOne::class:
				$this->relations['hasOne'][] = $relation;
				break;

			case HasMany::class:
				$this->relations['hasMany'][] = $relation;
				break;

			case HasManyThrough::class:
				$this->relations['hasManyThrough'][] = $relation;
				break;

			default:
				throw new \Exception("Unknown relation type ". get_class($relation));
		}
	}

	/**
	 * @param   string $column
	 *
	 * @return  bool
	 */
	public function isTableColumn($column)
	{
		if (!is_string($column))
		{
			return false;
		}

		return array_key_exists($column, $this->fields) || array_key_exists($column, $this->relations['belongsTo']);
	}

	/**
	 * Set the relations
	 *
	 * @param   Relation[] $values The relations
	 *
	 * @return  void
	 */
	protected function setRelations($values)
	{
		if (isset($values[0]->belongsTo))
		{
			$this->relations['belongsTo'] = $values[0]->belongsTo;
		}

		if (isset($values[0]->hasOne))
		{
			$this->relations['hasOne'] = $values[0]->hasOne;
		}

		if (isset($values[0]->hasMany))
		{
			$this->relations['hasMany'] = $values[0]->hasMany;
		}

		if (isset($values[0]->hasManyThrough))
		{
			$this->relations['hasManyThrough'] = $values[0]->hasManyThrough;
		}
	}

	/**
	 * Sets the storage
	 *
	 * @param   array $values The values
	 *
	 * @return  void
	 */
	protected function setStorage($values)
	{
		$vars = get_object_vars($values[0]);

		foreach ($vars as $type => $attributes)
		{
			$this->storage         = get_object_vars($attributes[0]);
			$this->storage['type'] = $type;

			if (isset($this->storage['primary']))
			{
				$this->primary = $this->storage['primary'];
			}

			break;
		}
	}

	/**
	 * Writes the XML file
	 *
	 * @param   string  $filename The filename
	 *
	 * @return  void
	 */
	public function writeXml($filename)
	{
		$dom = new DOMImplementation;
		$dtd = $dom->createDocumentType('entity', '', 'https://github.com/nibralab/joomla-architecture/blob/master/code/Joomla/ORM/Definition/entity.dtd');
		$xml = $dom->createDocument('', '', $dtd);
		$xml->encoding = 'UTF-8';

		$root = $this->createRootElement($xml);
		$root->appendChild($this->createStorageElement($xml));
		$root->appendChild($this->createFieldsElement($xml));
		$root->appendChild($this->createRelationsElement($xml));

		$xml->formatOutput = true;
		$xml->save($filename);
	}

	/**
	 * @param   DOMElement  $element    The element to add the attributes to
	 * @param   array       $attributes The attributes to add
	 * @param   array       $skip       List of attributes to skip
	 *
	 * @return  void
	 */
	private function addAttributes($element, $attributes, $skip = [])
	{
		foreach ($attributes as $key => $value)
		{
			if (in_array($key, $skip) || empty($value))
			{
				continue;
			}

			$element->setAttribute($key, $value);
		}
	}

	/**
	 * @param   DOMDocument  $xml The XML document
	 *
	 * @return  DOMElement
	 */
	private function createRootElement($xml)
	{
		$root = $xml->createElement('entity');
		$root->setAttribute('name', $this->class);
		$xml->appendChild($root);

		return $root;
	}

	/**
	 * @param   DOMDocument $xml The XML document
	 *
	 * @return  DOMElement
	 */
	private function createStorageElement($xml)
	{
		$storage = $xml->createElement('storage');
		$type    = $xml->createElement($this->storage['type']);
		$this->addAttributes($type, $this->storage, ['type']);
		$storage->appendChild($type);

		return $storage;
	}

	/**
	 * @param   DOMDocument $xml The XML document
	 *
	 * @return  DOMElement
	 */
	private function createFieldsElement($xml)
	{
		$fields = $xml->createElement('fields');

		foreach ($this->fields as $f)
		{
			$field = $xml->createElement('field');
			$this->addAttributes($field, get_object_vars($f), ['value', 'validation', 'options']);

			foreach ($f->validation as $rule => $value)
			{
				$validation = $xml->createElement('validation');
				$this->addAttributes($validation, ['rule' => $rule, 'value' => $value]);
				$field->appendChild($validation);
			}

			foreach ($f->options as $key => $value)
			{
				$option = $xml->createElement('option');

				// @todo Implement handling of options
				$field->appendChild($option);
			}

			$fields->appendChild($field);
		}

		return $fields;
	}

	/**
	 * @param   DOMDocument $xml The XML document
	 *
	 * @return  DOMElement
	 */
	private function createRelationsElement($xml)
	{
		$relations = $xml->createElement('relations');

		foreach ($this->relations as $type => $rr)
		{
			foreach ($rr as $r)
			{
				$relation = $xml->createElement($type);
				$this->addAttributes($relation, get_object_vars($r));
				$relations->appendChild($relation);
			}
		}

		return $relations;
	}
}
