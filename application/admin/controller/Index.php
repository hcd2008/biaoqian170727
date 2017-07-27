<?php
/**
 * @Author: lipeng
 * @Date:   2016-09-18 10:32:38
 * @Last Modified by:   lipeng
 * @Last Modified time: 2016-09-24 16:32:00
 */
namespace app\admin\controller;
use app\common\controller\Admin;
use \think\Db;
class Index EXTENDS Admin
{
	function __construct(){
		parent::__construct();
	}

    public function index(){
    	$this->view->engine->layout(false);
    	return $this->fetch();
    }

    public function home(){
    	$this->location = '首页';
    	$map['project.state'] = 1;
    	$items = Db::view('project','*')
                            ->view('product','proname','project.product_id=product.pid','LEFT')
                            ->where($map)
                            ->order('id DESC')
                            ->select();
        foreach ($items as $key => $value) {
           $items[$key]['type'] =  empty($this->view->label_type[$value['type']])?'':$this->view->label_type[$value['type']];
        }
       
        $this->view->item = [];
    	$this->view->items = $items;
    	return $this->fetch();
    }
}
