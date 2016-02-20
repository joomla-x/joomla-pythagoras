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
 * Abstract base class for immutable commands.
 *
 * Usage
 *   Commands are immutable objects that are completely defined by the arguments
 *   passed to them in their constructors.  Command argument validation logic
 *   may be added in the constructor.  Some basic checks are performed to try to
 *   enforce immutability, but these only really guard against accidental
 *   alteration of object state.
 *
 * Simple example
 *
 *     final class CommandSimpleTest extends CommandBase
 *     {
 *         public function __construct($test = null)
 *         {
 *             $this->test = $test;
 *
 *             // Note: Command validation logic should go here.
 *
 *             // Call the parent constructor at the end.
 *             parent::__construct();
 *         }
 *     }
 *
 *     $command = new CommandSimpleTest('testing');
 *
 *     echo $command->name;             // 'CommandSimpleTest'
 *     echo $command->requestedOn;      // [time of instantiation in microseconds since 1 Jan 1970]
 *     echo $command->test;             // 'testing'
 *     echo $command->getName();        // Same as $command->name;
 *     echo $command->getRequestedOn(); // Same as $command->requestedOn;
 *     echo $command->getTest();        // Same as $command->test;
 *
 * @since  __DEPLOY__
 */
abstract class CommandBase extends Value implements Command
{
}
