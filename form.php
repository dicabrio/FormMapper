<?php
/**
 * The Form
 *
 * @author robertcabri
 */
abstract class Form {

	/**
	 * @var Request
	 */
	private $oRequest;

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
	 * @param Request $oReq
	 * @param string $sAction
	 * @param string $sMethod
	 * @param string $sIdentifier
	 */
	public function __construct(Request $oReq, $sAction, $sMethod='post', $sIdentifier=null) {
		
		$this->oRequest = $oReq;
		$this->sFormAction = $sAction;
		$this->sFormMethod = $sMethod;
		$this->sFormIdentifier = $sIdentifier;

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
		
		if ($this->sFormMethod == 'post') {
			return $this->oRequest->post($sRequestKey);
		}

		return $this->oRequest->get($sRequestKey);
	}

	/**
	 * this method is only allowed to be called in the defineFormElements method
	 *
	 * @param string $sIdentifier
	 * @param FormElement $oFormElement
	 */
	protected function addFormElement($sIdentifier, FormElement $oFormElement) {

		$sFormElementName = $oFormElement->getName();

		if ($oFormElement->getType() == 'file') {
			$this->sFormEnctype = ' enctype="multipart/form-data"';
		}

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
		
		return '<form id="'.$this->sFormIdentifier.'" method="'.$this->sFormMethod.'" action="'.$this->sFormAction.'"'.$this->sFormEnctype.'>';
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
}


class FormException extends Exception {}