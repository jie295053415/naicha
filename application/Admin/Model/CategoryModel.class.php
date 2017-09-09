<?php
namespace Admin\Model;
use Think\Model;
class CategoryModel extends Model
{
	protected $insertFields = array('cat_name','parent_id','is_floor');
	protected $updateFields = array('id','cat_name','parent_id','is_floor');
	protected $_validate = array(
		array('cat_name', 'require', '分类名称不能为空！', 1, 'regex', 3),
	);

	//找一个分类所有子分类的ID
	public function getChildren($catId){
		//取出所有的分类
		$data = $this->select();
		//递归从所有的分类中挑出子分类的ID
		return $this->_getChildren($data,$catId,TRUE);

	}

	private function _getChildren($data,$catId,$isClear = FALSE){
		//静态一个变量，接收子分类
		static $_ret = array();
		if($isClear){
			$_ret = array();
		}
		//选好所有的分类找子分类
		foreach($data as $v){
			if($v['parent_id'] == $catId){
				$_ret[] = $v['id'];
				//递归找这个$v的子分类
				$this->_getChildren($data,$v['id']);
			}
		}
		return $_ret;
	}

	//获取树形数据
	public function getTree(){
		$data = $this->select();
		return $this->_getTree($data);
	}
	private function _getTree($data,$parent_id=0, $level=0){
		static $_ret = array();
		foreach($data as $v){
			if($v['parent_id'] == $parent_id){
				$v['level'] = $level;
				$_ret[] = $v;
				//找子分类
				$this->_getTree($data,$v['id'],$level+1);
			}
		}
		return $_ret;
	}

	//删除分类之前删除子分类
	protected function _before_delete($options)
	{
		/************第一种删除法*************/
		/*
		//先找出所有子分类的ID

		$children = $this->getChildren($options['where']['id']);
		if($children){
			$children = implode(',',$children);
			//为了防止在钩子函数里调用该模型的delete函数而造成的死循环
			//调了父类模型，然后通过模型引导category删除子分类
			$model = new \Think\Model;
			$model->table('__CATEGORY__')->delete($children);
		}
		/*********第二种删除法************/
		//找出所有子分类,然后把母分类的id也放到子分类里就可以了
		//TODO 该方法的原理
		$children = $this->getChildren($options['where']['id']);
		$children[] = $options['where']['id'];
		//echo '<pre>';
		//var_dump($options);
	}

	/*****************前台方法**********************/
	//获取导航条上的方法
	public function getNavData()
	{
		$catData = S('catData');
		if(!$catData){
			//找出顶级分类
			$all = $this->select();
			$ret = array();
			foreach($all as $v)
			{
				if($v['parent_id'] == 0)
				{
					//循环所有的分类找出这个顶级分类的子分类
					foreach($all as $v1)
					{
						if($v1['parent_id'] == $v['id'])
						{
							//循环所有的分类找出这个顶级分类的子分类
							foreach($all as $v2)
							{
								if($v2['parent_id'] == $v1['id'])
								{
									$v1['children'][] = $v2;
								}
							}
							$v['children'][] = $v1;
						}
					}
					$ret[] = $v;
				}
			}
			//把数组缓存1天
			S('catData', $ret, 86400);
			return $ret;
		}else{
			return $catData;
		}
	}

	/*
	 * 取出一个分类所有上级分类
	 * @param $catId int
	 */
	public function parentPath($catId)
	{
		static $ret;
		$info = $this->field('id,cat_name,parent_id')->find($catId);
		$ret[] = $info;
		//如果还有上级就递归
		if($info['parent_id'] > 0)
		{
			$this->parentPath($info['parent_id']);
		}
		return $ret;
	}

	//获取首页楼层的数据
	public function floorData()
	{
		$floorData = S('floorData');
		if($floorData){
			return $floorData;
		}else{
			//先取出推荐到楼层的顶级分类
			$ret = $this->where(array(
					'parent_id' => array('eq', 0),
					'is_floor' => array('eq', '是'),
			))->select();
			//循环每个楼层取出楼层中的数据
			$goodsModel = D('Admin/Goods');
			foreach($ret as $k => $v)
			{
				/*******这个楼层中的品牌数据*******/
				$goodsId = $goodsModel->getGoodsIdByCatId($v['id']);
				//再取出这些商品所用到的品牌
				$ret[$k]['brand'] = $goodsModel->alias('a')
						->join('left join __BRAND__ b on a.brand_id=b.id')
						->field('DISTINCT brand_id,b.brand_name,b.logo')
						->where(array(
								'a.id' => array('in',$goodsId),
								'a.brand_id' => array('neq',0),
						))->limit(9)->select();


				/*****取出未推荐的二级分类并保存到这个顶级分类的subCat字段中*********/
				$ret[$k]['subCat'] = $this->where(array(
						'parent_id' => array('eq',$v['id']),
						'is_floor' => array('eq','否'),
				))->select();
				/*****取出推荐的二级分类并保存到这个顶级分类的subCat字段中********/
				$ret[$k]['recSubCat'] = $this->where(array(
						'parent_id' => array('eq',$v['id']),
						'is_floor' => array('eq','是'),
				))->select();

				/*****循环每个推荐的二级分类取出分类下的8件被推荐到楼层的商品**********/
				foreach($ret[$k]['recSubCat'] as $k1 => &$v1)
				{
					//取出这个分类下所有的商品ID并返回一维数组
					$gids = $goodsModel->getGoodsIdByCatId($v1['id']);
					//在根据商品ID取出商品的详细信息
					$v1['goods'] = $goodsModel->field('id,mid_logo,goods_name,shop_price')->where(array(
							'is_on_sale' => array('eq','是'),
							'is_floor' => array('eq','是'),
							'id' => array('in',$gids),
					))->order('sort_num ASC')->limit(8)->select();
				}
			}
			S('floorData',$ret,86400);
			return $ret;
		}
	}
}