<?php
/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Service;

/**
 * Abstract base class for immutable queries.
 * 
 * Usage
 *   Queries are immutable objects that are completely defined by the arguments
 *   passed to them in their constructors.  Query argument validation logic
 *   may be added in the constructor.  Some basic checks are performed to try to
 *   enforce immutability, but these only really guard against accidental
 *   alteration of object state.
 * 
 * Simple example
 * 
 *     final class QuerySimpleTest extends QueryBase
 *     {
 *         public function __construct($test = null)
 *         {
 *             $this->test = $test;
 * 
 *             // Note: Query validation logic should go here.
 * 
 *             // Call the parent constructor at the end.
 *             parent::__construct();
 *         }
 *     }
 * 
 *     $query = new QuerySimpleTest('testing');
 * 
 *     echo $query->name;             // 'QuerySimpleTest'
 *     echo $query->requestedOn;      // [time of instantiation in microseconds since 1 Jan 1970]
 *     echo $query->test;             // 'testing'
 *     echo $query->getName();        // Same as $query->name;
 *     echo $query->getRequestedOn(); // Same as $query->requestedOn;
 *     echo $query->getTest();        // Same as $query->test;
 *
 * @since  __DEPLOY__
 */
abstract class QueryBase extends Value implements Query
{
}
