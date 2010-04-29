<?php
/**
 * A basic input field
 */
class Select implements FormElement {

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
	private $options = array();

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

		$options = "";
		foreach ($this->options as $value => $label) {

			$selected = "";
			if ($value == $this->sValue) {
				$selected = 'selected="selected"';
			}

			$options .= sprintf('<option value="%s" %s>%s</option>', $value, $selected, $label);
		}

		$attributes = "";
		foreach ($this->attributes as $attName => $attValue) {
			$attributes .= sprintf(' %s="%s"', $attName, $attValue);
		}

		return sprintf('<select name="%s" %s %s>%s</select>', $this->sName, $attributes, $this->sStyle, $options);
	}

	public function addOption($value, $label) {
		$this->options[$value] = $label;
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
		return 'select';
	}

	/**
	 * Declare the mapping for this form element. If no mapping is define it will return the mapping
	 * defined for this element
	 *
	 * @param string $sModelName
	 */
	public function mapTo($sModelName=null) {

		if ($sModelName === null) {
			return $this->mapping;
		}
		$this->mapping = $sModelName;

	}
}