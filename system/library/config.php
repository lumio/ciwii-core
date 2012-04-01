<?php
	class C_MAIN_Config {
		private $config	= array();
		
		public function __construct() {}
		
		public function extend($config) {
			if (!is_array($config)) {
				return false;
			}
			
			$this->config	+= $config;
		}
		
		public function set($name, $value) {
			$this->config[$name]	= $value;
		}
		
		/**
		 * Reads an configuration-key and returns its value
		 * @param string $name			Configuration-Key
		 * @param mixed [$default]		Default value if key does not exist
		 * @param mixed [$convert]		If set, value gets converted into another type
		 * @return mixed				Returns value of config-key
		 */
		public function get($name, $default=null, $convert=null) {
			if (array_key_exists($name, $this->config)) {
				$output	= $this->config[$name];
			}
			else {
				$output	= $default;
			}
			
			if ($convert !== null) {
				settype($output, $convert);
			}
			
			return $output;
		}
	}
?>
