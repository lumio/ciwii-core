<?php
	class C_Model extends C_Event {
		private $container = array();
		
		public function __construct() {
			parent::__construct();
		}
		
		public function __get($name) {
			if (array_key_exists($name, $this->container)) {
				return $this->container[$name];
			}
			elseif (isset(C_MAIN_Container::instance()->models[$name])) {
				return C_MAIN_Container::instance()->models[$name];
			}
			elseif (isset(C_MAIN_Container::instance()->controllers[$name])) {
				return C_MAIN_Container::instance()->controllers[$name];
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
		 * @param mixed [$convert]			If set, value gets converted into another type
		 * @return mixed					Returns config-value
		 */
		protected function config($key, $default=null, $convert=null) {
			return C_MAIN_Container::instance()->config->get($key, $default, $convert);
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
		protected function loadView($view, $data, $return=false) {
		
		}
	}
?>
