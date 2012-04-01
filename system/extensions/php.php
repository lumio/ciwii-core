<?php
	class PhpExtension extends C_Extension implements CI_Extension {
		public $extName = 'p';
		
		public function init() {
		}
		
		/**
		 * PHP STR-Replace
		 */
		public function replace($haystack, $needle, $replacement, &$count=null) {
			return str_replace($needle, $replacement, $haystack, $count);
		}
		
		public function replaceCi($haystack, $needle, $replacement, &$count=null) {
			return str_ireplace($needle, $replacement, $haystack, $count);
		}
	}
?>
