<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DomainEntity class
 *
 * @author robertcabri
 */
interface DomainEntity {

	/**
	 * @return string
	 */
	public function __toString();

	/**
	 * @param Object $oElement
	 */
	public function equals($oElement);

}
