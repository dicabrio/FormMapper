<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of formmapperclass
 *
 * @author robertcabri
 */
abstract class FormMapper {

	const C_DEFAULT_MODEL = 'TextNode';

	/**
	 * @var Form
	 */
	private $oForm;

	private $aResultModels = array();

	private $aErrors = array();

	private $aMappingFormElementsToDomainEntities = array();

	public function __construct(Form $oForm) {
		$this->oForm = $oForm;
	}

	abstract protected function defineFormElementToDomainEntityMapping();

	protected function addFormElementToDomainEntityMapping($sFormElementIdentifier, $sDomainEntity) {
		$this->aMappingFormElementsToDomainEntities[$sFormElementIdentifier] = $sDomainEntity;
	}

	/**
	 * @param string $sInputField
	 * @param array $aArguments
	 * @return TextElement
	 */
	private function buildInputToModel($sInputField, $aArguments=array()) {

		if (!isset($this->aResultModels[$sInputField])) {

			try {

				if (isset($this->aFormfieldsToModel[$sInputField])) {
					$sModelClass = $this->aFormfieldsToModel[$sInputField];
				} else {
					$sModelClass = self::C_DEFAULT_MODEL;
				}

				array_unshift($aArguments, $this->getFromReq($sInputField));
				$this->aResultModels[$sInputField] = $this->constructModel($sModelClass, $aArguments);
			} catch (Exception $e) {
				$this->aResultModels[$sInputField] = $this->constructModel(self::C_ERROR_TEXT_MODEL, array($this->getFromReq($sInputField)));
				$this->aErrors[$sInputField] = 'error'.$sInputField;
			}

		}

		return $this->aResultModels[$sInputField];
	}

	/**
	 *
	 * @param string $sClass
	 * @param array $aArguments
	 */
	private function constructModel($sClass, $aArguments) {
		if (!is_array($aArguments)) {
			trigger_error('$aArguments should be an array');
		}

		$oReflectionClass = new ReflectionClass($sClass);
		return $oReflectionClass->newInstanceArgs($aArguments);
	}

	/**
	 * @param string $sInputFieldString
	 * @return array
	 */
	private function getRequiredModels($sInputFieldString) {
		$aFields = array($sInputFieldString);
		if (strpos($sInputFieldString, ',')) {
			$aFields = explode(',', $sInputFieldString);
		}

		$aArgument = array();
		foreach ($aFields as $sField) {
			$aArgument[] = $this->buildInputToModel($sField);
		}

		return $aArgument;
	}

	/**
	 * @param string $aInputFields
	 */
	public function buildModels($aInputFields) {
		if (!is_array($aInputFields)) {
			throw new InvalidArgumentException('Given parameter is not an array', 1);
		}

		foreach ($aInputFields as $sInputField) {
			$sFieldIdentifier = $sInputField;
			if (strpos($sInputField, ':')) {
				$aModelFields = explode(':', $sInputField);
				$sNeededInput = $sFieldIdentifier = array_shift($aModelFields);
				$aArgumentModels = $this->getRequiredModels(current($aModelFields));
				$this->buildInputToModel($sNeededInput, $aArgumentModels);
			} else {
				$this->buildInputToModel($sInputField);
			}
		}

		if ($this->hasErrors()) {
			throw new FormMapperException('Error while validating data in the models');
		}
	}

	/**
	 * @return boolean
	 */
	private function hasErrors() {
		return (count($this->aErrors) > 0);
	}

	public function getErrors() {
		return $this->aErrors;
	}

	public function getModel($sField) {
		if (isset($this->aResultModels[$sField])) {
			return $this->aResultModels[$sField];
		}

		return null;
	}

}

class FormMapperException extends Exception {}