<?php
/**
 * @Author: lipeng
 * @Date:   2016-09-18 10:32:38
 * @Last Modified by:   lipeng
 * @Last Modified time: 2016-09-18 15:18:24
 */
namespace app\admin\controller;
use app\common\controller\Admin;
use think\Db;
class Setting EXTENDS Admin
{
	function __construct(){
		parent::__construct();
		$this->view->location = '系统设置';
	}

    public function base(){
    	$this->view->location = '基本设置';
    	if(IS_POST){
    		$this->updateSetting('system',$this->GET['setting']);
    		$this->success('操作成功...');
    		exit;
    	}
    	$this->view->setting = $this->getSetting('system');
    	return $this->fetch();
    }

    


}
