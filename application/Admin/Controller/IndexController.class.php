<?php
namespace Admin\Controller;

use Think\Controller;

class IndexController extends Controller
{
    //主页
    public function index()
    {
        $this->display();
    }

    //头部
    public function top()
    {
        $this->display();
    }

    //菜单
    public function menu()
    {
        $this->display();
    }

    //主体
    public function main()
    {
        $this->display();
    }
}