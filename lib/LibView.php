<?php
class LibView {
	private $path = 'view';
	private $PageArr = array();
	private $Tools;
	private $PwPage = false;
	public $JsLoad = array();
	private $Lang;
	function __construct($url=false) {
		if($url){
			$this->SetUrl($url);
		}
		// include_once 'control/Lang.php';
		// $this->Lang = new Lang;
		// $this->Lang = $this->Lang->GetFace();
		// print_r($this->Lang);
	}

	public function SetUrl($url){
		$this->path .= '/'.$url;
		$this->Tools = new LibTools;
	}

	public function SetPage($page = 'index'){
		if(is_array($page)){
			foreach($page as $val){
				if($val == 'head'){
					$this->SetOther($val);
				}
				if($val != 'foot' and $val != 'head'){
					$this->SetPage($val);
				}
				if($val == 'foot'){
					$this->SetOther($val);
				}
			}
		}else{
			foreach(SCANDIR($this->path) as $value){
				if (substr($value, 0, strrpos($value, ".")) == $page){
					$this->PageArr[] = $this->path.'/'.$value;
				}
			}
			/* $this->PageArr = array('head', 'Index'); */
		}
	}

	public function SetOther($page){
		// print_r(SCANDIR($this->path));
		$ck = $this->Tools->FileCk(SCANDIR($this->path),$page);
		if($ck != 'error'){
			$this->PageArr[] = "$this->path/$page.html";
		}else{
			$this->PageArr[] = "view/$page.html";
		}
	}

	public function ShowPage($page = false,$InData=false){
		if($page){
			$this->SetPage($page);
		}
		if(count($this->PageArr) != 0){
			foreach($this->PageArr as $toPage){
				if(!$this->PwPage){
					$this->getBody($toPage);
				}
			}
			if($this->PwPage){
				$this->Tools->PwEnCode($this->PwPage);
			}
			foreach($this->PageArr as $toPage){
				include_once $toPage;
			}
		}
	}

	private function getBody($filename){
		if(file_exists($filename)){
			$file = fopen($filename, "r");
			if($file != NULL){
				while (!feof($file)) {
					if(stristr (fgets($file),'password')){
						$this->PwPage = true;
					}
				}
				fclose($file);
			}
		}
	}
}