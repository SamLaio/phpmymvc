<?php
class LibControl {
	public $InData;
	public $View;
	public $Tools;
	public $FnName;
	function __construct($ObjName=false) {
		$this->View = new LibView;
		$this->Tools = new libTools;

		if(isset($_GET) and count($_GET) != 0){
			$this->InData['get'] = $this->Tools->ValEncode($_GET);
		}
		if(isset($_POST) and count($_POST) != 0){
			$this->InData['post'] = $this->Tools->ValEncode($_POST);
		}
		if($ObjName){
			$this->View->SetUrl($ObjName);
		}
	}

	public function Unfind(){
		// $this->View->SetUrl('Index');
		$this->View->ShowPage(array('head','Unfind','foot'));
	}
}