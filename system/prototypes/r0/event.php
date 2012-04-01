<?php
	class C_Event {
		public function __construct() {
		
		}
	
		/**
		 * Adds an Event-Listener to a given callback-method
		 * @param string $eventname					Contains name of event
		 * @param string/array $callback			Contains callback-method
		 * @return void
		 */
		protected function addEventListener($eventname, $callback) {
			if (is_string($callback)) {
				$callback	= array($this, $callback);
			}
			
			instance()->events->addEventListener($eventname, $callback);
		}
		
		/**
		 * Removes an Event-Listener
		 * @param string $eventname					Contains name of event
		 * @param string/array [$callback]			Contains callback-method
		 * @return boolean
		 */
		protected function removeEventListener($eventname, $callback=null) {
		
		}
		
		/**
		 * Raises an event and returns its result
		 * @param string $eventname					Contains the name of event
		 * @param mixed [$input]					Input what gets formed by event-methods
		 * @param mixed								The formed input
		 */
		protected function raiseEvent() {
			//$eventname, $input=null
			$argc	= func_num_args();
			
			// check if there are any, if not, return null
			if (!$argc) {
				return null;
			}
			
			// get arguments
			$args		= func_get_args();
			
			// get eventname
			$eventname	= array_shift($args);
			
			return instance()->events->raiseEvent($eventname, $args);
		}
		
		/**
		 * Returns original event parameters
		 */
		protected function eventParameters() {
			return instance()->events->getOriginalParameters();
		}
		
		/**
		 * Returns event-object
		 */
		protected function eventProperties() {
			return instance()->events->getEventProperties();
		}
	}
?>
