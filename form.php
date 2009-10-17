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

	private $aFormElements = array();

	private $aSubmitButtons = array();

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

	abstract protected function defineFormElements();

	private function getValueFromRequest($sElementIdentifier) {
		if ($this->sMethod == 'post') {
			return $this->oReq->post($sElementIdentifier);
		}

		return $this->oReq->get($sElementIdentifier);
	}

	/**
	 * this method is only allowed to be called in the defineFormElements method
	 * 
	 * @param FormElement $oElement
	 */
	protected function addFormElement(FormElement $oElement) {

		$sElementName = $oElement->getName();
		$oElement->setValue($this->getValueFromRequest($sElementName));
		$this->aFormElements[$oElement->getName()] = $oElement;
		
	}

	protected function addSubmitButton($sIdentifier, FormElement $oElement, FormHandler $oHandler) {
		$this->aSubmitButtons[$sIdentifier] = array('FormElement' => $oElement, 'FormHandler' => $oHandler);
	}

	/**
	 * @param string $sElementIdentifier
	 * @return FormElement
	 */
	public function getFormElement($sElementIdentifier) {
		if (!isset($this->aFormElements[$sElementIdentifier])) {
			throw new FormException('requested form element is not defined in this form: '.$sElementIdentifier);
		}

		return $this->aFormElements[$sElementIdentifier];
	}

	public function getSubmitButton($sButtonIentifier) {
		if (isset($this->aSubmitButtons[$sButtonIentifier])) {
			return $this->aSubmitButtons[$sButtonIentifier]['FormElement'];
		}
		return null;
	}

	public function getAction() {
		return $this->sAction;
	}

	public function getMethod() {
		return $this->sMethod;
	}

	public function getIdentifier() {
		return $this->sIdentifier;
	}

	public function listen() {

		foreach ($this->aSubmitButtons as $aSubmitButtonAndHandler) {
			$oButton = $aSubmitButtonAndHandler['FormElement'];
			$oHandler = $aSubmitButtonAndHandler['FormHandler'];

			$sValueFromRequest = $this->getValueFromRequest($oButton->getName());

			if ($sValueFromRequest == $oButton->getValue()) {
				$oHandler->handleForm($this);
			}
		}


	}

	public function begin() {
		return '<form id="'.$this->sIdentifier.'" method="'.$this->sMethod.'" action="'.$this->sAction.'">';
	}

	public function end() {
		return '</form>';
	}
}


class FormException extends Exception {}