<?php
namespace Admin\Model;
use Think\Model;
class CategoryModel extends Model
{
	protected $insertFields = array('cat_name','parent_id');
	protected $updateFields = array('id','cat_name','parent_id');
	protected $_validate = array(
		array('parent_id', 'require', '分类名称不能为空！', 1, 'regex', 3),

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
}