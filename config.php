<?php
/*set data base info*/

/*
//mysql
$MeHend = array(
	'DbType' => 'mysql',

	'DbHost' => '127.0.0.1',
	'DbUser' => 'root',
	'DbPw' => '',
	'DbName' => 'blueseeds',

	'P_DbHost' => '127.0.0.1',
	'P_DbUser' => 'root',
	'P_DbPw' => '',
	'P_DbName' => 'blueseeds'
);
//sqlite
$MeHend = array(
	'DbType' => 'sqlite',
	'DbName' => 'mvc.s3db'
);
*/
//sqlite
$MeHend = array(
	'DbType' => 'sqlite',
	'DbName' => 'mvc.s3db'
);

$MeHend['Cache'] = false;
foreach($MeHend as $key => $val){
	if(!defined($key)){
		define($key, $val);
	}
}
/*set data base info*/
