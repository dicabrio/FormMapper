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
class ShortText extends DomainText {

	/**
	 *
	 * @param string $sValue
	 */
	public function __construct($sValue=null, $iMinLength = null, $iMaxLength=null) {
		parent::__construct($sValue, null, 5);
	}

}
