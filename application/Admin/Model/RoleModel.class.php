<?php
namespace Admin\Model;
use Think\Model;
class RoleModel extends Model 
{
	protected $insertFields = array('role_name');
	protected $updateFields = array('id','role_name');
	protected $_validate = array(
		array('role_name', 'require', '角色名称不能为空！', 1, 'regex', 3),
		array('role_name', '', '角色名称已经存在！', 1, 'unique', 3),
	);
	public function search($pageSize = 20)
	{
		/**************************************** 搜索 ****************************************/
		$where = array();
		/************************************* 翻页 ****************************************/
		$count = $this->alias('a')->where($where)->count();
		$page = new \Think\Page($count, $pageSize);
		// 配置翻页的样式
		$page->setConfig('prev', '上一页');
		$page->setConfig('next', '下一页');
		$data['page'] = $page->show();
		/************************************** 取数据 ******************************************/
		$data['data'] = $this->alias('a')
				->field('a.*,GROUP_CONCAT(c.pri_name) pri_name')
				->join('left join __ROLE_PRI__ b on a.id=b.role_id
						left join __PRIVILEGE__ c on b.pri_id=c.id')
				->where($where)
				->limit($page->firstRow.','.$page->listRows)
				->group('a.id')
				->select();
		return $data;
	}
	//添加后
	protected function _after_insert($data, $options)
	{
		$priId = I('post.pri_id');
		$rpModel = D('role_pri');
		foreach($priId as $k => $v){
			$rpModel->add(array(
				'pri_id' => $v,
				'role_id' => $data['id'],
			));
		}
	}

	// 添加前
	protected function _before_insert(&$data, $option)
	{
	}
	// 修改前
	protected function _before_update(&$data, $option)
	{
		/******处理拥有的权限ID******/
		$priId  = I('post.pri_id');
		$rpModel = D('role_pri');
		$rpModel->where(array(
			'role_id' => array('eq',$option['where']['id']),
		))->delete();
		foreach($priId as $v){
			$rpModel->add(array(
				'pri_id' => $v,
				'role_id' => $option['where']['id'],
			));
		}
	}
	// 删除前
	protected function _before_delete($option)
	{
		if(is_array($option['where']['id']))
		{
			$this->error = '不支持批量删除';
			return FALSE;
		}
	}
	/************************************ 其他方法 ********************************************/
}