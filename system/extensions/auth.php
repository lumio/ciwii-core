<?php
	/**
	 *
	 */
	class AuthExtension extends C_Extension implements CI_Extension {
		public $extName = 'auth';
		
		private $enabled = true;
		
		/**
		 * @property $permissions
		 * Contains all permissions for users
		 */
		private $permissions = array();
		
		/**
		 * @property $level
		 * Current user-level
		 */
		private $level = 0;
		
		/**
		 * Loads all permissions
		 * @return void
		 */
		private function loadPermissions() {
			switch($this->config('auth.data')) {
				case 'config':
					$this->permissions	= $this->config('auth.permissions', array());
					break;
				case 'db':
					
					break;
			}
			
			//var_dump($this->permissions);
		}
		
		public function init() {
			// read configuration
			$this->enabled	= $this->config('auth.enabled', true);
			if (!$this->enabled)
				return false;
			
			// load permissions
			$this->loadPermissions();
			
			// set current user level
			$this->level	= $this->config('auth.default.level', 0);
			
			// add event-listeners
			$this->addEventListener('system.extends.extensions', 'checkExtension');
			$this->addEventListener('system.extends.controllers', 'checkController');
			$this->addEventListener('system.extends.models', 'checkModel');

			$this->addEventListener('system.load.view', 'checkView');
			$this->addEventListener('auth.*', 'checkAuth');
		}
		
		public function checkExtension($file, $name) {
			var_dump($file);
		}
		
		public function checkController($file, $name) {
			var_dump($file);
		}
		
		public function checkModel($file, $name) {
			
		}
		
		public function checkView($file) {
			
		}
		
		public function setUser($level, $data=array()) {
			//die('hello world');
		}
		
		public function checkAuth() {
			echo '<pre>';
			var_dump($this->eventProperties());
		}
	}
?>