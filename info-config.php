<?php
//配置文件
$path='/work/infosys';						//该文件夹对应服务器的域名绝对路径
$urlpath=$_SERVER['HTTP_HOST'].$path;	//组合成网络路径  用于广告交互

$config=array(
	'dbms'=>'mysql',		//数据库类型
	'dbhost'=>'localhost',	//数据库地址
	'dbuser'=>'root',		//数据库用户名
	'dbpwd'=>'',			//数据库密码
	'dbname'=>'infosys'		//数据库名称
);

define('__ROOT__',dirname(__FILE__));
/*
require_once(__ROOT__.'/models/Page.class.php');
require_once(__ROOT__.'/models/CDbCriteria.php');
require_once(__ROOT__.'/models/Mysql.class.php');
date_default_timezone_set('PRC');
$dblink=mysql::getInstance($config);
require_once(__ROOT__.'/models/User.php');
*/

?>
