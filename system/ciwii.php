<?php
	/**
	 * 
	 */
	
	!defined('C_BASE') && die('Access denied');
	
	class CIWII {
		private $loadedConfigs	= array();
		
		public function __construct() {
			$this->loadMainConfig();
			$this->loadInterfaces();
			$this->loadPrototypes();
			$this->loadExtensions();
		}
		
		/**
		 *
		 */
		public function loadLibrary() {
			// Common functions
			require_once C_SYSTEM.'/library/common.php';
			
			// Container Singleton
			require_once C_SYSTEM.'/library/container.php';
			
			// Config class
			require_once C_SYSTEM.'/library/config.php';
		
			// Events-Handler
			require_once C_SYSTEM.'/library/events.php';
		}
	
		/**
		 *
		 */
		public function initLibrary() {
			// create new Events object
			instance()->events		= new C_MAIN_Events;
		
			// create new Config object
			instance()->config		= new C_MAIN_Config;
		
			// create new extensions-array
			instance()->extensions	= array();
		
			// create new controller-array
			instance()->controllers	= array();
		
			// create new model-array
			instance()->models		= array();
			
			// create new globals-array
			instance()->globals		= array();
			
			// create new instance of master-class
			instance()->system		= new self;
		}
	
		/**
		 *
		 */
		private function loadMainConfig($application=false) {
			if (!$application) {
				$file = C_SYSTEM.'/configuration/config.php';
			}
			else {
				$file = C_APPLICATION.'/configuration/config.php';
				
				if (!file_exists($file)) {
					return false;
				}
			}
			
			require_once $file;
		
			if (!isset($config)) {
				return false;
			}

			instance()->config->extend($config);
		
			// preload configurations
			if (isset($config['system.autoload.configs']) && is_array($config['system.autoload.configs'])) {
		
				// temporarly set system.autoload to registry
				instance()->autoload	= $config['system.autoload.configs'];
			
				foreach (instance()->autoload as $load) {
					$this->loadConfig($load);
				}
			
				unset(instance()->autoload);
			}
			
			if (!$application) {
				$this->loadMainConfig(true);
			}
		}

		public function loadConfig($config, $directories=array()) {
			// ignore files from parent-directory
			if (strpos($config, '..') !== false) {
				return false;
			}
			
			// what files could exist?
			$files		= array();
			foreach ($directories as $dir) {
				$files[]	= rtrim($dir, '/').'/'.$config.'.php';
			}
			$files[]	= C_APPLICATION.'/configuration/'.$config.'.php';
			$files[]	= C_SYSTEM.'/configuration/'.$config.'.php';
			
			$loadConf	= '';
			foreach ($files as $file) {
				if (file_exists($file) && !in_array($file, $this->loadedConfigs)) {
					$loadConf	= $file;
					break;
				}
			}
			
			// if no file to load, escape from starting a mess
			if (empty($loadConf)) {
				return false;
			}
			
			// load configuration
			$config	= array();
			$this->loadedConfigs[]	= $loadConf;
			require_once $loadConf;
			instance()->config->extend($config);
			
			return true;
		}

		/**
		 * Loads class into container
		 * @param string $file				Name of file
		 * @param array $directories		Directories, where to find the file
		 * @param string $class				Name of class
		 * @param string $name				What name should the class have in container
		 * @param string $extend			Where to put the new class
		 */
		public function loadClass($file, $directories, $class, $name=null, $extend='extensions', $init=true) {
			// remove php extension
			if (substr($file, -4) == '.php') {
				$file = substr($file, 0, -4);
			}
			
			if (strpos($file, '..') !== false) {
				return false;
			}
			
			$allowedExtends = array('extensions', 'controllers', 'models', 'globals');
			if (!in_array($extend, $allowedExtends)) {
				return false;
			}
			
			if (empty($class)) {
				return false;
			}
			
			// check if file is absolute
			if (strpos($file, '/') !== false && $directories == false) {
				$directories	= array(dirname($file));
				$file			= basename($file);
			}
			
			// set name if needed
			if (empty($name)) {
				$name	= ucwords($file);
			}
			
			// load class
			foreach ($directories as $directory) {
				$filename = rtrim($directory, '/')."/$file.php";
				if (file_exists($filename)) {

					$result = instance()->events->raiseEvent("system.extends.{$extend}", array($file, $name));
					if ($result === false) {
						continue;
					}
					
					instance()->tmp['loadClass']	= array($file, $class, $name, $extend, $init);
					require_once $filename;
					
					// check if class exists
					if (!class_exists(instance()->tmp['loadClass'][1])) {
						exitError('Sorry, but I can\'t find a class called '.instance()->tmp['loadClass'][1].'!');
					}
					
					// create instance of class
					$class	= instance()->tmp['loadClass'][1];
					$name	= instance()->tmp['loadClass'][2];
					$extend = instance()->tmp['loadClass'][3];
					$init	= instance()->tmp['loadClass'][4];
					
					$object	= instance()->extend($extend, $name, new $class);
					if ($init && method_exists($object, 'init')) {
						$object->init();
					}
					
					instance()->resettmp();
					
					return true;
					break;
				}
			}
		}

		public function loadModel($model, $name=null) {
			// ignore files from parent-directory
			if (strpos($model, '..') !== false) {
				return false;
			}
			
			// check if model-file exists
			$modelFile	= C_APPLICATION.'/models/'.strtolower($model).'.php';
			if (!file_exists($modelFile)) {
				exitError('Model '.$modelFile.' does not exist');
			}
			
			// set model-names and load file
			if (empty($name)) {
				$name	= ucwords($model);
			}
			instance()->tmp['loadModelName']	= array(ucwords($model), $name);
			require_once $modelFile;
			
			// check if model-class exists
			if (!class_exists(instance()->tmp['loadModelName'][0])) {
				exitError('Model-Class '.instance()->tmp['loadModelName'][0].' does not exist');
			}
			
			// create instance of model
			$className	= instance()->tmp['loadModelName'][0];
			instance()->models[instance()->tmp['loadModelName'][1]]	= new $className;
			
			// check and init model
			if (method_exists(instance()->models[instance()->tmp['loadModelName'][1]], 'init')) {
				instance()->models[instance()->tmp['loadModelName'][1]]->init();
			}
			
			return true;
		}

		/**
		 * Loads interfaces
		 * 
		 */
		private function loadInterfaces($readdir='') {
			if (empty($readdir)) {
				$this->loadInterfaces(C_SYSTEM.'/interfaces');
				$this->loadInterfaces(C_APPLICATION.'/interfaces');
			}
			else {
				$readdir	= rtrim($readdir, '/').'/';
				$dir		= glob($readdir.'/*.php');
				foreach ($dir as $elm) {
					if (!is_file($elm)) {
						continue;
					}
					
					require_once $elm;
				}
			}
		}

		/**
		 *
		 */
		private function loadPrototypes($readdir='') {
			$dir	= '';
			if (!empty($readdir)) {
				$dir	= rtrim($readdir, '/').'/';
			}
			
			$dir	= glob(C_SYSTEM.'/prototypes/'.$dir.'*.php');
			// loading prototypes
			foreach ($dir as $elm) {
				if (!is_file($elm)) {
					continue;
				}
				
				$result = instance()->events->raiseEvent("system.load.prototype", array($elm));
				if ($result === false) {
					continue;
				}
		
				require_once $elm;
			}
			
			// read weaker prototypes
			if (empty($readdir)) {
				for ($i=0; $i<=3; $i++) {
					$this->loadPrototypes("r{$i}");
				}
			}
		}

		/**
		 *
		 */
		private function loadExtensions($baseDir='', $extension='', $init=true) {
			if (empty($baseDir)) {
				$result1 = $this->loadExtensions(C_SYSTEM.'/extensions');
				$result2 = $this->loadExtensions(C_APPLICATION.'/extensions');
				
				if ($result1 === true && $result2 === true)
					return true;

				return false;
			}
			
			$success = true;
			if (empty($extension)) {
				// read base-directory
				$dir	= glob(rtrim($baseDir, '/').'/*');
				foreach ($dir as $elm) {
					if (is_file($elm) && substr($elm, -4) == '.php') {
						$this->loadExtensionFile($elm, null, $init);
					}
					elseif (is_dir($elm)) {
						if (!$this->loadExtensions($elm, basename($elm)))
							$success = false;
					}
					else {
						continue;
					}
				}
			}
			else {
			
			}
			
			return $success;
		}
		
		/**
		 *
		 */
		private function loadExtensionFile($file, $name=null, $init) {
			if (empty($name)) {
				$name	= substr(basename($file), 0, -4);
			}
			
			return $this->loadClass($file, false, ucwords($name).'Extension', $name, 'extensions', $init);
		}
		
		/**
		 *
		 */
		public function loadView($view, $data=array(), $directories=array(), $return=false) {
			if (empty($directories)) {
				// get default directories
				$directories	= instance()->config->get('system.directories.views', array());
			}
			
			$directories	= $this->arraytr($directories, array(
				'%application'	=> C_APPLICATION,
				'%system'		=> C_SYSTEM
			));
		}
		
		/**
		 * Checks the system-environment
		 */
		private function systemEnvironment() {
			if (!defined('PHP_SAPI')) {
				exitError('Undefined PHP_SAPI. Quitting');
			}
		
			$scriptAllowsCGI		= instance()->config->get('system.allow.cgi', false);
			$scriptAllowsCLI		= instance()->config->get('system.allow.cli', false);
			$scriptAllowsWeb		= instance()->config->get('system.allow.web', false);
		
			$sapi			= php_sapi_name();
			switch ($sapi) {
				case 'cgi':
				case 'cgi-fcgi':
					if (!$scriptAllowsCGI) {
						exitError('This script is not allowed to run under CGI');
					}
					
					if (!defined('STDIN')) {
						define('STDIN', fopen('php://stdin', 'r'));
					}
					if (!defined('STDOUT')) {
						define('STDOUT', fopen('php://stdout', 'w'));
					}
					if (!defined('STDERR')) {
						define('STDERR', fopen('php://stderr', 'w'));
					}
				
					return 'cgi';
				
					break;
				case 'cli':
					if (!$scriptAllowsCLI) {
						exitError('This script is not allowed to run under CLI');
					}
					
					// Disable output-buffer for command-line
					if (isset(instance()->extensions->output)) {
						instance()->extensions->output->disable();
					}
					return 'cli';
				
					break;
				case 'aolserver':
				case 'apache':
				case 'apache2filter':
				case 'apache2handler':
				case 'caudium':
				case 'continuity':
				case 'embed':
				case 'isapi':
				case 'litespeed':
				case 'milter':
				case 'nsapi':
				case 'phttpd':
				case 'pi3web':
				case 'roxen':
				case 'thttpd':
				case 'tux':
				case 'webjames':
					if (!$scriptAllowsWeb) {
						exitError('This script is not allowed to run under a Webserver-Module');
					}
				
					return 'web';
				
					break;
			}
		}
	
		/**
		 *
		 */
		private function systemResetGlobals() {
			$_GET	= array();
		}
	
		/**
		 *
		 */
		public function initController($controller, $directories=false) {
			$file	= strtolower($controller).'.php';
			if ($directories === false) {
				$file	= C_APPLICATION.'/controllers/'.$file;
			}
			$this->loadClass($file, $directories, ucwords($controller), null, 'controllers');
		}
		
		/**
		 *
		 */
		public function callController($controller, $method, $arguments=array()) {
			if (is_string($controller)) {
				$controller	= ucwords($controller);
				if (!isset(instance()->controllers[$controller])) {
					exitError('Controller '.$controller.' not initiated');
					return false;
				}
				
				$controller	= &instance()->controllers[$controller];
			}
			
			// check given method
			if (!method_exists($controller, $method)) {
				exitError('Method '.$method.' does not exist in '.get_class($controller));
			}
			
			if (!is_array($arguments)) {
				$arguments = array($arguments);
			}
			
			// call given function
			return call_user_func_array(array(
				$controller,
				$method
			), $arguments);
		}
		
		/**
		 *
		 */
		public function callExtension($extension, $method, $arguments=array()) {
			if (!is_array($arguments)) {
				$arguments = array($arguments);
			}
			
			if (!isset(instance()->extensions[$extension])) {
				exitError('Extension '.$extension.' not initiated');
			}
			
			if (!method_exists(instance()->extensions[$extension], $method)) {
				exitError('Method '.$method.' does not exist');
			}
			
			return call_user_func_array(array(instance()->extensions[$extension], $method), $arguments);
		}
		
		public function callMethod($method, $arguments=array()) {
			if (!is_array($method)) {
				return false;
			}
			
			switch($method[0]) {
				case 'controller':
					return $this->callController($method[1], $method[2], $arguments);
				case 'extension':
					return $this->callExtension($method[1], $method[2], $arguments);
				case 'event':
					return instance()->events->raiseEvent($method[1], $arguments);
			}
		}
		
		public function start() {
			$env	= $this->systemEnvironment();
			$route	= $this->callExtension('route', 'getRoute', array($env));
			$this->initController($route['controller']);

			// raise start-event
			instance()->events->raiseEvent('system.start');

			$this->callController($route['controller'], $route['method'], $route['arguments']);

			// raise output and end-event
			instance()->events->raiseEvent('system.output');
			instance()->events->raiseEvent('system.end');
		}
	
		public function arraytr($input, $replacement) {
			if (is_string($input)) {
				return strtr($input, $replacement);
			}
			elseif(!is_array($input)) {
				return $input;
			}
			
			foreach ($input as $key => $value) {
				if (is_array($value)) {
					$input[$key]	= $this->arraytr($value, $replacement);
				}
				elseif (is_string($value)) {
					$input[$key]	= strtr($value, $replacement);
				}
			}
			
			return $input;
		}
	}
	
	CIWII::loadLibrary();
	CIWII::initLibrary();
?>