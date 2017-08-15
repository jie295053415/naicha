<?php
return array(
	//'配置项'=>'配置值'
	    /* 数据库设置 */
    'DB_TYPE'               =>  'pdo',     // 数据库类型  mysqli pdo  mysql(其中mysql不推荐使用)
    /*'DB_HOST'               =>  'localhost', // 服务器地址
    'DB_NAME'               =>  'php39',          // 数据库名*/
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  'root',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  'p39_',    // 数据库表前缀
    'DB_FIELDTYPE_CHECK'    =>  false,       // 是否进行字段类型检查
    'DB_FIELDS_CACHE'       =>  false,        // 启用字段缓存
    //如果数据库类型要采用'pdo'的话,必须配置一下设置,还要把'DB_HOST','DB_NAME'关掉
    'DB_DSN'                =>'mysql:host=localhost;dbname=php39;charset=utf8',
	
);