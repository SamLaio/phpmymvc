<?php
class LibCache extends LibModel{
	public $ret;
	function __construct($url=false) {
		parent::__construct();
		$this->table = 'cache';
	}
	public function Check($url) {
		$url = implode($url);
		$arr = $this->Assoc($this->table,'*',"url='$url'");
		$this->ret = false;
		if($this->count != 0){
			$arr = $arr[0];
			// if($arr['up_date'] == $arr['cache_date']){
			if(file_exists($arr['path'])){
				$this->ret = $arr['path'];
			}else{
				// echo $this->Del($this->table,"`url`='$url'");
				$this->Query($this->Del($this->table,"`url`='$url'"));
			}
			// echo $this->Del($this->table,"url='$url'");
			// exit;
			// }
		}
	}
	public function Save($arr){
		/* $arr = ['url','file']*/
		$this->Query($this->In($this->table,array('url'=>$arr['url'],'path'=>$arr['file'])));
	}
}