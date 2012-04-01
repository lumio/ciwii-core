<?php
	$config['auth.enabled']			= true;
	
	// auth.method defines how things should be checked.
	// * blacklist...	if this option is set, every file which should not be loaded, needs to
	//					be defined
	// * whitelist...	you need to define every file which should be allowed to be loaded
$config['auth.method']			= 'whitelist';

	// where is the information stored, what is needed to load files
	// * config...		data is stored directly in the config-file
	// * db...			data is stored in a database. You need to define how to communicate with
	//					the database
	$config['auth.data']			= 'config';
	
	// what level should this webapp run with?
	// * 0...			run as guest - default
	// *-1...			run as admin (no permissions needed)
	// * n...			you can specify more levels (n is just a number)
	$config['auth.default.level']	= 0;
	
	$config['auth.permissions']		= array(
		0	=>	array(),
		1	=>	array()
	);
	
	// what level should this run under?
	// * 0...			run as guest - usually this is default
	// * 1...			run as admin
	// * n...			you can specify more levels (n is just a number)
	$config['auth.default.level']	= 0;
	
	// describe the database
	$config['auth.db.table.user']	= 'users';
	$config['auth.db.table.data']	= 'authdata';
?>