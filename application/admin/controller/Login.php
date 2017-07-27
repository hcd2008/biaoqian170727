<?php
namespace app\admin\controller;
use app\common\controller\Base;
class Login EXTENDS Base
{
	function __construct(){
		parent::__construct();
        $this->view->engine->layout(false);
	}

    public function index(){
    	if(IS_POST){
    		$post = $this->GET;
    		$post['username'] or $this->error('请填写用户名');
    		$post['password'] or $this->error('请填写密码');
    		$user = db('member')->where('username',$post['username'])->find();
    		if(!$user || $user['password'] != dpassword($post['password'])){
    			$this->error('帐号或密码不正确');
    		}
			$auth = encrypt($user['userid'].'|'.$user['password'], SYS_KEY.'USER'.$_SERVER['HTTP_USER_AGENT']);
			//echo decrypt($auth,SYS_KEY.'USER'.$_SERVER['HTTP_USER_AGENT']);
    		cookie('admin_auth', $auth , 86400*365);
    		$this->redirect('/admin/index/index');
    		exit;
    	}
        return $this->fetch();
    }

    public function logout(){
    	cookie('auth',null);
    	$this->redirect('/admin/login/index');
    }

}
