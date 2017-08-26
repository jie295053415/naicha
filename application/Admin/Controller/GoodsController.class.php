<?php
/**
 * Created by PhpStorm.
 * User: KiTLoYuan
 * Date: 2017/8/8
 * Time: 22:43
 */
namespace Admin\Controller;

use Think\Controller;

class GoodsController extends Controller
{
    //处理获取属性的ajax请求
    public function ajaxGetAttr(){
        $typeId =I('get.type_id');
        $attrModel = D('Attribute');
        $attrData = $attrModel->where(array(
            'type_id' => array('eq',$typeId),
        ))->select();
        echo json_encode($attrData);
    }

    //处理AJAX删除图片的请求
    public function ajaxDelPic()
    {
        //获取ajax传来的参数，并且绑定了是哪张图片需要删除
        $picId = I('get.picid');
        //跟进ID从硬盘上数据删除中删除图片
        $gpModel = D('goods_pic');
        $pic = $gpModel->field('pic,sm_pic,mid_pic,big_pic')->find($picId);
        //从硬盘删除图片
        deleteImage($pic);
        //从数据库中删除记录
        $gpModel->delete($picId);

    }

    /********显示和处理表单（添加商品）**********/
    public function add()
    {
        /*
        echo '<pre>';
        var_dump($_POST);exit;
        */
        //判断用户是否提交表单
        if (IS_POST) {
            $pics = array();
            foreach ($_FILES['pic']['name'] as $k => $v) {
                $pics[] = array(
                    'name' => $v,
                    'type' => $_FILES['pic']['type'][$k],
                    'tmp_name' => $_FILES['pic']['tmp_name'][$k],
                    'error' => $_FILES['pic']['error'][$k],
                    'size' => $_FILES['pic']['size']['$k'],
                );
            }

            $model = D('goods');
            //2.CREATE方法：a.接收数据并保存到模型中 b.根据模型中定义的规则验证表单
            /*
             * 第一个参数：要接收的数据默认是$_POST
             * 第二个参数:表单的类型,当前是添加还是修改的表单,1:添加  2:修改
             * $_POST:表单中原始的数据, I('post.'):过滤之后的$_POST数据,过滤XSS攻击
             */
            if ($model->create(I('post.'), 1)) {
                //插入到数据库中
                if ($model->add()) {//在add()里又先调用了_before_insert方法
                    //显示成功信息并等待1秒后重定向
                    $this->success('操作成功!', U('lst'));
                    exit;
                }
            }
            //如果走到这 说明上面失败了在这里除了失败请求
            //从模型中取出失败的原因
            $error = $model->getError();
            //由控制器显示错误信息,并在3秒后调回上一个页面
            $this->error($error);
        }
        //取出所有的会员级别
        $mlData = D('member_level')->select();
        //取出所有的分类
        $catData = D('category')->getTree();

        //显示页面
        $this->assign(array(
            'mlData' => $mlData,
            'catData' => $catData,
            '_page_title' => '添加新商品',
            '_page_btn_name' => '商品列表',
            '_page_btn_link' => U('lst'),
        ));
        $this->display();
    }

    /**********修改商品************/
    public function edit()
    {
        $id = I('get.id');   //接收商品id
        $model = D('goods'); //Goods模型
        //var_dump($_FILES); echo '<hr/>';
        if (IS_POST) {
            if ($model->create(I('post.'), 2)) {
                //插入到数据库中
                if ($model->save() !== false) { //save()有3个值，false，受影响行数（0 & more）
                    //显示成功信息并等待1秒后重定向
                    $this->success('操作成功!', U('lst'));
                    exit;
                }
            }
            //如果走到这 说明上面失败了在这里除了失败请求
            //从模型中取出失败的原因
            $error = $model->getError();
            //由控制器显示错误信息,并在3秒后调回上一个页面
            $this->error($error);
        }
        //根据ID取出要修改的商品的原信息
        $data = $model->find($id);
        //var_dump($data);exit;

        //取出所有的会员级别
        $mlData = D('member_level')->select();

        //取出这件商品的会员价格
        $mpModel = D('member_price');
        $mpData = $mpModel->where(array(
            'goods_id' => array('eq', $id),
        ))->select();
        //把这二维数组转一维，  level_id=>price
        $_mpData = array();
        foreach ($mpData as $v) {
            $_mpData[$v['level_id']] = $v['price'];
        }

        //取出相册中先有的图片
        $gpModel = D('goods_pic');
        $gpData = $gpModel->field('id,mid_pic')->where(array(
            'goods_id' => array('eq', $id),
        ))->select();
        //var_dump($gpModel->getLastSql());
        //取出所有的分类
        $catData = D('category')->getTree();

        //取出扩展分类的ID
        $gcModel = D('goods_cat');
        $gcData = $gcModel->field('cat_id')->where(array(
            'goods_id' => array('eq',$id),
        ))->select();

        //取出这间商品已经设置了的属性值
        $gaModel = D('goods_attr');
        $gaData = $gaModel->alias('a')
            ->field('a.*,b.attr_name,b.attr_type,b.attr_option_values')
            ->join('left join __ATTRIBUTE__ b on a.attr_id=b.id')
            ->where(array(
                'a.goods_id' => array('eq',$id)
            ))->select();

        /*
        echo '<pre>';
        var_dump($data);echo '<hr/>';
        var_dump($gaData);exit;
        */


        //显示页面
        $this->assign(array(
            'data' => $data,
            'mpData' => $_mpData,
            'catData' => $catData,
            'mlData' => $mlData,
            'gpData' => $gpData,
            'gcData' => $gcData,
            'gaData' => $gaData,
            '_page_title' => '修改商品',
            '_page_btn_name' => '商品列表',
            '_page_btn_link' => U('lst'),
        ));
        $this->display();
    }

    /**********删除商品*********/
    public function delete()
    {
        $model = D('goods');
        if ($model->delete(I('get.id')) !== false) {
            $this->success('删除成功！', U('lst'));
        } else {
            $this->error('删除失败！原因：', $model->getError());
        }
    }

    //商品列表页
    public function lst()
    {
        $model = D('goods');
        //传递数据给view
        $data = $model->search();

        //取出所有的分类
        $catData = D('category')->getTree();

        $this->assign($data)
            ->assign(array(
                'catData' => $catData,
                '_page_title' => '商品列表',
                '_page_btn_name' => '添加新商品',
                '_page_btn_link' => U('add'),
            ));
        $this->display();
    }
}

