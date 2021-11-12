<?php

namespace App\CustomClasses;

class Gender{
	public $value = '';
	public function __construct($value){
		$this->value = $value;

	}	
	public function format($format = null){
		return __('global.'.$this->value);
	}
}