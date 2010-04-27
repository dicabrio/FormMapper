<?php

class FormElementImpl implements FormElement {

	private $attributes;

	private $node;

	private $mapping;

	public function __construct($nodename, $name, $value=null) {

		$this->node = $nodename;
		$this->attributes['name'] = $name;
		$this->attributes['value'] = $value;
		
	}

	/**
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function addAttribute($name, $value) {

		$this->attributes[$name] = $value;
		return $this;

	}

	/**
	 *
	 * @return string
	 */
	public function __toString() {

		$formElement = "<%s %s />";

		$sAttributes = "";
		foreach ($this->attributes as $name => $value) {
			$sAttributes .= sprintf(' %s="%s"', $name, $value);
		}

		return sprintf($formElement, $this->node, $sAttributes);

	}

	private function getAttribute($name) {

		if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
		}
		return '';

	}

	/**
	 *
	 * @return string
	 */
	public function getType() {

		return $this->getAttribute('type');
		
	}

	/**
	 *
	 * @return string
	 */
	public function getValue() {

		return $this->getAttribute('value');

	}

	/**
	 *
	 * @return string
	 */
	public function getName() {

		return $this->getAttribute('name');

	}

	/**
	 * @param string $value
	 */
	public function setValue($value) {

		$this->addAttribute('value', $value);

	}

	/**
	 * behaviour when the value cannot be mapped
	 */
	public function notMapped() {

		$this->addAttribute('style', 'border: 1px solid red;');
		
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

	/**
	 *
	 * @return boolean
	 */
	public function isSelected() {

		return true;
		
	}

}



