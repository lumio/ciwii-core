<?php
	class OutputExtension extends C_Extension implements CI_Extension {
		public $extName = 'output';
		
		// Either if the Output-Extension is enabled or not
		// Set by enable and disable
		private $enabled		= true;
		
		// Render page on event?
		// Set through enableOnEvent and disableOnEvent
		private $renderOnEvent	= true;
		
		// Title
		// Set with setTitle
		private $title			= '';
		
		// Title-Prefix
		// Set with setTitlePrefix
		private $titlePrefix	= '';
		
		// Title-Suffix
		// Set with setTitleSuffix
		private $titleSuffix	= '';
		
		// Format of output
		// * markup		wrap output in markup-scaffold
		// * raw		direct output
		// Set with setFormat
		private $format			= '';
		
		// Specific format of markup
		// * html4-strict
		// * html4-transitional
		// * xhtml1-strict
		// * xhtml1-transitional
		// * xhtml1-frameset
		// * xhtml1.1-dtd
		// * xhtml1.1-basic
		// * html5
		// Set with setDoctype
		private $doctype		= '';
		
		// Either if the doctype is xml or not
		// This property gets automatically set when using setDoctype
		private $xml			= false;
		
		// Contains messages
		private $debugMessages	= array();
		private $infoMessages	= array();
		private $warnMessages	= array();
		private $errorMessages	= array();
		
		/**
		 *
		 */
		public function init() {
			// Check if Output should be enabled
			if ($this->config('output.enabled', true)) {
				$this->enable();
			}
			// or disabled
			else {
				$this->disable();
			}
			
			// Check if rendering on event should be enabled
			// So if the event system.output is called, the
			// renderOutputOnEvent-method is called
			if ($this->config('output.onevent', true)) {
				$this->enableOnEvent();
			}
			// or disabled
			else {
				$this->disableOnEvent();
			}
			
			// set default values
			$this->setTitle($this->config('application.name', ''));
			$this->setTitlePrefix($this->config('output.title.prefix', ''));
			$this->setTitleSuffix($this->config('output.title.suffix', ''));
			
			$this->setFormat($this->config('output.format', 'markup'));
			$this->setDoctype($this->config('output.doctype', 'html5'));
			
			$this->addEventListener('system.output', 'renderOutputOnEvent');
		}
		
		/**
		 *
		 */
		public function enable() {
			if (!$this->enabled) {
				ob_start();
			}
			$this->globalMessageRedirect	= array('extension', 'output', 'addMessage');
			$this->enabled					= true;
		}
		
		/**
		 *
		 */
		public function disable() {
			if ($this->enabled) {
				@ob_end_clean();
			}
			$this->globalMessageRedirect	= false;
			$this->enabled					= false;
		}
		
		/**
		 *
		 */
		public function enableOnEvent() {
			$this->renderOnEvent	= true;
		}
		
		/**
		 *
		 */
		public function disableOnEvent() {
			$this->renderOnEvent	= false;
		}
		
		/**
		 *
		 */
		public function render($input=array()) {
			var_dump($input);
		}
		
		/**
		 * Renders given information to page
		 * @param array $overwrite		Overwriting variables
		 * @param string $file			Save result to file
		 * @return boolean
		 */
		public function renderOutput($overwrite=array(), $file='') {
			if (!$this->enabled) {
				return false;
			}
		}
		
		/**
		 * To have better adaptability the event goes to an extra
		 * method called renderOutputOnEvent. For disabling the
		 * event, just call disableOnEvent.
		 * @return boolean
		 */
		public function renderOutputOnEvent() {
			if (!$this->renderOnEvent) {
				return false;
			}
			
			return $this->renderOutput();
		}
		
		/**
		 * Sets output-format
		 * @param string $format		Contains either markup or raw
		 * @return boolean				Returns true on success
		 */
		public function setFormat($format) {
			// Define allowed formats
			$allowed	= array(
				'markup',
				'raw'
			);
			
			// Check if given format is allowed
			if (!in_array($format, $allowed)) {
				exitError('Output-format '.$format.' not allowed');
			}
			
			// Set new format and return that we have succeeded
			$this->format = $format;
			return true;
		}
		
		/**
		 * Sets doctype. This function is relevant, if output-format is set
		 * to markup.
		 * @param string $doctype
		 * @return boolean				Returns true on success
		 */
		public function setDoctype($doctype) {
			// Define allowed doctypes
			$allowed	= array(
				'html4-strict',
				'html4-transitional',
				'xhtml1-strict',
				'xhtml1-transitional',
				'xhtml1-frameset',
				'xhtml1.1-dtd',
				'xhtml1.1-basic',
				'html5'
			);
			
			// Define xml-doctypes
			$xml		= array(
				'xhtml1-strict'			=> true,
				'xhtml1-transitional'	=> true,
				'xhtml1-frameset'		=> true,
				'xhtml1.1-dtd'			=> true,
				'xhtml1.1-basic'		=> true
			);
			
			// Check if given doctype is allowed
			$success	= true;
			if (!in_array($doctype, $allowed)) {
				$this->addMessage('Doctype '.$doctype.' is unknown.', 2);
				$doctype	= $this->config('output.doctype', 'html5');
				$success	= false;
			}
			
			// Set new doctype and if it is either an xml-format
			// or not
			$this->doctype	= $doctype;
			$this->xml		= isset($xml[$doctype]);
			
			return $success;
		}
		
		/**
		 *
		 */
		public function setTitle($str) {
			$this->title		= $str;
		}
		
		/**
		 *
		 */
		public function setTitlePrefix($str) {
			$this->titlePrefix	= $str;
		}
		
		/**
		 *
		 */
		public function setTitleSuffix($str) {
			$this->titleSuffix	= $str;
		}
		
		/**
		 * Adds message to array
		 * @param string	$str	Contains message
		 * @param integer	$level	Message-Level
		 *								0 = Debug
		 *								1 = Information
		 *								2 = Warning
		 *								3 = Error
		 */
		public function addMessage($str, $level=0) {
			switch($level) {
				case 0:
					$this->debugMessages[]	= $str;
					break;
				case 1:
					$this->infoMessages[]	= $str;
					break;
				case 2:
					$this->warnMessages[]	= $str;
					break;
				case 3:
					@ob_end_clean();
					die($str);
					break;
			}
			
			return true;
		}
		
	}
?>
