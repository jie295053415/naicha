<?php
/**
 * Created by PhpStorm.
 * User: KiTLoYuan
 * Date: 2017/8/6
 * Time: 20:12
 */


//判断PHP版本
if(version_compare(PHP_VERSION,'5.3.0','<')) die('require PHP > 5.3.0 !');
//项目目录路径
define('APP_PATH','application/');
//启动调试模式
define('APP_DEBUG','true');
//引入tp
require_once 'ThinkPHP/ThinkPHP.php';

