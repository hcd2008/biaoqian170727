<?php
/**
 * @Author: lipeng
 * @Date:   2016-09-18 10:32:38
 * @Last Modified by:   lipeng
 * @Last Modified time: 2016-09-22 17:17:17
 */
namespace app\admin\controller;
use app\common\controller\Admin;
class Product EXTENDS Admin
{
	function __construct(){
		parent::__construct();
		$this->view->location = '分类管理';
		$this->model = model('product');
		$map = [];
		if(!empty($this->kw)){
			$map['proname'] = ['like',"%{$this->kw}%"];
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
    		$this->success('操作成功...','product/index');
    	}
    }

    public function edit($pid){
    	if(IS_POST){
    		$post = $this->GET;
    		$result = $this->model->validate(true)->allowField(true)->isUpdate(true)->save($post);
    		if(!$result){
    			$this->error($this->model->getError());
    		}
    		$this->success('操作成功...','product/index');
    		exit;
    	}
    	$item = $this->model->where(['pid'=>$pid])->find() or $this->error('数据不存在...');
    	$this->view->item = $item;
    	return $this->fetch('index');
    }

    public function delete($pid){
		$this->model->destroy(['pid'=>$pid]) or $this->error('删除失败');
		$this->success('删除成功...');
	}

}
