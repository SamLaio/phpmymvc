<?php
class Index extends LibControl{
	private $db;
	function __construct() {
		parent::__construct(get_class($this));
		// $this->View->SetUrl();
		include_once 'model/Index.php';
		$this->db = new ModelIndex;
	}
	public function Index(){
		// print_r($this->db->Account());
		$this->View->ShowPage(['head','Index','foot']);
	}
}