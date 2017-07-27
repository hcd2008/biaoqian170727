<?php
namespace app\member\controller;
use app\common\controller\Base;
class Login EXTENDS Base
{
	function __construct(){
		parent::__construct();
	}

    public function index(){
    	if(request()->isPost()){
    		$post = input('post.');
    		$post['username'] or $this->error('请填写用户名');
    		$post['password'] or $this->error('请填写密码');
    		$user = db('member')->where('username',$post['username'])->find();
    		if(!$user || $user['password'] != dpassword($post['password'])){
    			$this->error('帐号或密码不正确');
    		}
			$auth = encrypt($user['userid'].'|'.$user['password'], SYS_KEY.'USER'.$_SERVER['HTTP_USER_AGENT']);
			//echo decrypt($auth,SYS_KEY.'USER'.$_SERVER['HTTP_USER_AGENT']);
    		cookie('auth', $auth , 86400*365);
    		$this->redirect('/member/label/daiban');
    		exit;
    	}
        return $this->fetch();
    }

    public function logout(){
    	cookie('auth',null);
    	$this->redirect('/member/login');
    }

}
