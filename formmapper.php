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
	 * @param string $sFormElementName
	 * @param string $sDomainEntity
	 */
	protected function addFormElementToDomainEntityMapping($sFormElementName, $sDomainEntity) {

		if (!class_exists($sDomainEntity, true)) {
			throw new FormMapperException('The specified domain entity does not exist: '.$sDomainEntity);
		}

		$oReflection = new ReflectionClass($sDomainEntity);
		if (!$oReflection->implementsInterface('DomainEntity')) {
			throw new FormMapperException('Given domain entity is not a valid DomainEntity. Formelement:'.$sFormElementName.' to: '.$sDomainEntity);
		}

		$this->aFormElementsToDomainEntitiesMapping[$sFormElementName] = $sDomainEntity;
	}

	/**
	 *
	 * @param string $sFormElementName
	 * @param string $sDomainEntity
	 * @return DomainEntity
	 */
	private function constructModelFromFormElement($sFormElementName, $sDomainEntity) {

		$oFormElement = $this->oForm->getFormElementByName($sFormElementName);
		try {
			return $this->constructModel($sDomainEntity, array($oFormElement->getValue()));
		} catch (Exception $e) {

			$oFormElement->notMapped();
			$this->aMappingErrors[$sFormElementName] = $sFormElementName.'-'.$e->getMessage();

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
	 * @throws FormMapperException if there are errors while mapping formelements to domainentities
	 */
	public function constructModelsFromForm() {

		foreach ($this->aFormElementsToDomainEntitiesMapping as $sFormElementName => $sDomainEntity) {
			$this->aConstructedModels[$sFormElementName] = $this->constructModelFromFormElement($sFormElementName, $sDomainEntity);
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

	public function addMappingError($key, $errormsg) {
		$this->aMappingErrors[$key] = $errormsg;
	}

	/**
	 * get the constructed model. If the model was not constructed because the there was no mapping defined.
	 * It will return the raw value
	 *
	 * @param string $sFormElementIdentifier
	 * @return DomainEntity
	 */
	public function getModel($sFormElementIdentifier) {
		if (isset($this->aConstructedModels[$sFormElementIdentifier])) {
			return $this->aConstructedModels[$sFormElementIdentifier];
		}

		return $this->oForm->getFormElement($sFormElementIdentifier)->getValue();
	}

}

class FormMapperException extends Exception {}