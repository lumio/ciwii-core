<?php
	class Main extends C_Controller {
		public function init() {
			echo 'hello world';
		}
		
		public function index() {
			$this->auth->setUser(0);
			
			/*$this->output->disable();
			$this->output->disable();
			$this->output->disableOnEvent();
			$this->addEventListener('test.event1', 'event1');
			$this->addEventListener('test.event2', 'event2');
			$this->raiseEvent('test.event1', array('a'=>1, 'b'=>2), 'foo');
			$this->raiseEvent('test.event2', 'bar');*/
		}
		
		public function event1() {
			echo 'test';
		}
		
		public function yeah() {
			//$this->loadView('test');
		}
	}
?>
