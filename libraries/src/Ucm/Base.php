<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  UCM
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Ucm;

defined('JPATH_PLATFORM') or die;

use Exception;
use JFactory;
use Joomla\CMS\Helper\Content as JHelperContent;
use JTable;
use JTableInterface;
use RuntimeException;

/**
 * Base class for implementing UCM
 *
 * @since  3.1
 */
class Base implements UcmInterface
{
	/**
	 * The UCM type object
	 *
	 * @var    Type
	 * @since  3.1
	 */
	protected $type;

	/**
	 * The alias for the content table
	 *
	 * @var    string
	 * @since  3.1
	 */
	protected $alias;

	/**
	 * Instantiate the UcmBase.
	 *
	 * @param   string  $alias  The alias string
	 * @param   Type    $type   The type object
	 *
	 * @since   3.1
	 */
	public function __construct($alias = null, Type $type = null)
	{
		// Setup dependencies.
		$input = JFactory::getApplication()->input;
		$this->alias = isset($alias) ? $alias : $input->get('option') . '.' . $input->get('view');

		$this->type = isset($type) ? $type : $this->getType();
	}

	/**
	 * Store data to the appropriate table
	 *
	 * @param   array            $data        Data to be stored
	 * @param   JTableInterface  $table       JTable Object
	 * @param   string           $primaryKey  The primary key name
	 *
	 * @return  boolean  True on success
	 *
	 * @since   3.1
	 * @throws  Exception
	 */
	protected function store($data, JTableInterface $table = null, $primaryKey = null)
	{
		if (!$table)
		{
			$table = JTable::getInstance('Ucm');
		}

		$ucmId      = isset($data['ucm_id']) ? $data['ucm_id'] : null;
		$primaryKey = $primaryKey ? $primaryKey : $ucmId;

		if (isset($primaryKey))
		{
			$table->load($primaryKey);
		}

		try
		{
			$table->bind($data);
		}
		catch (RuntimeException $e)
		{
			throw new Exception($e->getMessage(), 500, $e);
		}

		try
		{
			$table->store();
		}
		catch (RuntimeException $e)
		{
			throw new Exception($e->getMessage(), 500, $e);
		}

		return true;
	}

	/**
	 * Get the UCM Content type.
	 *
	 * @return  Type  The UCM content type
	 *
	 * @since   3.1
	 */
	public function getType()
	{
		return new Type($this->alias);
	}

	/**
	 * Method to map the base ucm fields
	 *
	 * @param   array  $original  Data array
	 * @param   Type   $type      UCM Content Type
	 *
	 * @return  array  Data array of UCM mappings
	 *
	 * @since   3.1
	 */
	public function mapBase($original, Type $type = null)
	{
		$type = $type ? $type : $this->type;

		$data = array(
			'ucm_type_id' => $type->id,
			'ucm_item_id' => $original[$type->primary_key],
			'ucm_language_id' => JHelperContent::getLanguageId($original['language'])
		);

		return $data;
	}
}
