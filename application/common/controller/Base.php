<?php
/**
*基类、父类
*
*所有控制器的父类
* @author      lipeng
* @version     $Id$
*/
namespace app\common\controller;
use think\Controller;
use think\Db;
class Base EXTENDS Controller{
	function __construct(){
		parent::__construct();
		$this->_init_var();
		$this->_init_constant();
		$this->_init_cache();
		$label_type = explode('|', '|'.$this->view->ST['project_type']);
		unset($label_type[0]);
		$label_size_unit = config('label_size_unit');
        $notice_type = config('notice_type');
        $roles = config('roles');
        $this->assign([
            'label_type'        =>  $label_type,
            'label_size_unit'   =>  $label_size_unit,
            'notice_type_list'  =>  $notice_type,
            'roles'				=>	$roles,
        ]);
	}
	function _initialize(){
		// Db::query("set global sql_mode=''");
	}

	/** 
	* 初始化变量
	* @access private 
	* @param
	* @return  
	*/
	private function _init_var(){
		$this->view->_m = $this->request->module();
		$this->view->_c = $this->request->controller();
		$this->view->_a = $this->request->action();
		
		$this->GET = input('param.');
		if(isset($this->GET['kw'])){
			$this->kw = addslashes($this->GET['kw']);
		}
	}

	/** 
	* 初始化常量
	* @access private 
	* @param
	* @return  
	*/
	private function _init_constant(){

		define('TIMES',$_SERVER['REQUEST_TIME']);
		define('SYS_KEY',config('sys_key'));
		define('IS_POST',($this->request->method()=='POST')?true:false);
		define('SYS_DOMAIN',$this->request->domain());

		define('PROJECT_STATE_NEW',1);  //我的项目：进行中
		define('PROJECT_STATE_STOP',2); //我的项目：终止项目
		define('PROJECT_STATE_END',3);  //我的项目：完成

		define('PROJECT_RESULT_STATE_REJECT',10);  //我的项目评审状态：拒绝
		define('PROJECT_RESULT_STATE_PASS',20);  //我的项目评审状态：通过



		define('TASK_STATE_NEW',1);	//任务：等待分配
		define('TASK_STATE_SEND',2);	//任务:评审中
		define('TASK_STATE_REWIEW',3);	//任务：评审完毕
		define('TASK_STATE_SUM',4);	//任务：完成部门汇总
		define('TASK_STATE_REPORT',5);//任务：上报部门
		define('TASK_STATE_APPROVAL',6);//任务：领导审批
		define('TASK_STATE_APPROVAL_OK',7);//任务：领导审批OK
		define('TASK_STATE_APPROVAL_NO',8);//任务：领导审批NO
		define('TASK_STATE_SUCCESS',9);//任务：完成环节
		

		define('TASK_RESULT_STATE_REJECT',10);	//部门任务评审结果:拒绝
		define('TASK_RESULT_STATE_PASS',20);	//部门任务评审结果:通过


		define('REVIEW_STATE_NEW',1);		//评审：新的评审
		define('REVIEW_STATE_OK',2);		//评审：已经评审
		define('REVIEW_STATE_REPORT',3);	//评审：已经提交评审报告

		define('REVIEW_RESULT_STATE_REJECT',10);	//评审：拒绝通过
		define('REVIEW_RESULT_STATE_PASS',20);	//评审：通过审核

		define('CHECK_RESULT_STATE_REJECT',10);	//领导审批：拒绝
		define('CHECK_RESULT_STATE_PASS',20);	//领导审批：通过

		define('PM_STATE_UNREAD',1);
		define('PM_STATE_READED',3);

	}

	/** 
	* 初始缓存
	* @access public 
	* @param
	* @return  
	*/
	public function _init_cache(){
		$this->DEPT = $this->_cache_dept();
		$this->view->ST = $this->_cache_setting();
		//$this->_cache_product();
	}

	//缓存系统设置
	public function _cache_setting($force=false){
		$setting = cache('setting');
		if(!$setting || $force){
			$setting = $this->getSetting('system');
			cache('setting',$setting);
		}
		return $setting;
	}


	//缓存部门
	public function _cache_dept($force=false){
		$dept = cache('dept');
		if(!$dept || $force){
			$_dept = db('dept')->select();
			foreach ($_dept as $k => $v) {
				$dept[$v['depid']] = $v;
			}
			cache('dept',$dept);
			unset($_dept);
		}
		return $dept;
	}

	//缓存产品
	public function _cache_product($force=false){
		$product = cache('product');
		if(!$product || $force){
			$_product = db('product')->field('pid,proname')->select();
			foreach ($_product as $k => $v) {
				$product[$v['pid']] = $v;
			}
			cache('product',$product);
			unset($_product);
		}
		return $product;
	}

	//更新设置
	public function updateSetting($item,$post){
    	Db::name('setting')->where(['item'=>$item])->delete();
    	foreach ($post as $item_key => $item_value) {
    		$data[] = [
    			'item'	=>	$item,
    			'item_key'	=>	$item_key,
    			'item_value'	=>	$item_value
    		]; 
    	}
    	Db::name('setting')->insertAll($data);
    	$this->_cache_setting(true);
    }

    //获取设置
    public function getSetting($item){
    	$list = [];
    	$_list = Db::name('setting')->where(['item'=>$item])->select();
    	foreach ($_list as $key => $value) {
    		$list[$value['item_key']] = $value['item_value'];
    	}
    	return $list;
    }


}
?>