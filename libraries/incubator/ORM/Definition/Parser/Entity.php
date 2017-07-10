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
	/** @var  string  Type of the entity (class name with namespace) */
	public $name;
	/** @var  string  The role of the entity */
	public $role = 'default';
	/** @var  string  Parent type */
	public $extends;
	/** @var  string  Name(s) of the primary key column(s) */
	public $primary = 'id';
	/** @var Relation[][]  List of relations */
	public $relations = [
		'belongsTo'      => [],
		'belongsToMany'  => [],
		'hasOne'         => [],
		'hasMany'        => [],
		'hasManyThrough' => []
	];
	/** @var  Field[]  List of fields */
	public $fields = [];
	/** @var array */
	public $storage = null;
	private $entityDtd = 'https://raw.githubusercontent.com/nibralab/joomla-architecture/master/code/Joomla/ORM/Definition/entity.dtd';

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

			case BelongsToMany::class:
				$this->relations['belongsToMany'][] = $relation;
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
				throw new \Exception("Unknown relation type " . get_class($relation));
		}
	}

	/**
	 * @param   string $column The column name
	 *
	 * @return  boolean
	 */
	public function isTableColumn($column)
	{
		if (!is_string($column))
		{
			return false;
		}

		return array_key_exists($column, $this->fields);
	}

	/**
	 * Writes the XML file
	 *
	 * @param   string $filename The filename
	 *
	 * @return  void
	 */
	public function writeXml($filename)
	{
		$dom           = new DOMImplementation;
		$dtd           = $dom->createDocumentType('entity', '', $this->entityDtd);
		$xml           = $dom->createDocument('', '', $dtd);
		$xml->encoding = 'UTF-8';

		$root = $this->createRootElement($xml);
		$root->appendChild($this->createStorageElement($xml));
		$root->appendChild($this->createFieldsElement($xml));
		$root->appendChild($this->createRelationsElement($xml));

		$xml->formatOutput = true;
		$xml->save($filename);
	}

	/**
	 * @param   DOMDocument $xml The XML document
	 *
	 * @return  DOMElement
	 */
	private function createRootElement($xml)
	{
		$root = $xml->createElement('entity');
		$root->setAttribute('name', $this->class);
		$root->setAttribute('role', $this->role);
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
	 * @param   DOMElement $element    The element to add the attributes to
	 * @param   array      $attributes The attributes to add
	 * @param   array      $skip       List of attributes to skip
	 *
	 * @return  void
	 */
	private function addAttributes($element, $attributes, $skip = [])
	{
		foreach ($attributes as $key => $value)
		{
			if (in_array($key, $skip) || is_null($value))
			{
				continue;
			}

			$element->setAttribute($key, $value);
		}
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
			$fields->appendChild($this->createFieldElement($xml, $f));
		}

		return $fields;
	}

	/**
	 * @param  DOMDocument $xml The XML document
	 * @param  Field       $f   The field's XML
	 *
	 * @return DOMElement
	 */
	private function createFieldElement($xml, $f)
	{
		$field = $xml->createElement('field');
		$this->addAttributes($field, get_object_vars($f), ['value', 'validation', 'options']);

		foreach ($f->validation as $rule => $value)
		{
			$field->appendChild($this->createValidationElement($xml, $rule, $value));
		}

		foreach ($f->options as $key => $value)
		{
			$field->appendChild($this->createOptionElement($xml, $key, $value));
		}

		return $field;
	}

	/**
	 * @param   DOMDocument $xml The XML document
	 * @param   string      $rule
	 * @param   string      $value
	 *
	 * @return DOMElement
	 */
	private function createValidationElement($xml, $rule, $value)
	{
		$validation = $xml->createElement('validation');
		$this->addAttributes($validation, ['rule' => $rule, 'value' => $value]);

		return $validation;
	}

	/**
	 * @param   DOMDocument $xml The XML document
	 * @param string        $key
	 * @param string        $value
	 *
	 * @return DOMElement
	 */
	private function createOptionElement($xml, $key, $value)
	{
		$option = $xml->createElement('option');
		$this->addAttributes($option, ['value' => $key]);

		$text = $xml->createTextNode($value);
		$option->appendChild($text);

		return $option;
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
			foreach ($rr as $relation)
			{
				$relations->appendChild($this->createRelationElement($xml, $type, $relation));
			}
		}

		return $relations;
	}

	/**
	 * @param   DOMDocument $xml The XML document
	 * @param   string      $type
	 * @param   Relation    $r
	 *
	 * @return DOMElement
	 */
	private function createRelationElement($xml, $type, $r)
	{
		$relation = $xml->createElement($type);
		$this->addAttributes($relation, get_object_vars($r));

		return $relation;
	}

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

		if (isset($values[0]->belongsToMany))
		{
			$this->relations['belongsToMany'] = $values[0]->belongsToMany;
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
}
