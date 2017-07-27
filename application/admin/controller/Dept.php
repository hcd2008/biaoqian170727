<?php
/**
 * @Author: lipeng
 * @Date:   2016-09-18 10:32:38
 * @Last Modified by:   lipeng
 * @Last Modified time: 2016-09-18 17:07:35
 */
namespace app\admin\controller;
use app\common\controller\Admin;
use think\Db;
use think\Cache;
class Dept EXTENDS Admin{

	function __construct(){
		parent::__construct();
		$this->view->location = '部门管理';
		$map = [];
		$DEPT = cache('dept');
		if(!empty($this->kw)){
			$map['dept.dept_name'] = ['like',"%{$this->kw}%"];
		}
		$dept_list = Db::view('dept','*')
						->view('member',['realname'=>'dept_manager'],'dept_muid=userid','LEFT')
						->where($map)
						->order('dept_level ASC,depid ASC')
						->select();
		foreach ($dept_list as $key => $value) {
			if($dept_list[$key]['dept_parentid']){
				$dept_list[$key]['dept_parent_manager'] = $DEPT[$value['dept_parentid']]['dept_name'];
			}else{
				$dept_list[$key]['dept_parent_manager'] = '';
			}
		}
		#print_r($dept_list);exit;
		$this->view->dept_list = $dept_list;
	}

	public function index(){
		//清理下部门缓存
		Cache::clear(); 
		return $this->fetch('');
	}

	public function add(){
		if(IS_POST){
			$post = $this->GET;
			$post['dept_name'] or $this->error('请填部门名称');
			$result = model('dept')->isUpdate(false)->allowField(true)->save($post);
			if($result){
				$this->success('添加成功...','dept/index');
			}else{
				$this->error('添加失败...');
			}
			exit;
		}
		$fields = model('dept')->getTableInfo('', 'fields');
		foreach($fields as $v){
			$dept_info[$v] = '';
		}
		$output['dept_info'] = $dept_info;
		return $this->fetch('edit',$output);
	}

	public function edit($depid){
		$output = [];
		$dept_info = Db::name('dept')
						->where(['depid'=>$depid])
						->find() or $this->error('部门不存在...');
		if(IS_POST){
			$post = $this->GET;
			if(empty($post['dept_muid'])){
				//$this->error('请选择部门主管');
			}
			model('dept')->isUpdate(true)->allowField(true)->save($post);
			$this->success('修改成功...','dept/index');
			exit;
		}
		$member_list = Db::view('member','*')
						->view('dept','dept_name','dept=depid')
						->where(['dept'=>$depid])
						->order('userid ASC')
						->select();
		
		$output['member_list'] = $member_list;
		$output['dept_info'] = $dept_info;
		return $this->fetch('index',$output);
	}

	public function delete($depid){
		model('dept')->destroy(['depid'=>$depid]) or $this->error('删除失败');
		$this->success('删除成功...');
	}

}
?>