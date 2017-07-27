<?php
/**
*会员中心基类
*
*所有会员中心控制器的父类
* @author      lipeng
* @version     $Id$
*/
namespace app\common\controller;
use app\common\controller\Base;
class Member EXTENDS Base{

	function __construct(){
		parent::__construct();
		global $_G;
		$this->check_login();
		$this->check_auth();
		
	}

	/** 
	* 检测是否登陆
	* @access private 
	* @param
	* @return  
	*/
	private function check_login(){
		global $_G;
		$_auth = cookie('auth');
		if(!$_auth){
			$this->redirect('/member/login');
		}
		$auth = decrypt($_auth,SYS_KEY.'USER'.$_SERVER['HTTP_USER_AGENT']);
		if($auth){
			$auth = explode('|', $auth);
			$userid = $auth[0];
			$passwd = $auth[1];
			$user = db('member')->where('userid',$userid)->find();
			if($user['password'] != $passwd){
				$this->redirect('/member/login');
			}
			$user['dept_name'] = $this->DEPT[$user['dept']]['dept_name'];
			foreach ($user as $k => $v) {

				$name = '_'.$k;
				$this->$name = $v;
				$this->view->$name = $v;
				$_G[$name] = $v;
			}

		}else{
			$this->redirect('/member/login');
		}
		
	}

	private function check_auth(){
		$c = $this->view->_c;
		$a = $this->view->_a;
		$role = $this->_role;

		//1 2 4
		$role_rule[1] = [
			'project'	=>	true,
			'archive'	=>	true,
			'setting'	=>	true,
			'diy'		=>	true,
			'review'	=>	true,
			'jiancha'   => true,
		];

		$role_rule[2] = [
			'task'		=>	true,
			'review'	=>	true,
			'jiancha'   => true,
		];

		$role_rule[4] = [
			'dept_task'	=>	true,
			'dept_job'	=>	true,
			'review'	=>	true,
			'jiancha'   => true,
		];

		switch ($role) {
			case '1':
				$auths = $role_rule[$role];
				break;
			case '2':
				$auths = $role_rule[$role];
				break;
			case '4':
				$auths = $role_rule[$role];
				break;
			case '3':
				$auths = array_merge($role_rule[1],$role_rule[2]);
				break;
			case '5':
				$auths = array_merge($role_rule[1],$role_rule[4]);
				break;
			case '6':
				$auths = array_merge($role_rule[2],$role_rule[4]);
				break;
			case '7':
				$auths = array_merge($role_rule[1],$role_rule[2],$role_rule[4]);
				break;
			
			default:
				$auths = [];
				break;
		}

		$this->assign('auths',$auths);
		//允许控制器
		$allow_controller = [
			'index'		=>	true,
			'label'		=>	true,
			'message'	=>	true,
			'ext'		=>	true,
			'login'		=>	true,
		];
		
		if(isset($allow_controller[$c]) && $allow_controller[$c] === true){
			return true;
		}

		//允许操作
		$allow_action = [
			'project'	=>	[
				'show'	=>	true,
			]
		];
		if(isset($allow_action[$c][$a]) && $allow_action[$c][$a] === true){
			return true;
		}

		if(isset($auths[$c]) && $auths[$c] ===true){
			return true;
		}
		$this->error('您没有权限访问该操作...');

	}

	protected function addActionLog($type,$typeid='',$message=''){
		if(!$type) return ;
		$data['type'] = $type;
		$data['typeid'] = $typeid;
		$data['action_url'] = $_SERVER['REQUEST_URI'];
		$data['userid'] = $this->_userid;
		$data['message'] = str_replace(array('{username}','{realname}'),array($this->_username,$this->_realname),$message);
		$data['dateline'] = TIMES;
		return  db('log')->insert($data);
	}
}
?>