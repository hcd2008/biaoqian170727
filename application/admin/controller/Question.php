<?php
/**
 * @Author: lipeng
 * @Date:   2016-09-18 10:32:38
 * @Last Modified by:   lipeng
 * @Last Modified time: 2016-09-23 16:38:43
 */
namespace app\admin\controller;
use app\common\controller\Admin;
class Question EXTENDS Admin
{
	function __construct(){
		parent::__construct();
		$this->model = model('question');
        $type = input('type','2');
        if($type == 1){
            $this->view->location = '区域性问题';
        }else{
            $this->view->location = '问题类型';
        }
		$map = [];
        $map['type'] = $type;
		if(!empty($this->kw)){
			$map['title'] = ['like',"%{$this->kw}%"];
		}
    	$items = $this->model->where($map)->order('number asc')->paginate('','',['query'=>['type'=>$type]]);
        $this->view->item = ['type'=>$type];
    	$this->view->items = $items;

	}

    public function index(){
    	return $this->fetch();
    }

    public function add(){
        $post = $this->GET;
    	if(IS_POST){
            unset($post['qid']);
    		$result = $this->model->validate(true)->allowField(true)->save($post);
    		if(!$result){
    			$this->error($this->model->getError());
    		}
    		$this->success('操作成功...','question/index');
    	}
    }

    public function edit($qid){
    	if(IS_POST){
    		$post = $this->GET;
    		$result = $this->model->validate(true)->allowField(true)->isUpdate(true)->save($post);
    		if(!$result){
    			$this->error($this->model->getError());
    		}
    		$this->success('操作成功...','question/index');
    		exit;
    	}
    	$item = $this->model->where(['qid'=>$qid])->find() or $this->error('数据不存在...');
    	$this->view->item = $item;
    	return $this->fetch('index');
    }

    public function delete($qid){
		$this->model->destroy(['qid'=>$qid]) or $this->error('删除失败');
		$this->success('删除成功...');
	}

}
