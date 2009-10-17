<?php
/**
 * 
 */
class Input implements FormElement {

	/**
	 * @var string
	 */
	private $sType;

	/**
	 * @var string
	 */
	private $sName;

	/**
	 * @var string
	 */
	private $sValue;

	/**
	 * @param string $sType
	 * @param string $sName
	 */
	public function __construct($sType, $sName) {
		$this->sName = $sName;
		$this->sType = $sType;
	}

	/**
	 * @param string $sValue
	 */
	public function setValue($sValue) {
		$this->sValue = $sValue;
	}

	/**
	 * @return string
	 */
	public function getValue() {
		return $this->sValue;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->sName;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return '<input type="'.$this->sType.'" name="'.$this->sName.'" value="'.$this->sValue.'" />';
	}
}