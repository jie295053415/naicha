<?php
/**
 * Created by PhpStorm.
 * User: KiTLoYuan
 * Date: 2017/9/2
 * Time: 14:03
 */
namespace Home\Controller;
use Think\Controller;

class NavController extends Controller
{
    public function __construct()
    {
        //必须先调用父类的构造方法
        parent::__construct();
        $catModel = D('Admin/Category');
        $catData = $catModel->getNavData();
        $this->assign('catData',$catData);
    }
}
