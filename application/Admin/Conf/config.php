<?php
return array(
	//'配置项'=>'配置值'
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

    'TMPL_ACTION_ERROR'     =>  THINK_PATH.'Tpl/dispatch_jump.tpl', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  THINK_PATH.'Tpl/dispatch_jump.tpl', // 默认成功跳转对应的模板文件

    'DEFAULT_FILTER'        =>  'trim,htmlspecialchars', // 默认参数过滤方法 用于I函数...

    /*
     * 图片相关的配置
     */
    'IMAGE_CONFIG' => array(
        'maxSize'  => 1024*1024,
        'exts'     => array('jpg', 'gif', 'png', 'jpeg'),
        'rootPath' => './Public/Uploads/',    //图片保存的路径
        'viewPath' => __ROOT__.'/Public/Uploads/',     //图片显示的路径
    ),


);