<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of formelement
 *
 * @author robertcabri
 */
interface FormElement {
    
	public function __toString();

	public function getValue();

	public function getName();

	public function setValue($sValue);
}
