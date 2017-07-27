<?php
/**
 * @Author: lipeng
 * @Date:   2016-09-18 10:32:38
 * @Last Modified by:   lipeng
 * @Last Modified time: 2016-09-22 17:05:25
 */
namespace app\admin\controller;
use app\common\controller\Admin;
class Company EXTENDS Admin
{
	function __construct(){
		parent::__construct();
		$this->view->location = '企业管理';
		$this->model = model('company');
		$this->view->ctypes = explode('|','生产商|经销商|代理商');
		$map = [];
		if(!empty($this->kw)){
			$map['com_name'] = ['like',"%{$this->kw}%"];
		}
    	$items = $this->model->where($map)->paginate();
    	$this->view->items = $items;
	}

    public function index(){
    	return $this->fetch();
    }

    public function add(){

    	if(IS_POST){
    		$post = $this->GET;
    		$result = $this->model->validate(true)->allowField(true)->save($post);
    		if(!$result){
    			$this->error($this->model->getError());
    		}
    		$this->success('操作成功...','company/index');
    	}
    }

    public function edit($com_id){
    	if(IS_POST){
    		$post = $this->GET;
    		$result = $this->model->validate(true)->allowField(true)->isUpdate(true)->save($post);
    		if(!$result){
    			$this->error($this->model->getError());
    		}
    		$this->success('操作成功...','company/index');
    		exit;
    	}
    	$item = $this->model->where(['com_id'=>$com_id])->find() or $this->error('数据不存在...');
    	$this->view->item = $item;
    	return $this->fetch('index');
    }

    public function delete($com_id){
		$this->model->destroy(['com_id'=>$com_id]) or $this->error('删除失败');
		$this->success('删除成功...');
	}


}
