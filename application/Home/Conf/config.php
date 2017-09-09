<?php
return array(
	//'配置项'=>'配置值'
    'HTML_CACHE_ON'     => true, // 开启静态缓存
    'HTML_CACHE_TIME'   => 60,   // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX'  => '.shtml', // 设置静态缓存文件后缀
    //这个模块中那些页面生成静态页
    'HTML_CACHE_RULES'  => array(  // 定义静态缓存规则
        'index:index' => array('index',86400), //首页生成index.shtml
        'index:goods' => array('goods-{id}',3600), //首页生成goods.shtml
    )



    );