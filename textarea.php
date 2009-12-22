<?php
/**
 * A basic input field
 */
class TextArea implements FormElement {

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
	 * @var array
	 */
	private $attributes = array();

	/**
	 * @param string $sType
	 * @param string $sName
	 */
	public function __construct($sName) {
		$this->sName = $sName;
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
		try {
			$sAttributes = "";
			foreach ($this->attributes as $name => $value) {
				$sAttributes .= sprintf(' %s="%s"', $name, $value);
			}

			return sprintf('<textarea id="%s" name="%s" %s %s>%s</textarea>', $this->sName, $this->sName, $this->sStyle, $sAttributes, $this->sValue);
		} catch (Exception $e) {
			return (string)$e->getMessage();
		}
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
		return 'textarea';
	}
}