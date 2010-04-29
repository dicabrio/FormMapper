<?php
/**
 * A basic input field
 */
class Input extends FormElementImpl {

	/**
	 * @param string $sType
	 * @param string $name
	 */
	public function __construct($sType, $name, $value=null) {
		parent::__construct('input', $name, $value);

		parent::addAttribute('type', $sType);
	}
	
}