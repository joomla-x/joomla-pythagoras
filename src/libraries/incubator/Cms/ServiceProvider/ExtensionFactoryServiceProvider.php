<?php
/**
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\ServiceProvider;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Extension\DefaultExtensionFactory;
use Joomla\Extension\ExtensionFactoryInterface;
use Joomla\Extension\FileExtensionFactory;

/**
 * Class ExtensionFactoryServiceProvider
 *
 * @package Joomla\Cms\ServiceProvider
 *
 * @since   1.0
 */
class ExtensionFactoryServiceProvider implements ServiceProviderInterface
{
    /** @var string The access key for the container */
    private $key = 'ExtensionFactory';

    /**
     * Add the plugin factory to a container
     *
     * @param   Container $container The container
     * @param   string    $alias     An optional alias
     *
     * @return  void
     */
    public function register(Container $container, $alias = null)
    {
        $container->set(
            $this->key,
            [
                $this,
                'createExtensionFactory'
            ],
            true,
            true
        );

        if (!empty($alias)) {
            $container->alias($alias, $this->key);
        }
    }

    /**
     * Create the plugin factory
     *
     * @param   Container $container The container
     *
     * @return  ExtensionFactoryInterface
     */
    public function createExtensionFactory(Container $container)
    {
        return new DefaultExtensionFactory($container->get('ConfigDirectory'), $container);
    }
}
