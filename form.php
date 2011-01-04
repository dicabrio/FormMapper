<?php

/**
 * The Form
 *
 * @author robertcabri
 */
class Form {

	/**
	 * @var Request
	 */
	private $request;
	/**
	 * @var string
	 */
	private $sFormMethod;
	/**
	 * @var string
	 */
	private $sFormAction;
	/**
	 * @var string
	 */
	private $sFormIdentifier;
	/**
	 * @var string
	 */
	private $sFormEnctype;
	/**
	 * @var array
	 */
	private $aFormElementsByIdentifier = array();
	/**
	 * @var array
	 */
	private $aSubmitButtonsAndHandlers = array();
	/**
	 * @var array
	 */
	private $aFormElementsByName = array();

	/**
	 * @param string $sAction
	 * @param string $sMethod
	 * @param string $sIdentifier
	 */
	public function __construct($sAction, $sMethod='post', $sIdentifier=null) {

		$this->sFormAction = $sAction;
		$this->sFormMethod = $sMethod;
		$this->sFormIdentifier = $sIdentifier;

		$this->defineFormElements();
	}

	/**
	 * In this method you should define the form. This is done to force you adding elements
	 */
	protected function defineFormElements() {

	}

	/**
	 * This method will get a value from a request. It checks if the request object exists
	 * IF not it returns null
	 *
	 * @param string $sRequestKey
	 * @return mixed
	 */
	private function getValueFromRequest(FormElement $formElement) {

		if ($this->isSubmitted()) {
			$formElementName = $formElement->getName();

			if ($formElement->getType() == 'file') {
				return $this->request->files($formElementName);
			} else {
				return $this->request->request($formElementName);
			}
		}
	}

	/**
	 * this method is only allowed to be called in the defineFormElements method
	 *
	 * @param FormElement $oFormElement
	 */
	public function addFormElement(FormElement $oFormElement, FormHandler $handler = null) {

		$sFormElementIdentifier = $oFormElement->getIdentifier();

		if ($oFormElement->getType() == 'file') {
			$this->sFormEnctype = ' enctype="multipart/form-data"';
		}

		if ($this->isSubmitted() && $oFormElement->getType() !== 'submit') {
			$oFormElement->setValue($this->getValueFromRequest($oFormElement));
		}

		$this->aFormElementsByIdentifier[$sFormElementIdentifier] = $oFormElement;
		$this->aFormElementsByName[$oFormElement->getName()][] = $oFormElement;

		if ($handler !== null) {
			$this->aSubmitButtonsAndHandlers[$sFormElementIdentifier] = array('FormElement' => $oFormElement, 'FormHandler' => $handler);
		}
	}

	/**
	 * @param string $sFormElementIdentifier
	 * @return FormElement
	 */
	public function getFormElement($sFormElementIdentifier) {

		if (!isset($this->aFormElementsByIdentifier[$sFormElementIdentifier])) {
			throw new FormException('requested form element is not defined in this form: ' . $sFormElementIdentifier);
		}

		return $this->aFormElementsByIdentifier[$sFormElementIdentifier];
	}

	/**
	 * @return array
	 */
	public function getFormElements() {

		return $this->aFormElementsByIdentifier;
	}

	/**
	 *
	 * @param string $sFormElementName
	 * @return FormElement
	 */
	public function getFormElementByName($sFormElementName) {

		if (!isset($this->aFormElementsByName[$sFormElementName])) {
			throw new FormException('No such elementname defined: ' . $sFormElementName);
		}

		$aElements = $this->aFormElementsByName[$sFormElementName];
		if (!is_array($aElements) || count($aElements) == 0) {
			throw new FormException('No such elements for this elementname defined: ' . $sFormElementName);
		}

		if (count($aElements) == 1) {
			return current($aElements);
		}

		foreach ($aElements as $oFormElement) {
			if ($oFormElement->isSelected()) {
				return $oFormElement;
			}
		}
	}

	public function notMapped($formElementName) {

		if (!isset($this->aFormElementsByName[$formElementName])) {
			throw new FormException('No such elementname defined: ' . $formElementName);
		}

		$aElements = $this->aFormElementsByName[$formElementName];
		foreach ($aElements as $formElement) {
			$formElement->notMapped();
		}

	}

	/**
	 * @return string
	 */
	public function getFormAction() {

		return $this->sFormAction;
	}

	public function addListener($buttonIdentifier, FormHandler $handler) {

		$formElement = $this->getFormElement($buttonIdentifier);
		$this->addSubmitButton($buttonIdentifier, $formElement, $handler);
	}

	/**
	 * @param string $sButtonIdentifier
	 * @param FormElement $oElement
	 * @param FormHandler $oHandler
	 */
	public function addSubmitButton($sButtonIdentifier, FormElement $oElement, FormHandler $oHandler) {

		$this->aSubmitButtonsAndHandlers[$sButtonIdentifier] = array('FormElement' => $oElement, 'FormHandler' => $oHandler);
	}

	/**
	 * @param string $sButtonIdentifier
	 * @return FormElement
	 */
	public function getSubmitButton($sButtonIdentifier) {

		if (isset($this->aSubmitButtonsAndHandlers[$sButtonIdentifier])) {
			return $this->aSubmitButtonsAndHandlers[$sButtonIdentifier]['FormElement'];
		}
		return null;
	}

	/**
	 * Listen if the form is submitted. It will tell the handlers to fire if the right button is pressed
	 */
	public function listen(Request $request) {

		$this->request = $request;

		if ($this->isSubmitted()) {
			$this->populateFormElementsWithRequestData();
		}


		foreach ($this->aSubmitButtonsAndHandlers as $aSingleSubmitButtonAndHandler) {
			$oButton = $aSingleSubmitButtonAndHandler['FormElement'];
			$oHandler = $aSingleSubmitButtonAndHandler['FormHandler'];
			$sValueFromRequest = $this->getValueFromRequest($oButton);
			if ($sValueFromRequest == $oButton->getValue()) {
				$oHandler->handleForm($this);
			}
		}
	}

	/**
	 * @return void
	 */
	private function populateFormElementsWithRequestData() {

		foreach ($this->aFormElementsByIdentifier as $oFormElement) {
			if ($oFormElement->getType() !== 'submit') {
				$oFormElement->setValue($this->getValueFromRequest($oFormElement));
			}
		}
	}

	/**
	 * @return string
	 */
	public function begin() {

		return '<form id="' . $this->sFormIdentifier . '" method="' . $this->sFormMethod . '" action="' . $this->sFormAction . '"' . $this->sFormEnctype . '>';
	}

	/**
	 * @return sring
	 */
	public function end() {

		return '</form>';
	}

	/**
	 * @return string
	 */
	public function getIdentifier() {

		return $this->sFormIdentifier;
	}

	/**
	 * check if this form is submitted
	 * @return Boolean
	 */
	private function isSubmitted() {

		if ($this->request instanceof Request) {
			return ($this->request->method() == Request::POST);
		}

		return false;
	}

}

class FormException extends Exception {

}