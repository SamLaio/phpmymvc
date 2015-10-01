<?php
class LibView {
	private $path;
	private $PageArr;
	private $Tools;
	private $PwPage;
	public $JsLoad;
	private $Lang;
	public $Url;
	function __construct($url=false) {
		$this->path = 'view';
		$this->PageArr = array();
		$this->PwPage = false;
		$this->JsLoad = array();
		$this->Lang;
		$this->Url = '';
		$this->Tools = new LibTools;
	}

	public function SetUrl($url=''){
		$this->path .= '/'.$url;
	}

	public function SetPage($page = 'Index'){
		if(is_array($page)){
			foreach($page as $val){
				if($val != 'head' and $val != 'foot'){
					if(file_exists($this->path.'/'.$val.".html")){
						$this->PageArr[] = $this->path.'/'.$val.".html";
					}
				}else{
					if(file_exists($this->path.'/'.$val.".html")){
						$this->PageArr[] = $this->path.'/'.$val.".html";
					}else{
						$this->PageArr[] = 'view/'.$val.".html";
					}
				}
			}
		}else{
			if(file_exists($this->path.'/'.$val.".html")){
				$this->PageArr[] = $this->path.'/'.$val.".html";
			}
		}
	}

	public function ShowPage($page = false,$InData = false){
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
			if(Cacahe == true){
				if(!$InData){
					ob_start();
				}
			}
			foreach($this->PageArr as $toPage){
				include_once $toPage;
			}
			if(Cacahe == true){
				if(!$InData){
					$file = 'cache/'.md5(date('Y-m-d-H-i-s').$this->Url);
					$fp = fopen($file,"w");
					fwrite($fp,ob_get_contents());
					fclose($fp);
					ob_end_flush();

					$Cache = new LibCache;
					$Cache->Save(['url'=>$this->Url,'file'=>$file]);
				}
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