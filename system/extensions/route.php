<?php
	class RouteExtension extends C_Extension implements CI_Extension {
		public $extName = 'route';
		
		public function init() {
		}
		
		public function getRoute($env) {
			$routeType	= strtoupper($this->config('system.uri', 'AUTO'));
			$seperator	= '/';
			
			// if needed, find out, what way to go
			if ($routeType == 'AUTO') {
				if ($env == 'cli')
					$routeType	= 'ARGV';
				elseif (isset($_SERVER['PATH_INFO']))
					$routeType	= 'PATH_INFO';
				else
					$routeType	= 'QUERY_STRING';
			}
			
			$routeRaw	= '';
			if (isset($_SERVER[$routeType])) {
				if (strtolower($routeType) == 'argv') {
					if (!isset($_SERVER['argv']) || !isset($_SERVER['argv'][1])) {
						$routeRaw = trim($_SERVER['argv'][1], $seperator);
					}
				}
				else {
					$routeRaw	= trim($_SERVER[$routeType], $seperator);
				}
			}
			
			// no route was given, so use default one
			if (empty($routeRaw)) {
				$routeRaw	= instance()->config->get('route.default', 'main');
			}
			
			$parts	= explode($seperator, $routeRaw);
		
			// create $route-array
			$route					= array();
			$route['controller']	= strtolower(array_shift($parts));
			$route['method']		= isset($parts[0]) ? array_shift($parts):'index';
			$route['arguments']		= isset($parts[0]) ? $parts:array();
			
			return $route;
		}
	}
?>
