<?php
class ModelIndex extends LibModel{
	function __construct() {
		parent::__construct();
	}
	public function Account(){
		return $this->Assoc('admin','*');
	}
}