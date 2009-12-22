<?php

class ActionButton extends Input {
	
	public function  __construct($sValue) {
		parent::__construct('submit', 'action');
		parent::setValue($sValue);
	}
}