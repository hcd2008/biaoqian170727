<?php
/**
*后台基类
*
*所有前台控制器的父类
* @author      lipeng
* @version     $Id$
*/
namespace app\common\controller;
use app\common\controller\Base;
class Admin EXTENDS Base{

	function __construct(){
		parent::__construct();
		$this->check_login();
	}

	/** 
	* 检测是否登陆
	* @access private 
	* @param
	* @return  
	*/
	private function check_login(){
		global $_G;
		$_auth = cookie('admin_auth');
		if(!$_auth){
			$this->redirect('admin/login/index');
		}
		$auth = decrypt($_auth,SYS_KEY.'USER'.$_SERVER['HTTP_USER_AGENT']);
		if($auth){
			$auth = explode('|', $auth);
			$userid = $auth[0];
			$passwd = $auth[1];
			$user = db('member')->where('userid',$userid)->find();
			if($user['admin'] !=1){
				$this->error('您没有权限访问该操作','admin/login/index');
			}
			if($user['password'] != $passwd){
				$this->redirect('admin/login/index');
			}
			$user['dept_name'] = $this->DEPT[$user['dept']]['dept_name'];
			foreach ($user as $k => $v) {
				$name = '_'.$k;
				$this->$name = $v;
				$this->view->$name = $v;
				$_G[$name] = $v;
			}
		}else{
			$this->redirect('admin/login/index');
		}
		
	}
}
?>