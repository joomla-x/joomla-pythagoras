<?php

defined('_JEXEC') or die;

require_once __DIR__ . '/legacy/framework.php';

// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;

// Instantiate the application.
$app = JApplicationCms::getInstance(JAPPLICATIONTYPE);

// Execute the application.
$app->execute();
