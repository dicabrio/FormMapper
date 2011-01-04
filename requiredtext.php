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
class RequiredText extends DomainText {


	/**
	 *
	 * @param string $sValue
	 * @param int $iMinLength
	 * @param int $iMaxLength
	 */
	public function __construct($sValue=null) {

		if (empty($sValue)) {
			throw new InvalidArgumentException('value-not-given', 10);
		}
		
		parent::__construct($sValue);

	}
}
