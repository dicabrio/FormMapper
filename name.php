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
class Name extends DomainText {

	/**
	 * minimum length should be 3 and max lenght should be 30
	 * @param string $sValue
	 */
	public function __construct($sValue=null) {
		parent::__construct($sValue, 3, 30);
	}

}
