<?php
/**
 * @package        Joomla.FunctionalTest
 * @copyright      Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Celtic\Testing\Joomla;

$config = new \SeleniumConfig();

if (empty($config->versionAdapter))
{
	class SeleniumTestCase extends Version3Adapter
	{
	}
}
else
{
	$classDeclaration = "namespace " . __NAMESPACE__ . ";\n\nclass SeleniumTestCase extends {$config->versionAdapter}\n{\n}\n";
	eval($classDeclaration);
}
