<?php
if(!isset($_SESSION))
	session_start();
$UrlBase = explode('load.php',$_SERVER['PHP_SELF']);
if(!isset($_SESSION['SiteUrl'])){
	$port = ($_SERVER['SERVER_PORT'] == 80)?'http://':'https://';
	$_SESSION['SiteUrl'] = $port . $_SERVER['HTTP_HOST'] . $UrlBase[0];
}
$url = array();
if(isset($UrlBase[1])){
	if(trim($UrlBase[1],'/') != ''){
		$url = explode('/',trim($UrlBase[1],'/'));
	}
}
// $url = (isset($UrlBase[1]) and trim($UrlBase[1],'/') != '')?explode('/', $UrlBase[1]):array();
// print_r($url);exit;
/*load config*/
include_once "config.php";

/*load other class*/
include_once 'lib/LibControl.php';
include_once 'lib/LibView.php';
include_once 'lib/LibTools.php';
include_once 'lib/LibModel.php';
include_once 'lib/LibCache.php';


// include_once 'lib/LibBoot.php';
LibBoot($url);

/*LibBoot*/

function LibBoot($url) {
	$control;
	$Tools = new LibTools;
	$url[0] = (isset($url[0]))?$url[0]:'Index';
	// print_r($url);
	$SetFunction = '';

	if(
		!isset($_SESSION['ControlArr']) or
		count($_SESSION['ControlArr']) == 0 or
		count($_SESSION['ControlArr']) != count(glob('control/*.php'))
	){
		$Tools->ControlFnCheck();
	}

	/*Set $control*/
	/*
	ex 1:
	http://www.blueseeds.com.tw/[load.php/]AdminIndex =>
	$_SESSION['SiteUrl'] = 'http://www.blueseeds.com.tw';
	$url=array(0=>'AdminIndex');

	ex 2:
	http://www.blueseeds.com.tw/[load.php/]Admin/Index =>
	$_SESSION['SiteUrl'] = 'http://www.blueseeds.com.tw';
	$url=array(0=>'Admin',1=>'Index');

	*/
	if(count($url) == 1){
		if($url[0] != 'Index'){
			$tmp = array();
			for($i = 0; $i < strlen($url[0]); $i++){
				for($j = 65; $j <= 90; $j++){
					if($url[0][$i] == chr($j)){
						$tmp[] = $i;
					}
				}
				if(count($tmp) == 2){
					break;
				}
			}
			if(count($tmp) == 1){
				$url[0] = substr($url[0],$tmp[0]);
			}
			if(count($tmp) == 2){
				$str = $url[0];
				// $url[0] = strtolower(substr($url[0],$tmp[0]));
				$url[0] = substr($str,$tmp[0],$tmp[1]);
				$url[1] = substr($str,$tmp[1]);
				unset($str);
			}
		}
	}
	$url[0][0] = (ord($url[0][0]) > 96)?strtoupper($url[0][0]):$url[0][0];
	if(isset($_SESSION['ControlArr'][$url[0]])){
		$control = $url[0];
	}else{
		$control = 'Index';
		$url[1] = 'Unfind';
	}

	/*Set $SetFunction*/
	$url[1] = (isset($url[1]))?$url[1]:'Index';
	if($url[1] != 'Unfind' and !isset($_SESSION['ControlArr'][$control][$url[1]])){
		if($control != 'Js'){
			$control = 'Index';
			$url[1] = 'Unfind';
		}else{
			$url[2] = $url[1];
			$url[1] = 'getJs';
		}
	}
	$SetFunction = $url[1];

	/*Cache*/
	$Cache = new LibCache;
	$Cache->Check($url);
	$File = $Cache->ret;
	$Cache = null;
	if($File){
		include_once $File;
		exit;
	}
	/*Cache*/

	/*Call $control->$SetFunction()*/
	include_once "control/$control.php";
	$control = new $control;
	$control->Url(implode($url));
	if(isset($url[2])){
		$control->{$SetFunction}($url[2]);
		/* Js(obj) -> getJs('Jquery'); */
	}else{
		$control->{$SetFunction}();
		/* Admin->Index(); */
	}
}