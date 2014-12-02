<?php
namespace Imager;

define('IMAGER_DIR', __DIR__);

spl_autoload_register(function($className) {
	if (strpos($className, 'Imager') !== false) {
		$temp = str_replace('Imager', '', $className);
		require_once IMAGER_DIR . str_replace('\\', '/', $temp) . '.php';
		return true;
	}
	return false;
}, true, true);
