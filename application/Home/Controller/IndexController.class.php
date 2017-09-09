<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends NavController
{
    //处理浏览历史
    public function displayHistory(){
        $id = I('get.id');
        //先从COOKIE中取出浏览历史的iD数组
        $data = isset($_COOKIE['display_history']) ? unserialize($_COOKIE['display_history']) : array();
        //把最新浏览的商品放到数组中的第一个位置上
        array_unshift($data,$id);
        //去重
        $data = array_unique($data);
        if(count($data) > 6){
            $data = array_slice($data, 0 ,6);
        }
        //数组存回COOKIE
        setcookie('display_history', serialize($data), time()+30*86400, '/');
        //在根据商品的id取出商品的详细信息
        $goodsModel = D('Goods');
        $data = implode(',', $data);
        $gData = $goodsModel->field('id,mid_logo,goods_name')->where(array(
            'id' => array('in', $data),
            'is_on_sale' => array('eq', '是'),
        ))->order("FIELD(id,$data)")->select();
        //json格式化数据
        echo json_encode($gData);
    }
    //首页
    public function index()
    {
        //取出疯狂抢购的商品
        $goodsModel = D('Admin/Goods');
        $goods1 = $goodsModel->getPromoteGoods();
        //取出三种推荐商品
        $goods2 = $goodsModel->getRecGoods('is_hot');
        $goods3 = $goodsModel->getRecGoods('is_best');
        $goods4 = $goodsModel->getRecGoods('is_new');
        //取出首页楼层的数据
        $catModel = D('Admin/Category');
        $floorData = $catModel->floorData();

        //设置页面信息
        $this->assign(array(
            'goods1' => $goods1,
            'goods2' => $goods2,
            'goods3' => $goods3,
            'goods4' => $goods4,
            'floorData' => $floorData,
            '_show_nav' => 1,
            '_page_title' => '首页',
            '_page_keywords' => '首页',
            '_page_description' => '首页',
        ));
        $this->display();
    }
    //商品详情页
    public function goods()
    {
        //接收商品的ID
        $id = I('get.id');
        //根据ID取出商品的详细信息
        $gModel = D('Goods');
        $info = $gModel->find($id);
        //取出面包屑导航信息
        $catModel = D('Admin/Category');
        $catPath = $catModel->parentPath($info['cat_id']);
        $catPath = array_reverse($catPath);

        $this->assign(array(
            'info' => $info,
            'catPath' => $catPath,
        ));

        //设置页面信息
        $this->assign(array(
            '_show_nav' => 0,
            '_page_title' => '商品详情页',
            '_page_keywords' => '商品详情页',
            '_page_description' => '商品详情页',
        ));
        $this->display();
    }
}