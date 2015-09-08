<?php
class LibView {
	private $path = 'view';
	private $PageArr = array();
	private $Tools;
	private $PwPage = false;
	public $JsLoad = array();
	private $Lang;
	public $Url = '';
	function __construct($url=false) {
		if($url){
			$this->SetUrl($url);
		}
	}

	public function SetUrl($url=''){
		$this->path .= '/'.$url;
		$this->Tools = new LibTools;
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
		// echo $this->path;
		// print_r($this->PageArr);exit;
		/*if(is_array($page)){
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

			// foreach(SCANDIR($this->path) as $value){
			// 	if (substr($value, 0, strrpos($value, ".")) == $page){
			// 		$this->PageArr[] = $this->path.'/'.$value;
			// 	}
			// }
			 $this->PageArr = array('head', 'Index'); 
		}*/
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
			if(!$InData){
				ob_start();
			}
			foreach($this->PageArr as $toPage){
				include_once $toPage;
			}
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