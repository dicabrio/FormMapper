<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of form
 *
 * @author robertcabri
 */
abstract class Form {

	/**
	 * @var Request
	 */
	private $oReq;

	private $sMethod;

	private $sAction;

	private $sIdentifier;

	private $aFormElementsByIdentifier = array();

	private $aSubmitButtonsAndHandlers = array();

	private $aFormElementsByName = array();

	/**
	 * @param Request $oReq
	 * @param string $sAction
	 * @param string $sMethod
	 * @param string $sIdentifier
	 */
	public function __construct(Request $oReq, $sAction, $sMethod='post', $sIdentifier=null) {
		$this->oReq = $oReq;
		$this->sAction = $sAction;
		$this->sMethod = $sMethod;
		$this->sIdentifier = $sIdentifier;

		$this->defineFormElements();
	}

	/**
	 * In this method you should define the form. This is done to force you adding elements
	 */
	abstract protected function defineFormElements();

	/**
	 * @param string $sRequestKey
	 * @return mixed
	 */
	private function getValueFromRequest($sRequestKey) {
		if ($this->sMethod == 'post') {
			return $this->oReq->post($sRequestKey);
		}

		return $this->oReq->get($sRequestKey);
	}

	/**
	 * this method is only allowed to be called in the defineFormElements method
	 *
	 * @param string $sIdentifier
	 * @param FormElement $oFormElement
	 */
	protected function addFormElement($sIdentifier, FormElement $oFormElement) {

		$sFormElementName = $oFormElement->getName();
		$this->aFormElementsByIdentifier[$sIdentifier] = $oFormElement;
		$this->aFormElementsByName[$sFormElementName][] = $oFormElement;
		
	}

	/**
	 * @param string $sFormElementIdentifier
	 * @return FormElement
	 */
	public function getFormElement($sFormElementIdentifier) {
		if (!isset($this->aFormElementsByIdentifier[$sFormElementIdentifier])) {
			throw new FormException('requested form element is not defined in this form: '.$sFormElementIdentifier);
		}

		return $this->aFormElementsByIdentifier[$sFormElementIdentifier];
	}

	/**
	 *
	 * @param string $sFormElementName
	 * @return FormElement
	 */
	public function getFormElementByName($sFormElementName) {
		if (!isset($this->aFormElementsByName[$sFormElementName])) {
			throw new FormException('No such elementname defined: '.$sFormElementName);
		}

		$aElements = $this->aFormElementsByName[$sFormElementName];
		if (!is_array($aElements) || count($aElements) == 0) {
			throw new FormException('No such elements for this elementname defined: '.$sFormElementName);
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
	public function listen() {
		foreach ($this->aSubmitButtonsAndHandlers as $aSingleSubmitButtonAndHandler) {
			$oButton = $aSingleSubmitButtonAndHandler['FormElement'];
			$oHandler = $aSingleSubmitButtonAndHandler['FormHandler'];

			$sValueFromRequest = $this->getValueFromRequest($oButton->getName());

			if ($sValueFromRequest == $oButton->getValue()) {
				$this->populateFormElementsWithRequestData();
				$oHandler->handleForm($this);
			}
		}
	}

	/**
	 * @return void
	 */
	private function populateFormElementsWithRequestData() {
		foreach ($this->aFormElementsByIdentifier as $oFormElement) {
			$oFormElement->setValue($this->getValueFromRequest($oFormElement->getName()));
		}
	}

	/**
	 * @return string
	 */
	public function begin() {
		return '<form id="'.$this->sIdentifier.'" method="'.$this->sMethod.'" action="'.$this->sAction.'">';
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
		return $this->sIdentifier;
	}
}


class FormException extends Exception {}