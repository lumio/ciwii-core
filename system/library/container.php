<?php
	final class C_MAIN_Container {
		private static $instance = null;
		
		private $container = array('tmp' => array());
		
		private function __construct() {}
		private function __clone() {}
		public function __toString() {
			return '';
		}
		
		public function instance() {
			if (self::$instance === null) {
				self::$instance = new self;
			}
			
			return self::$instance;
		}
		
		public function __set($name, $value) {
			$this->container[$name] = $value;
		}
		
		public function &__get($name) {
			if (array_key_exists($name, $this->container)) {
				return $this->container[$name];
			}
			
			return $null;
		}
		
		public function __isset($name) {
			return array_key_exists($name, $this->container);
		}
		
		public function __unset($name) {
			unset($this->container[$name]);
		}
		
		public function resettmp() {
			unset($this->container['tmp']);
			$this->container['tmp']	= array();
		}
		
		public function extend($extend, $name, $value) {
			if (array_key_exists($extend, $this->container) && is_array($this->container[$extend])) {
				return $this->container[$extend][$name] = $value;
			}
			
			return false;
		}
	}
?>
