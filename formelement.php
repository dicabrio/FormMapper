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
	 * @return string
	 */
	public function getType();

	/**
	 * @param string $sValue
	 */
	public function setValue($sValue);

	/**
	 * @return boolean
	 */
	public function isSelected();

	/**
	 * when this element cannot be mapped this method will be called
	 */
	public function notMapped();

}
