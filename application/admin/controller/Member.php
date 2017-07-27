<?php
/**
 * @Author: lipeng
 * @Date:   2016-09-18 10:32:38
 * @Last Modified by:   lipeng
 * @Last Modified time: 2016-09-20 15:34:02
 */
namespace app\admin\controller;
use app\common\controller\Admin;
use think\Db;
use think\Cache;
class Member EXTENDS Admin{

	function __construct(){
		parent::__construct();
		$this->view->location = '人员管理';
		$map = [];
		if(!empty($this->kw)){
			$map['username|realname'] = ['like',"%{$this->kw}%"];
		}
		if(!empty($this->GET['depid'])){
			$map['dept'] = intval($this->GET['depid']);
		}
		$member_list = Db::view('member','*')
						->view('dept','dept_name','dept=depid')
						->where($map)
						->order('dept ASC')
						->select();
		$dept_list = Db::name('dept')
						->order('depid ASC')
						->select();
		$this->view->member_list = $member_list;
		$this->view->dept_list = $dept_list;
		$this->view->depid = !empty($this->GET['depid'])?$this->GET['depid']:'';
	}

	public function index(){
		Cache::clear();
		return $this->fetch('');
	}

	public function add(){
		if(IS_POST){
			$post = $this->GET;
			if(empty($post['role'])){
				$this->error('请选择角色...');
			}
			$post['role'] = array_sum($post['role']);
			$post['username'] or $this->error('请填用户名');
			$post['realname'] or $this->error('真实姓名');
			$post['password'] or $this->error('请填写密码');
			$post['dept'] or $this->error('请选择部门');
			$post['password'] = dpassword($post['password']);
			
			$result = model('member')->isUpdate(false)->allowField(true)->save($post);
			if($result){
				$this->success('添加成功...','member/index');
			}else{
				$this->error('添加失败...');
			}
			exit;
		}
	}

	public function edit($userid){
		$output = [];
		$user_info = Db::view('member','*')
						->view('dept','dept_name','dept=depid')
						->where(['userid'=>$userid])
						->find() or $this->error('用户不存在');
				
		if(IS_POST){
			$post = $this->GET;
			if(empty($post['role'])){
				$this->error('请选择角色...');
			}
			$post['role'] = array_sum($post['role']);
			if($post['password']){
				$post['password'] = dpassword($post['password']);
			}else{
				unset($post['password']);
			}
			
			$result = model('member')->isUpdate(true)->allowField(true)->save($post);
			$this->success('修改成功...','member/index');
			exit;
		}
		$user_info['role_array'] = role_to_array($user_info['role']);
		$output['user_info'] = $user_info;
		return $this->fetch('index',$output);
	}

	public function delete($userid){
		model('member')->destroy(['userid'=>$userid]) or $this->error('删除失败');
		$this->success('删除成功...');
	}

}
?>