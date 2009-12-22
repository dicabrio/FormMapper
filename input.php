<?php
/**
 * A basic input field
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
	 * @var string
	 */
	private $sStyle;

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
		return '<input type="'.$this->sType.'" name="'.$this->sName.'" value="'.$this->sValue.'"'.$this->sStyle.' />';
	}

	/**
	 * @param string $attribute
	 * @param string $value
	 */
	public function addAttribute($attribute, $value) {
		$this->attributes[$attribute] = $value;
	}

	/**
	 * @return void
	 */
	public function notMapped() {
		$this->sStyle = ' style="border: 1px solid red;"';
	}

	public function isSelected() {
		return true;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->sType;
	}
}