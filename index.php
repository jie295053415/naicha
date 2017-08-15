<?php
/**
 * Created by PhpStorm.
 * User: KiTLoYuan
 * Date: 2017/8/6
 * Time: 20:12
 */

//跳后台
header('location:admin.php/Index/index');


//项目目录路径
define('APP_PATH','application/');
//绑定前台模块
//define('BIND_MODULE','Home');
//开启安全模式
//define('BUILD_DIR_SECURE','false');
//启动调试模式
define('APP_DEBUG','true');
//引入tp
require_once 'ThinkPHP/ThinkPHP.php';

