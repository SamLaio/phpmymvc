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
/*load config*/
include_once "config.php";

/*load other class*/
include_once 'lib/LibControl.php';
include_once 'lib/LibView.php';
include_once 'lib/LibTools.php';
include_once 'lib/LibModel.php';
include_once 'lib/LibCache.php';

/*LibBoot*/
$Tools = new LibTools;
$url[0] = (isset($url[0]))?$url[0]:'Index';

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
		$tmp = [];
		for($i = 0; $i < strlen($url[0]); $i++){
			for($j = 65; $j <= 90; $j++){
				if($url[0][$i] == chr($j)){
					$tmp[] = $i;
				}
			}
			if(count($tmp) == 2){
				$str = $url[0];
				$url[0] = substr($str,$tmp[0],$tmp[1]);
				$url[1] = substr($str,$tmp[1]);
				unset($str);
				break;
			}
		}
		unset($tmp);
	}
}
$url[0][0] = (ord($url[0][0]) > 96)?strtoupper($url[0][0]):$url[0][0];
if(!isset($_SESSION['ControlArr'][$url[0]])){
	$url[0] = 'Index';
	$url[1] = 'Unfind';
}

/*Set $SetFunction*/
if($url[0] == 'Js'){
	if(isset($url[1])){
		$url[2] = $url[1];
	}
	$url[1] = 'getJs';
}else{
	$url[1] = (isset($url[1]))?$url[1]:'Index';
	if($url[1] != 'Unfind' and !isset($_SESSION['ControlArr'][$url[0]][$url[1]])){
		unset($url);
		$url[0] = 'Index';
		$url[1] = 'Unfind';
	}
}
$control = $url[0];
$SetFunction = $url[1];

/*Cache*/
if(Cache == true){
	$Cache = new LibCache;
	$Cache->Check($url);
	$File = $Cache->ret;
	$Cache = null;
	if($File){
		include_once $File;
		exit;
	}
}
/*Cache*/

/*Call $control->$SetFunction()*/
include_once "control/$control.php";
$control = new $control;
if(isset($control->View)){
	$control->View->Url = $url[0].$url[1];
}
if(isset($url[2])){
	$control->{$SetFunction}($url[2]);
}else{
	$control->{$SetFunction}();
}