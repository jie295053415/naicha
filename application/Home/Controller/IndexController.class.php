<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller
{
    //首页
    public function index()
    {
        //设置页面信息
        $this->assign(array(
            '_page_title' => '首页',
            '_show_nav' => 1,
        ));
        $this->display();
    }
    //商品详情页
    public function goods()
    {
        //设置页面信息
        $this->assign(array(
            '_page_title' => '商品详情页',
            '_show_nav' => 0,
        ));
        $this->display();
    }
}