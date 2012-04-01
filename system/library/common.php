<?php
	// magical clean global variables
	if (get_magic_quotes_gpc()) {
		$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
		while (list($key, $val) = each($process)) {
			foreach ($val as $k => $v) {
				unset($process[$key][$k]);
				if (is_array($v)) {
					$process[$key][stripslashes($k)] = $v;
					$process[] = &$process[$key][stripslashes($k)];
				}
				else {
					$process[$key][stripslashes($k)] = stripslashes($v);
				}
			}
		}
		unset($process);
	}
	
	/**
	 * Log messages and set leven
	 * @param mixed $log			Contains value to log
	 * @param integer $level		How serious is the log level
	 *								0: Debug
	 *								1: Information
	 *								2: Warning
	 *								3: Error (calls exitError)
	 * @return void
	 */
	function ciwiiLog($log, $level=0, $backtrace='') {
		$debug		= false;
		$debugInfo	= array();
		if (defined('CIWII_DEBUG')) {
			$debug = (bool)CIWII_DEBUG;
		}
		
		if (is_string($log) || is_numeric($log)) {
			$value = $log;
		}
		else {
			$value = print_r($log, true);
		}
		
		if (!empty($backtrace) && $debug) {
			$backtraceArray = debug_backtrace();
			
			$index	= 0;
			switch ($backtrace) {
				case 'event':
					$sysLen = strlen(C_SYSTEM);
					foreach ($backtraceArray as $frame) {
						if (substr($frame['file'], 0, $sysLen) == C_SYSTEM) {
							continue;
						}
						
						$debugInfo = $frame;
						break;
					}
					break;
				default:
					$debugInfo = $backtraceArray;
					break;
			}
		}
		
		switch ($level) {
			case 0:
			case 1:
			case 2:
				
				break;
			case 3:
				exitError($value, $debugInfo);
				break;
		}
	}
	
	function exitError($str, $debug=array()) {
		if (!defined('CIWII_DEBUG') || (bool)CIWII_DEBUG == false) {
			$debug = array();
		}
		
		if (isset(instance()->globals['globalMessageRedirect']) && is_array(instance()->globals['globalMessageRedirect']) && !isset(instance()->tmp['exitErrorRedirect'])) {
			$redirect	= instance()->globals['globalMessageRedirect'];
			instance()->tmp['exitErrorRedirect'] = true;
			instance()->system->callMethod($redirect, array($str, 3, $debug));
			exit;
		}
		
		echo '<br />'.$str;
		if (isset($debug['file']) && isset($debug['line'])) {
			echo '<br />Error in file <b>'.htmlspecialchars($debug['file']).'</b> on line <b>'.htmlspecialchars($debug['line']).'</b>';
		}
		exit;
	}
	
	function sendError($str) {
		if (php_sapi_name() != 'cli') {
			return;
		}
		
		fwrite(STDERR, $str);
	}
	
	function instance() {
		return @C_MAIN_Container::instance();
	}
?>
