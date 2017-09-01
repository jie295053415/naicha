<?php
/**
 * Created by PhpStorm.
 * User: KiTLoYuan
 * Date: 2017/8/8
 * Time: 22:43
 */
namespace Admin\Controller;



class GoodsController extends BaseController
{
    //
    public function goods_number(){

        //接收商品ID
        $id = I('get.id');
        $gnModel = D('goods_number');

        //处理表单
        if(IS_POST){
            //先删除原库存
            $gnModel->where(array(
                'goods_id' => array('eq',$id),
            ))->delete();
            $gaid = I('post.goods_attr_id');
            $gn = I('post.goods_number');

            //var_dump($gaid);echo '<hr/>';
            //先计算商品属性ID和库存量的比例
            $gaidCount = count($gaid);
            $gnCount = count($gn);
            $rate = $gaidCount/$gnCount;
            //循环库存量
            $_i = 0; //取第几个商品属性ID
            foreach ($gn as $k => $v){
                $_goodsAttrId = array();  //把下面取出来的ID放这里
                //后开从商品属性ID数组中取出$rate个，循环一次取一个
                for($i = 0; $i < $rate; $i++){
                    $_goodsAttrId[] = $gaid[$_i];
                    $_i++;
                }
                sort($_goodsAttrId,SORT_NUMERIC); // 以数字的形式排序
                //把取出来的商品属性ID转换成字符串
                $_goodsAttrId = (string)implode(',',$_goodsAttrId);
                $gnModel->add(array(
                    'goods_id' => $id,
                    'goods_attr_id' => $_goodsAttrId,
                    'goods_number' => $v,
                ));
            }
        }

        //根据商品ID取出这件商品所有可选属性的值
        $gaModel = D('goods_attr');
        $gaData = $gaModel->alias('a')
            ->join('left join __ATTRIBUTE__ b on a.attr_id=b.id')
            ->field('a.*,b.attr_name')
            ->where(array(
                'a.goods_id' => array('eq',$id),
                'b.attr_type' => array('eq','可选'),
            ))->select();
        //整理这个二维数组：转化为三维：把属性相同的放在一起
        $_gaData = array();
        foreach($gaData as $k => $v){
            $_gaData[$v['attr_name']][] = $v;
        }

        //先取出这件商品已经设置的库存量
        $gnData = $gnModel->where(array(
            'goods_id' => $id,
        ))->select();

        //显示页面
        $this->assign(array(
            'gaData' => $_gaData,
            'gnData' => $gnData,
            '_page_title' => '库存量',
            '_page_btn_name' => '返回商品列表',
            '_page_btn_link' => U('lst'),
        ));
        //显示页面
        $this->display();
    }

    //处理删除属性
    public function ajaxDelAttr(){
        $goodsId = addslashes(I('get.goods_id'));
        $gaid = addslashes(I('get.gaid'));
        $gaModel = D('goods_attr');
        $gaModel->delete($gaid);
        //删除相关库存量
        $gnModel = D('goods_number');
        $gnModel->where(array(
            'goods_id' => array('EXP',"=$goodsId and FINd_IN_SET($gaid,attr_list)"),
        ))->delete();
    }

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

        //取出当前类型下所有的属性
        $attrModel = D('Attribute');
        $attrData = $attrModel->alias('a')
            ->field('a.id attr_id,a.attr_name,a.attr_type,a.attr_option_values,b.attr_value,b.id')
            ->join('left join __GOODS_ATTR__ B on (a.id=b.attr_id and b.goods_id='.$id.')')
            ->where(array(
                'a.type_id' => array('eq',$data['type_id'])
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
            'gaData' => $attrData,
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

