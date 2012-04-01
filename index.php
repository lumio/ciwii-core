<?php
	
	// set base-directory
	define('C_BASE', dirname(__FILE__));

	// define system-directory
	define('C_SYSTEM', C_BASE.'/system');
	// define application-directory
	define('C_APPLICATION', C_BASE.'/application');

	// load system-files
	require_once C_SYSTEM.'/ciwii.php';
	
	// start
	C_MAIN_Container::instance()->system->start();
?>
