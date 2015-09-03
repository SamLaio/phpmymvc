<?php
class Captcha extends LibControl{
	private $LibCaptcha;
	function __construct() {
		parent::__construct();
		include_once 'lib/LibCaptcha.php';
		$this->SetSize();
	}
	function __destruct(){
		$this->LibCaptcha = null;
	}

	private function SetSize(){
		if(
			isset($this->InData['get']['img_y']) and is_numeric($this->InData['get']['img_y']) and
			isset($this->InData['get']['img_x']) and is_numeric($this->InData['get']['img_x'])
		){
			$this->LibCaptcha = new LibCaptcha($this->InData['get']['img_x'],$this->InData['get']['img_y']);
		}else{
			$this->LibCaptcha = new LibCaptcha;
		}
	}
	public function ImgPut(){
		unset($_SESSION['CatptchaError']);
		$_SESSION['CaptchaPw'] = mt_rand(50,500000);

		$this->LibCaptcha->CreateImg($this->LibCaptcha->num2adb($_SESSION['CaptchaPw']));
	}
	public function ImgCheck(){
		if(isset($this->InData['post']['captcha'])){
			$key = $this->InData['post']['captcha'];
			if(isset($_SESSION['CatptchaError'])){
				if($_SESSION['CatptchaError'] >= 3){
					echo -1;
					exit;
				}
			}
			if(isset($_SESSION['CaptchaPw'])){
				if($this->LibCaptcha->CheckImg($_SESSION['CaptchaPw'],strtoupper($key))){
					echo 1;
					unset($_SESSION['CatptchaError']);
				}else{
					echo 0;
					if(isset($_SESSION['CatptchaError'])){
						$_SESSION['CatptchaError']++;
					}else{
						$_SESSION['CatptchaError'] = 1;
					}
				}
			}else{
				echo 0;
			}
		}else{
			echo 0;
		}
	}
}
?>