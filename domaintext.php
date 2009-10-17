<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of textnodeclass
 *
 * @author robertcabri
 */
class DomainText implements DomainEntity {

	/**
	 * @var string
	 */
	private $sValue;

	/**
	 *
	 * @param string $sValue
	 * @param int $iMinLength
	 * @param int $iMaxLength
	 */
	public function __construct($sValue=null, $iMinLength = null, $iMaxLength=null) {
		if ($iMinLength !== null && mb_strlen($sValue, 'UTF-8') < $iMinLength) {
			throw new InvalidArgumentException('Given string is too short', 1);
		}

		if ($iMaxLength !== null && mb_strlen($sValue, 'UTF-8') > $iMaxLength) {
			throw new InvalidArgumentException('Given string is too long', 1);
		}

		$this->sValue = $sValue;
	}

	public function getValue() {
		return $this->sValue;
	}

	public function __toString() {
		return $this->sValue;
	}

	public function equals($oObject) {
		if (!is_object($oObject)) {
			return false;
		}

		if (get_class($this) !== get_class($oObject)) {
			return false;
		}

		return ($oObject->sValue === $this->sValue);

	}
}
