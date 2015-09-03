<?php
	class LibCaptcha{
		private $img_x, $img_y;
		function __construct($size_x=false,$size_y=false) {
			if(!isset($_SESSION['CaptchaArr'])){
				$_SESSION['CaptchaArr'] = $this->SetArr();
			}
			if($size_x and $size_y){
				$this->img_x = $size_x;
				$this->img_y = $size_y;
			}else{
				$this->img_x = 120;
				$this->img_y = 40;
			}
		}
		public function num2adb($num){
			$text="";
			for($i=0;$i<rand(3,6);$i++){
				$shift = substr($num, $i, 1)+substr($num, $i - strlen($num), 2);
				$text .= $_SESSION['CaptchaArr'][$shift];
			}
			return $text;
		}
		public function CreateImg($authText){
			/*產生圖檔, 及定義顏色*/
			$im = imageCreate($this->img_x, $this->img_y);
			/*ImageColorAllocate 分配圖形的顏色*/
			$back = ImageColorAllocate($im, mt_rand(200,255), mt_rand(200,255), mt_rand(200,255));

			// $authText=$this->num2adb($num);
			imageFill($im, 0, 0, $back);
			/*imageString($im, 5, mt_rand(0,55), mt_rand(0,40), $authText, $font);//把字放上圖片*/
			$str_x = 10;
			$tmp_min = ($str_x == 5)?12:15;
			$tmp_max = ($str_x == 5)?15:20;
			$str_y = 0;
			$FontArr = glob('Font/*.ttf');
				// print_r($FontArr);exit;
			for($i = 0; $i < strlen($authText); $i++){
				$str_size = rand($tmp_min,$tmp_max);
				$str_y = rand(($str_size+5),($this->img_y-10));
				$font = ImageColorAllocate($im, mt_rand(0,100), mt_rand(0,100), mt_rand(0,100));
				/*
				Unuse by Sam 2015/08/20
				imageString($im, rand(1,5), $str_x, $str_y, $authText[$i], $font);
				*/
				imagettftext($im,$str_size,rand(-60,60),$str_x, $str_y, $font, $FontArr[rand(0,count($FontArr))],$authText[$i]);
				$str_x += floor(($this->img_x-10)/strlen($authText));
			}

			/*插入圖形干擾點共 50~200 點*/
			$tmp_min = ($this->img_x>=120)?100:200;
			$tmp_max = ($this->img_x>=120)?200:500;
			for($i = 0; $i < mt_rand($tmp_min,$tmp_max); $i++) {
				$point = ImageColorAllocate($im, mt_rand(0,100), mt_rand(0,100), mt_rand(0,100));
				imagesetpixel($im, mt_rand(0,$this->img_x)  , mt_rand(0,$this->img_y) , $point);
			}
			/*插入圖形干擾線共2~5條*/
			$tmp_min = ($this->img_x>=120)?5:100;
			$tmp_max = ($this->img_x>=120)?10:200;
			for($i = 1; $i<=mt_rand($tmp_min,$tmp_max); $i++){
				$point = ImageColorAllocate($im, mt_rand(0,100), mt_rand(0,100), mt_rand(0,100));
				imageline($im, mt_rand(0,$this->img_x), mt_rand(0,$this->img_y), mt_rand(0,$this->img_x), mt_rand(0,$this->img_y) ,$point);
			}
			header("Content-type: image/PNG");
			ImagePNG($im);
			ImageDestroy($im);
		}
		public function CheckImg($source,$input_code){
			// echo $this->num2adb($source).'--'.$input_code;
			$ret = ($this->num2adb($source)==$input_code);
			unset($_SESSION['CaptchaArr']);
			return $ret;
		}
		private function SetArr(){
			$tmp = array();
			for($i = 1; $i<199; $i++){
				$val = (mt_rand(0,1) == 0)? mt_rand(49,56):mt_rand(65,90);
				$tmp[$i] = chr($val);
			}
			return $tmp;
		}
	}