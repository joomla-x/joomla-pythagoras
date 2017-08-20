<?php
$basedir = dirname(dirname(__DIR__));

if (!defined('JPATH_ROOT'))
{
	define('JPATH_ROOT', $basedir . '/src');
}

require_once $basedir . '/vendor/autoload.php';
