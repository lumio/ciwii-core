<?php
	/**
	 *
	 */
	class C_MAIN_Events {
		/**
		 * array $events			Contains events and its callbacks
		 */
		private $events			= array();
		/**
		 * array $wildcardEvents	Contains wildcard-events and its callbacks
		 */
		private $wildcardEvents	= array();
		/**
		 * array $parameters		Contains parameters of the current ongoing events
		 */
		private $parameters		= array();
		/**
		 * array $eventProperties		Contains specific information about the event, its target, etc
		 */
		private $eventProperties	= array();
		
		public function __construct() {}

		/**
		 * Raises Event
		 * @param string $event			Contains eventname
		 * @param array [$args]			A list of arguments.
		 */
		public function raiseEvent($event, $args=array()) {
			$output	= null;
			
			if (!is_array($args)) {
				$args	= array($args);
			}
			
			$this->parameters[]			= $args;
			$this->eventProperties[]	= array(
				'event'			=> $event,
				'wildcardEvent'	=> false
			);
			
			// check if wildcard-event exists
			foreach ($this->wildcardEvents as $wild => $callbackList) {
				$useEvent = false;
				
				// check if the only wildcard is at the end of the event-name
				if (substr_count($wild, '*') == 1 && substr($wild, -1) == '*') {
					// check if event-name starts with the name of the wildcard-event
					$check	= substr($wild, 0, -1);
					if (substr($event, 0, strlen($check)) == $check)
						$useEvent	= true;
				}
				// if not, use regex
				else {
					$check	= '/'.preg_quote($wild, '/').'/';
					$check	= str_replace('\\*', '.+', $check);
					if (preg_match($check, $event))
						$useEvent	= true;
				}
				
				if ($useEvent) {
					$this->eventProperties[]	= array_merge($this->getEventProperties(), array(
						'wildcardEvent'	=> $wild
					));
					$this->execCallbackList($callbackList, $args);
				}
			}
			
			// remove current parameters and the event-object
			$this->removeCurrentParameters();
			$this->removeCurrentEventProperties();
			
			// check if event exists
			if (isset($this->events[$event])) {
				$this->execCallbackList($this->events[$event], $args);
				
				return $output;
			}
			else {
				// event not found
				ciwiiLog('Event not found');
			}
		}

		public function addEventListener($event, $callback) {
			$eventContainer = &$this->events;
			
			// check if this event is a wildcard-event
			// if so, change the $eventContainer
			if (strpos($event, '*') !== false) {
				$eventContainer	= &$this->wildcardEvents;
			}
			
			if (!isset($eventContainer[$event])) {
				$eventContainer[$event]	= array();
			}

			$eventContainer[$event][]	= $callback;
		}
		
		public function removeEventListener($event, $callback=null) {
			if (!isset($this->events[$event])) {
				return true;
			}
			
			// check if callback was given
			if ($callback) {
				foreach ($this->events[$event] as $i => $c) {
					if ($c == $callback) {
						unset($this->events[$event][$i]);
						return true;
					}
				}
			}
			// remove every event-listener, if callback was not given
			else {
				unset($this->events[$event]);
			}
			
			return false;
		}
		
		public function getOriginalParameters() {
			return end($this->parameters);
		}
		
		public function getEventProperties() {
			return end($this->eventProperties);
		}
		
		private function removeCurrentParameters() {
			return array_pop($this->parameters);
		}
		private function removeCurrentEventProperties() {
			return array_pop($this->eventProperties);
		}
		
		private function execCallbackList(&$callbackList, &$args=array()) {
			foreach ($callbackList as $callback) {
				// check $callback
				
				$fnWorks	= false;
				
				// check if $callback is a string only
				if (is_string($callback)) {
					$_callback	= preg_replace('/[\r\n]+/', '', $callback);
					
					// seems to be a normal string
					if (strpos($callback, '::') === false) {
						// normal function so check it
						if (!function_exists($callback)) {
							ciwiiLog('Function '.$_callback.' does not exist');
							continue;
						}
					}
					else {
						// static method callback
						$parts	= explode('::', $callback);
						if (!method_exists($parts[0], $parts[1])) {
							ciwiiLog('Method '.$_callback.' does not exist');
							continue;
						}
					}
					
					$fnWorks	= true;
				}
				
				// check if $callback is an array and contains an object
				// and the name of the method
				elseif (is_array($callback)) {
					$avail	= true;
					if (!isset($callback[0]) || !isset($callback[1])) {
						ciwiiLog('Invalid callback set', 2, 'event');
						continue;
					}
					
					if (!method_exists($callback[0], $callback[1])) {
						if (is_string($callback[0])) {
							$name	= implode("::", $callback);
						}
						elseif (is_object($callback[0])) {
							$name = get_class($callback[0]).'::'.$callback[1];
						}
						else {
							$name	= '::'.$callback[1];
						}
						ciwiiLog('Method '.$name.' does not exist', 3, 'event');
						continue;
					}
					
					$fnWorks	= true;
				}
				
				// Run callback, if available
				if ($fnWorks) {
					$output	= call_user_func_array($callback, $args);
				}
				
			}
			
			return $output;
		}
	}
?>
