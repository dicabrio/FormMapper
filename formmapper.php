<?php
/**
 * FormMapper is a class that maps FormElements to DomainEntities
 *
 * @author robertcabri
 */
abstract class FormMapper {


	/**
	 * @var Form
	 */
	private $oForm;

	/**
	 * @var array
	 */
	private $aConstructedModels = array();

	/**
	 * @var array
	 */
	private $aMappingErrors = array();

	/**
	 * @var array
	 */
	private $aFormElementsToDomainEntitiesMapping = array();

	/**
	 * @param Form $oForm
	 */
	public function __construct(Form $oForm) {
		$this->oForm = $oForm;

		$this->defineFormElementToDomainEntityMapping();
	}

	/**
	 * setup rules for form to domainentities mapping
	 */
	abstract protected function defineFormElementToDomainEntityMapping();

	/**
	 * @param string $sFormElementIdentifier
	 * @param string $sDomainEntity
	 */
	protected function addFormElementToDomainEntityMapping($sFormElementIdentifier, $sDomainEntity) {

		if (!class_exists($sDomainEntity, true)) {
			throw new FormMapperException('The specified domain entity does not exist: '.$sDomainEntity);
		}

		$oReflection = new ReflectionClass($sDomainEntity);
		if (!$oReflection->implementsInterface('DomainEntity')) {
			throw new FormMapperException('Given domain entity is not a valid DomainEntity');
		}

		$this->aFormElementsToDomainEntitiesMapping[$sFormElementIdentifier] = $sDomainEntity;
	}

	/**
	 * @param string $sInputField
	 * @param array $aArguments
	 * @return TextElement
	 */
	//	private function buildInputToModel($sInputField, $aArguments=array()) {
	//
	//		if (!isset($this->aResultModels[$sInputField])) {
	//
	//			try {
	//
	//				if (isset($this->aFormfieldsToModel[$sInputField])) {
	//					$sModelClass = $this->aFormfieldsToModel[$sInputField];
	//				} else {
	//					$sModelClass = self::C_DEFAULT_MODEL;
	//				}
	//
	//				array_unshift($aArguments, $this->getFromReq($sInputField));
	//				$this->aResultModels[$sInputField] = $this->constructModel($sModelClass, $aArguments);
	//			} catch (Exception $e) {
	//				$this->aResultModels[$sInputField] = $this->constructModel(self::C_ERROR_TEXT_MODEL, array($this->getFromReq($sInputField)));
	//				$this->aErrors[$sInputField] = 'error'.$sInputField;
	//			}
	//
	//		}
	//
	//		return $this->aResultModels[$sInputField];
	//	}

	/**
	 *
	 * @param string $sFormElementIdentifier
	 * @param string $sDomainEntity
	 * @return DomainEntity
	 */
	private function constructModelFromFormElement($sFormElementIdentifier, $sDomainEntity) {

		$oFormElement = $this->oForm->getFormElement($sFormElementIdentifier);
		try {
			return $this->constructModel($sDomainEntity, array($oFormElement->getValue()));
		} catch (Exception $e) {

			$oFormElement->notMapped();
			$this->aMappingErrors[$sFormElementIdentifier] = 'error'.$sFormElementIdentifier;

			return null;
		}
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
	//	private function getRequiredModels($sInputFieldString) {
	//		$aFields = array($sInputFieldString);
	//		if (strpos($sInputFieldString, ',')) {
	//			$aFields = explode(',', $sInputFieldString);
	//		}
	//
	//		$aArgument = array();
	//		foreach ($aFields as $sField) {
	//			$aArgument[] = $this->buildInputToModel($sField);
	//		}
	//
	//		return $aArgument;
	//	}

	/**
	 * @throws FormMapperException if there are errors while mapping formelements to domainentities
	 */
	public function constructModelsFromForm() {

		foreach ($this->aFormElementsToDomainEntitiesMapping as $sFormElementIdentifier => $sDomainEntity) {
			$this->aConstructedModels[$sFormElementIdentifier] = $this->constructModelFromFormElement($sFormElementIdentifier, $sDomainEntity);
		}

		if ($this->hasErrors()) {
			throw new FormMapperException('Error while validating data in the models');
		}
	}

	/**
	 * @return boolean
	 */
	private function hasErrors() {
		return (count($this->aMappingErrors) > 0);
	}

	public function getMappingErrors() {
		return $this->aMappingErrors;
	}

	/**
	 * @param string $sFormElementIdentifier
	 * @return DomainEntity
	 */
	public function getModel($sFormElementIdentifier) {
		if (isset($this->aConstructedModels[$sFormElementIdentifier])) {
			return $this->aConstructedModels[$sFormElementIdentifier];
		}

		return null;
	}

}

class FormMapperException extends Exception {}