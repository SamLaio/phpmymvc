<?php
class Js{
	private $path = 'lib/js/';

	public function getJs($obj = false) {
		$arr = array(
			'Jquery' => $this->path . "jquery-2.1.3.js",
/*--------------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------------------*/
			'Captcha'=> $this->path . 'captcha.js'
		);
		if($obj and in_array($obj, array_keys($arr))){
			include_once $arr[$obj];
		}else{
			include_once $arr['Jquery'];
		}
	}
}