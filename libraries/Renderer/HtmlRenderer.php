<?php
/**
 * Part of the Joomla Framework Renderer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer;

/**
 * Class HtmlRenderer
 *
 * @package  joomla/renderer
 * @since    1.0
 */
class HtmlRenderer extends Renderer
{
    protected $mediatype = 'text/html';

    /** @var  ScriptStrategyInterface */
    private $clientScript;

    /**
     * @param   ScriptStrategyInterface $strategy
     */
    public function setScriptStrategy(ScriptStrategyInterface $strategy)
    {
        $this->clientScript = $strategy;
    }

    /**
     * {@inheritdoc}
     */
    protected function collectMetadata()
    {
        $metaData = parent::collectMetadata();
        $metaData['wrapper_data']['client_script'] = empty($this->clientScript) ? null : get_class($this->clientScript);

        return $metaData;
    }
}
