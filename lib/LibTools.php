<?php
class LibTools {
	/*尋找檔案是否在資料夾中($isFile是指是不是單純只有指檔案)*/
	public function FileCk($FileArr, $FileName, $isFile = true){
		$ret = 'error';
		foreach($FileArr as $value){
			if (substr($value, 0, strrpos($value, ".")) == $FileName and $isFile){
				$ret = substr($value, 0, strrpos($value, "."));
			}
			if($value == $FileName and !$isFile){
				$ret = $value;
			}
		}
		return $ret;
	}

	public function ControlFnCheck(){
		$_SESSION['ControlArr'] = array();
		$Obj = glob('control/*.php');
		foreach($Obj as $ObjV){
			include_once $ObjV;
			$ObjV = str_replace('control/', '', $ObjV);
			$ObjV = explode('.php', $ObjV);
			foreach (get_class_methods($ObjV[0]) as $FunctionName) {
				if($FunctionName != '__construct' and $FunctionName != '__destruct'){
					$_SESSION['ControlArr'][$ObjV[0]][$FunctionName] = true;
				}
			}
		}
	}

	public function ValEncode($Indata = false) {
		if($Indata){
			if(is_array($Indata)) {
				foreach ($Indata as $key => $value){
					$Indata[$key] = $this->ValEncode($value);
				}
			}else{
				/*
				Check Password move in this function from ValEncode.
				add by SamLaio 2015/05/19
				*/
				if(isset($_SESSION['PwHead']) and stristr($Indata,$_SESSION['PwHead'])){
					$Indata = $this->PwDeCode(str_replace($_SESSION['PwHead'],'',$Indata));
				}
				/*
				Check Password move in this function from ValEncode.
				add by SamLaio 2015/05/19
				*/
				$Indata = str_replace(array("&",'union', "'", '"', "<", ">",'-'), array('@&5','@&7' , '@&1', '@&2', '@&3', '@&4','@&6'), $Indata);
			}
			return $Indata;
		}
	}

	public function ValDecode($arr){
		if(is_array($arr)){
			foreach($arr as $key2 => $value2){
				$arr[$key2] = $this->ValDecode($value2);
			}
		}else{
			$arr = str_replace(array('@&4', '@&3', '@&2', '@&1', '@&5','@&6','@&7'), array(">", "<", '"', "'", "&",'-','union'), stripslashes($arr));
		}
		return $arr;
	}

	public function PwDeCode($str){
		$tmp = '';
		$arr = $_SESSION['PwEnCode'];
		$str = explode('*|*', $str);
		foreach($str as $val){
			foreach($arr as $arr_v){
				if(urldecode($arr_v['val']) == urldecode($val)){
					$tmp .= urldecode($arr_v['id']);
				}
			}
		}
		return $tmp;
	}

	public function PwEnCode($set = false){
		if($set){
			$re_arr = array();
			for($i = 33; $i <=126; $i++){
				$t = urlencode(chr(mt_rand(33,126)));
				if(!in_array($t,array_values($re_arr))){
					$re_arr[urlencode(chr($i))] = $t;
				}else{
					$i--;
				}
			}
			$tmp=array();
			foreach($re_arr as $key => $value){
				$tmp[] = array('id'=>$key, 'val'=>$value);
			}
			unset($re_arr);
			$_SESSION['PwEnCode']=$tmp;
			$tmp = array();
			$_SESSION['PwHead'] = mt_rand(3,5);
			for($i = 1; $i<= $_SESSION['PwHead'];$i++){
				$tmp[] = chr(mt_rand(65,90));
			}
			$_SESSION['PwHead'] = implode($tmp).'::';
		}
	}

	public function Json2Array($json) {
		$ret = array();
		if(count($json) != 0){
			foreach($json as $key => $val){
				$ret[$key] = (is_object($val) or is_array($val))?$this->Json2Array($val):$val;
			}
		}
		return $ret;
	}

	public function Array2Json($arr){
		if(is_array($arr)){
			$ret = array();
			foreach($arr as $key => $val){
				if(is_array($val)){
					$ret[$key] = $this->Array2Json($val);
				}else{
					$tmp = '';
					if(gettype($val)== 'string'){
						$tmp = '"'.$val.'"';
					}else{
						$tmp = $val;
					}
					$ret[$key] = $tmp;
				}
			}
			$tmp = array();
			foreach($ret as $key => $val){
				$tmp[] = '"'.$key.'"'.':'.$val;
			}
			return '{'.implode(',', $tmp).'}';
		}
	}

	public function SetToDb($arr){
		return rawurlencode($this->ValEncode($this->Array2Json($arr)));
	}
}