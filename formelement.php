<?php
/**
 * FormElement
 *
 * @author robertcabri
 */
interface FormElement {

	/**
	 * @return string
	 */
	public function __toString();

	/**
	 * @return string
	 */
	public function getValue();

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param string $sValue
	 */
	public function setValue($sValue);
}
