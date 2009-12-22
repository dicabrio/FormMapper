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
class Email extends DomainText {

	/**
	 * minimum length should be 3 and max lenght should be 30
	 * @param string $sValue
	 */
	public function __construct($value=null) {

		if (!preg_match('/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD', (string) $value)) {
			throw new InvalidArgumentException('not-well-formed', 100);
		}

		parent::__construct($value);
	}

}
