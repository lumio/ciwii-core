<?php
	class C_Controller extends C_Base {
		private $container = array();
		
		public function __construct() {
			parent::__construct();
		}
		
		public function __get($name) {
			if (array_key_exists($name, $this->container)) {
				return $this->container[$name];
			}
			elseif (isset(C_MAIN_Container::instance()->globals[$name])) {
				return C_MAIN_Container::instance()->globals[$name];
			}
			elseif (isset(C_MAIN_Container::instance()->extensions[$name])) {
				return C_MAIN_Container::instance()->extensions[$name];
			}
			elseif (isset(C_MAIN_Container::instance()->models[$name])) {
				return C_MAIN_Container::instance()->models[$name];
			}
			elseif (isset(C_MAIN_Container::instance()->controllers[$name])) {
				return C_MAIN_Container::instance()->controllers[$name];
			}
		}
		
		public function __set($name, $value) {
			if (substr($name, 0, 6) == 'global') {
				C_MAIN_Container::instance()->globals[$name] = $value;
			}
			else {
				$this->container[$name] = $value;
			}
		}
		
		/**
		 * Load configuration
		 * @param string $config			Config-name
		 * @return bool						true on success
		 */
		protected function loadConfig($config) {
			return C_MAIN_Container::instance()->system->loadConfig($config);
		}
		
		/**
		 * Returns Configuration-value
		 * @param string $key				Contains config-key
		 * @param mixed [$default]			Contains value, if config-key does not exist
		 * @return mixed					Returns config-value
		 */
		protected function config($key, $default=null) {
			return C_MAIN_Container::instance()->config->get($key, $default);
		}
		
		/**
		 * Loads model
		 * @param string $model				Modelname
		 * @param string [$name]			New name for model
		 */
		protected function loadModel($model, $name=false) {
			return C_MAIN_Container::instance()->system->loadModel($model, $name);
		}
		
		/**
		 *
		 */
		protected function loadView($view, $data=array(), $directories=array(), $return=false) {
			return C_MAIN_Container::instance()->system->loadView($view, $data, $directories, $return);
		}
	}
?>
