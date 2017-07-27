<?php
/**
 * @Author: lipeng
 * @Date:   2016-09-18 10:32:38
 * @Last Modified by:   lipeng
 * @Last Modified time: 2016-09-22 14:18:30
 */
namespace app\admin\controller;
use app\common\controller\Admin;
class Category EXTENDS Admin
{
	function __construct(){
		parent::__construct();
		$this->view->location = '分类管理';
		$this->model = model('category');
		$map = [];
		if(!empty($this->kw)){
			$map['catname'] = ['like',"%{$this->kw}%"];
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
            if($post['listorder']==''){
                $post['listorder']=0;
            }
    		$result = $this->model->validate(true)->allowField(true)->save($post);
    		if(!$result){
    			$this->error($this->model->getError());
    		}
    		$this->success('操作成功...','category/index');
    	}
    }

    public function edit($catid){
    	if(IS_POST){
    		$post = $this->GET;
            if($post['listorder']==''){
                $post['listorder']=0;
            }
    		$result = $this->model->validate(true)->allowField(true)->isUpdate(true)->save($post);
    		if(!$result){
    			$this->error($this->model->getError());
    		}
    		$this->success('操作成功...','category/index');
    		exit;
    	}
    	$item = $this->model->where(['catid'=>$catid])->find() or $this->error('数据不存在...');
    	$this->view->item = $item;
    	return $this->fetch('index');
    }

    public function delete($catid){
		$this->model->destroy(['catid'=>$catid]) or $this->error('删除失败');
		$this->success('删除成功...');
	}

}
