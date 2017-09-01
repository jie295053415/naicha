<?php
/**
 * Created by PhpStorm.
 * User: KiTLoYuan
 * Date: 2017/8/9
 * Time: 9:00
 */
namespace Admin\Model;

use Think\Model;

class GoodsModel extends Model
{

    //添加是调用create方法允许接收的字段
    protected $insertFields = 'goods_name,market_price,shop_price,is_on_sale,goods_desc,brand_id,cat_id,type_id';
    //修改商品时调用create方法允许接收的字段
	protected $updateFields = 'id,goods_name,market_price,shop_price,is_on_sale,goods_desc,brand_id,cat_id,type_id';
    //定义验证规则
    protected $_validate = array(
        array('cat_id', 'require', '必须选择主分类!', 1),
        array('goods_name', 'require', '商品名称不能为空!', 1),
        array('market_price', 'currency', '市场价格必须是货币类型!', 1),
        array('shop_price', 'currency', '本店价格必须是货币类型!', 1),
    );

    //这个方法在添加之前会自动被调用--> 钩子方法
    //第一个参数:表单中即将要插入到数据库中的数据->数组
    //&按引用传递:函数内部要修改函数外部穿进来的变量必须按引用传递,除非传递的是一个对象,因为对象默认是引用传递

	//添加商品前的钩子函数
    protected function _before_insert(&$data, $option)
    {

        //处理LOGO
        //判断有没有上传图片
        if ($_FILES['logo']['error'] == 0) {
            $ret = uploadOne('logo','Goods',array(
                array(700,700),
                array(350,350),
                array(130,130),
                array(50,50),
            ));
            $data['logo'] = $ret['images'][0];
            $data['mbig_logo'] = $ret['images'][1];
            $data['big_logo'] = $ret['images'][2];
            $data['mid_logo'] = $ret['images'][3];
            $data['sm_logo'] = $ret['images'][4];

        }
        //获取当前时间并添加到表单中这样就会插入到数据库中
        date_default_timezone_set('PRC');
        $data['addtime'] = date('Y-m-d H:i:s', time());
        //过滤该字段
        $data['goods_desc'] = removeXSS($_POST['goods_desc']);

    }
	
	//添加商品后的钩子函数
	protected function _after_insert($data, $option)
	{
        /*************处理商品属性的代码****************/
        $attrValue = I('post.attr_value');
        $gaModel = D('goods_attr');
        foreach($attrValue as $k=>$v){
            //把属性值的数组去去重
            $v = array_unique($v);
            foreach ($v as $k1 =>$v1) {
                $gaModel->add(array(
                    'goods_id' => $data['id'],
                    'attr_id' => $k,
                    'attr_value' => $v1,
                ));
            }
        }

        /*************处理扩展分类************************/
        $ecid = I('post.ext_cat_id');
        if($ecid){
            $gcModel = D('goods_cat');
            foreach($ecid as $v){
                if(empty($v)){
                    continue;
                }else{
                    $gcModel->add(array(
                        'cat_id' => $v,
                        'goods_id' => $data['id'],
                    ));
                }
            }
        }
        /*************处理相册图片**************/
        if(isset($_FILES['pic'])){
            $pics = array();
            foreach($_FILES['pic']['name'] as $k => $v){
                $pics[] = array(
                    'name' =>$v,
                    'type' => $_FILES['pic']['type'][$k],
                    'tmp_name' => $_FILES['pic']['tmp_name'][$k],
                    'error' => $_FILES['pic']['error'][$k],
                    'size' => $_FILES['pic']['size'][$k],
                );
            }
            //把处理好的图片数组塞回$_FILES
            $_FILES = $pics;
            $gpModel = D('goods_pic');
            //选好每个上传
            foreach($pics as $k=>$v){
                if($v['error'] == 0){
                    $ret = uploadOne($k,'Goods',array(
                       array(650,650),
                       array(350,350),
                       array(50,50),
                    ));
                    if($ret['ok'] == 1){
                        $gpModel->add(array(
                            'pic' => $ret['images'][0],
                            'big_pic' => $ret['images'][1],
                            'mid_pic' => $ret['images'][2],
                            'sm_pic' => $ret['images'][3],
                            'goods_id' => $data['id'],
                        ));
                    }
                }
            }
        }

        /**********处理会员价格*********/
        $mp =I('post.member_price');
		$mpModel = D('member_price');
		foreach($mp as $k=>$v){
			$_v = (float)$v;
			//如果设置了会员价格就插入到表中
			If($_v>0)
			{
				$mpModel->add(array(
					'price' =>$_v,
					'level_id' =>$k,
					'goods_id' =>$data['id'],
					
				));
			}
		}
		
	}
	
	//修改商品前的钩子函数
	protected function _before_update(&$data, $option)
    {
        //var_dump($option);exit;
		$id = $option['where']['id'];  //要修改的商品ID

        /*********修改商品属性*********/
        $gaid = I('post.goods_attr_id');
        $attrValue = I('post.attr_value');
        $gaModel = D('goods_attr');
        $_i = 0; //循环次数
        foreach($attrValue as $k => $v){
            foreach ($v as $k1 => $v1) {
                //找这个属性值是否有id
                if($gaid[$_i] == ''){
                    //空值就是要添加新的属性值
                    $gaModel->add(array(
                        'goods_id' => $id,
                        'attr_id' => $k,
                        'attr_value' => $v1,
                    ));
                }else{
                    $gaModel->where(array(
                        'id' => array('eq',$gaid[$_i]),
                    ))->setField('attr_value',$v1);
                }
                $_i++;
            }
        }


        /*********处理扩展分类*********/
        $ecid = I('post.ext_cat_id');
        $gcModel = D('goods_cat');
        //先删除原分类数据
        $gcModel->where(array(
            'goods_id' => array('eq',$id),
        ))->delete();
        if($ecid){
            foreach ($ecid as $v) {
                if(!empty($v)){
                    $gcModel->add(array(
                        'cat_id' => $v,
                        'goods_id' => $id,
                    ));
                }
            }
        }

		/************处理logo************/
        //判断有没有上传图片
        if ($_FILES['logo']['error'] == 0) {
            $ret = uploadOne('logo','Goods',array(
                array(700,700),
                array(350,350),
                array(130,130),
                array(50,50),
            ));
            $data['logo'] = $ret['images'][0];
            $data['mbig_logo'] = $ret['images'][1];
            $data['big_logo'] = $ret['images'][2];
            $data['mid_logo'] = $ret['images'][3];
            $data['sm_logo'] = $ret['images'][4];

				/*********删除旧图片**********/

                //先查询出原来图片的路径
                $oldLogo = $this->field('logo,mbig_logo,big_logo,mid_logo,sm_logo')->find($id);
                //从文件服务器上删除旧图片
                deleteImage($oldLogo);
        }

        /************处理相册图片***************/
        if(isset($_FILES['pic'])){
            $pics = array();
            foreach($_FILES['pic']['name'] as $k=> $v){
                $pics[] =array(
                    'name' => $v,
                    'type' => $_FILES['pic']['type'][$k],
                    'tmp_name' => $_FILES['pic']['tmp_name'][$k],
                    'error' => $_FILES['pic']['error'][$k],
                    'size' => $_FILES['pic']['size'][$k],
                );
            }
            $_FILES = $pics;
            $gpModel = D('goods_pic');
            //循环每个上传
            foreach ($pics as $k=>$v) {
                if($v['error'] == 0){
                    $ret = uploadOne($k,'Goods',array(
                        array(650,650),
                        array(350,350),
                        array(50,50),
                    ));
                    if($ret['ok'] == 1){
                        $gpModel->add(array(
                            'pic' => $ret['images'][0],
                            'big_pic' => $ret['images'][1],
                            'mid_pic' => $ret['images'][2],
                            'sm_pic' => $ret['images'][3],
                            'goods_id' => $id,
                        ));
                    }
                }
            }
        }

        /**********处理会员价格*************/
        $mp = I('post.member_price');
        $mpModel = D('member_price');
        //先删除原来的会员价格
        $mpModel->where(array(
            'goods_id' => array('eq',$id),
        ))->delete();
        foreach($mp as $k=>$v){
            $_v = (float)$v;
            //如果设置了会员价格就插入到表中
            if($_v>0){
                $mpModel->add(array(
                    'price' => $_v,
                    'level_id' => $k,
                    'goods_id' => $id,
                ));
            }
        }

        //过滤该字段
        $data['goods_desc'] = removeXSS($_POST['goods_desc']);
    }

    //删除商品前的钩子函数
    protected function _before_delete($option){
        $id = $option['where']['id'];   //要删除的商品ID

        /**********删除商品属性***************/
        $gaModel = D('goods_attr');
        $gaModel->where(array(
            'goods_id' => array('eq',$id),
        ))->delete();

        /*
         * 删除扩展分类
         */
        $gcModel = D('goods_cat');
        $gcModel->where(array(
            'goods_id' => array('eq',$id),
        ))->delete();

        /************删除相册中的图片**************/
        //先从相册表中取出相册所在硬盘的路径
        $gpModel = D('goods_pic');
        $pics = $gpModel->field('pic,sm_pic,mid_pic,big_pic')->where(array(
            'goods_id' => array('eq',$id),
        ))->select();
        //选好每个图片从硬盘上删除图片
        foreach($pics as $v){
            deleteImage($v);
        }
        //从数据库中把记录删除
        $gpModel->where(array(
            'goods_id' => array('eq',$id),
        ))->delete();

        /******** 删除原来的图片***********/
        //先查询出原来图片的路径
        $oldLogo = $this->field('logo,mbig_logo,big_logo,mid_logo,sm_logo')->find($id);
        //从文件服务器上删除旧图片
        deleteImage($oldLogo);
        /*
         * 删除会员价格
         */
        $mpModel = D('member_price');
        $mpModel->where(array(
            'goods_id' => array('eq',$id),
        ))->delete();

    }

    /*
     * 通过分类查找商品id
     * @param $catId      //分类ID
     * @return array $id  //商品ID
     *
     */

    public function getGoodsIdByCatId($catId){
        //通过主分类查找所有的子分类
        $catModel = D('category');
        $children = $catModel->getChildren($catId);
        //和子分类一起
        $children[] = $catId;

        /******通过主分类或者扩展分类查找所有的子分类*******/
        //取出主分类下的商品ID
        $gids = $this->field('id')->where(array(
            'cat_id' => array('IN',$children),
        ))->select();
        //echo '<pre>';var_dump($gids);
        //取出扩展分类下的商品ID
        $gcModel = D('goods_cat');
        $gids1 = $gcModel->field('DISTINCT goods_id id')->where(array(
            'cat_id' => array('IN',$children),
        ))->select();
        //var_dump($gids1);die;
        //把主分类的ID和扩展分类下的商品ID合并成一个二维数组【两个都不为空时合并，否则取出不为空的数组】
        if($gids && $gids1){
            $gids = array_merge($gids,$gids1);
        }elseif($gids1){
            $gids = $gids1;
        }
        //二维转一维
        $id = array();
        foreach($gids as $v){
            if(!in_array($v['id'],$id)){
                $id[] = $v['id'];
            }
        }
        return $id;
    }

    /******翻页/搜索/排序******/
    public function search($perPage = 5)
    {
        /********搜索功能********/
        $where = array(); //空的where条件
        //商品名称不能为空
        $gn = I('get.gn');
        if($gn)  {
            $where['a.goods_name'] = array('like', '%'.$gn.'%'); //weher goods_name like '%'.$gn.'%'
        }
        //价格
        $fp = I('get.fp');
        $tp = I('get.tp');
        if($fp&&$tp) {
            $where['a.shop_price'] = array('between',array($fp,$tp)); //where shop_price between $fp= and $tp
        }
        elseif($fp) {
            $where['a.shop_price'] = array('egt',$fp); //where shop_price >=$fp
        }
        elseif($tp) {
            $where['a.shop_price'] = array('elt',$tp); //where shop_price<=$fp
        }
        //是否上架
        $ios = I('get.ios');
        if($ios) {
            $where['a.is_on_sale'] = array('eq',$ios);
        }                                                   //where is_on_sale = $ios
        //添加时间
        $fa = I('get.fa');
        $ta = I('get.ta');
        if($fa && $ta) {
            $where['a.addtime'] = array('between',array($fa,$ta));
        }
        elseif($fa) {
            $where['a.addtime'] = array('egt',$fa);
        }
        elseif($ta) {
            $where['a.addtime'] = array('elt',$ta);
        }
        //品牌
        $brandId = I('get.brand_id');
        if($brandId){
            $where['a.brand_id'] = array('eq',$brandId);
        }
        $catId = I('get.cat_id');
        if($catId){
            //查找主分类和扩展分类的商品ID
            $gids = $this->getGoodsIdByCatId($catId);
            $where['a.id'] = array('IN',$gids);
        }
        /******翻页*****/
        /*分页变量*/
        //总记录数
        $count = $this->where($where)->count();
        //生成翻页类的对象
        $pageObj = new \Think\Page($count,$perPage);
        //设置样式
        $pageObj->setconfig('prev','上一页');
        $pageObj->setconfig('next','下一页');
        //生成页面下显示的上一页,下一页的字符串
        $pageString = $pageObj->show();
		
		/***********排序**********/
		$orderby = 'a.id';   //默认的排序字段
		$orderway = 'desc';//默认的排序方式
		$odby = I('get.odby');
		if($odby){
			if($odby == 'id_asc') $orderway = 'asc';
			elseif($odby == 'price_desc') $orderby = 'shop_price';
			elseif($odby == 'price_asc'){
				$orderby = 'shop_price';  
				$orderway = 'asc';
			}
		}

        /**********取某一页的数据****************/
        $data = $this->order("$orderby $orderway")                  //排序
            ->field('a.*,b.brand_name,c.cat_name,GROUP_CONCAT(e.cat_name SEPARATOR "<br/>") ext_cat_name')
            ->alias('a')
            ->join('left join __BRAND__ b on a.brand_id=b.id
                    left join __CATEGORY__ c on a.cat_id=c.id
                    left join __GOODS_CAT__ d on a.id=d.goods_id
                    left join __CATEGORY__ e on d.cat_id=e.id')
            ->where($where)                                         //搜索
            ->group('a.id')
            ->limit($pageObj->firstRow,$pageObj->listRows)          //翻页
            ->select();
        //var_dump($this->getLastSql());exit;
        //返回数据给控制器
        return array(
            'data' => $data,      //数据
            'page' => $pageString //翻页字符串
        );

    }
	
	
	
}

