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
    protected $insertFields = 'goods_name,market_price,shop_price,is_on_sale,goods_desc,brand_id';
    //修改商品时调用create方法允许接收的字段
	protected $updateFields = 'id,goods_name,market_price,shop_price,is_on_sale,goods_desc,brand_id';
    //定义验证规则
    protected $_validate = array(
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
			if($ret['ok']==0){
                //先查询出原来图片的路径
                $oldLogo = $this->field('logo,mbig_logo,big_logo,mid_logo,sm_logo')->find($id);
                //从文件服务器上删除旧图片
                deleteImage($oldLogo);
            }
                /*
				unlink('./Public/Uploads/'.$oldLogo['logo']);
				unlink('./Public/Uploads/'.$oldLogo['mbig_logo']);
				unlink('./Public/Uploads/'.$oldLogo['big_logo']);
				unlink('./Public/Uploads/'.$oldLogo['mid_logo']);
				unlink('./Public/Uploads/'.$oldLogo['sm_logo']);
                */
        }

        //过滤该字段
        $data['goods_desc'] = removeXSS($_POST['goods_desc']);

    }

    //删除商品前的钩子函数
    protected function _before_delete($option){
        $id = $option['where']['id'];   //要删除的商品ID
        /*
         * 删除原来的图片
         */
        //先查询出原来图片的路径
        $oldLogo = $this->field('logo,mbig_logo,big_logo,mid_logo,sm_logo')->find($id);
        //从文件服务器上删除旧图片
        deleteImage($oldLogo);

    }

	
    /******翻页/搜索/排序******/
    public function search($perPage = 3)
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
        }//where is_on_sale = $ios
        //添加时间
        $fa = I('get.ios');
        $ta = I('get.ios');
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

        //生成页面 or 取某一页的数据
        $data = $this->order("$orderby $orderway")
            ->field('a.*,b.brand_name')
            ->alias('a')
            ->join('left join __BRAND__ b on a.brand_id=b.id')
            ->where($where)
            ->limit($pageObj->firstRow,$pageObj->listRows)
            ->select();
        //var_dump($this->getLastSql());exit;
        //返回数据给控制器
        return array(
            'data' => $data,      //数据
            'page' => $pageString //翻页字符串
        );

    }
	
	
	
}

