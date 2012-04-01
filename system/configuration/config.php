<?php
	// Allowing application run under PHP running
	// under CGI?
	$config['system.allow.cgi']				= true;
	
	// Allowing application to run under CLI PHP
	$config['system.allow.cli']				= true;
	
	// Allowing application to run under a Webserver
	// Module
	$config['system.allow.web']				= true;
	
	//
	$config['system.autoload.configs']		= array(
		'route',
		'auth'
	);
	
	// What are the default directories to read for views
	// Placeholders are %application and %system for each
	// folder
	// The higher a directory stands in the array (highest = 0
	// lowest = n) the more important it is.
	$config['system.directories.views']		= array(
		'%application/templates',
		'%application/views',
		'%system/templates'
	);
	
	// AUTO				= decides itself, what to use
	// PATH_INFO		= reads uri like a path
	// QUERY_STRING		= reads uri like a query-string
	$config['system.uri']					= 'AUTO';
	
	// Format of output
	// * markup		wrap output in markup-scaffold
	// * raw		direct output
	// Set with setFormat
	$config['output.format']				= 'markup';
	
	// Specific format of markup
	// * html4-strict
	// * html4-transitional
	// * xhtml1-strict
	// * xhtml1-transitional
	// * xhtml1-frameset
	// * xhtml1.1-dtd
	// * xhtml1.1-basic
	// * html5
	$config['output.doctype']				= 'html5';
?>
