<?php
/**
 * 问题模型
 *
 */
namespace app\common\model;
use think\Db;
use think\Model;
class Question extends Base{

	public $modelName = '问题';

	public $insertFields = [
		['name'=>'qid','type'=>'hidden','title'=>'','help'=>'','option'=>''],
		['name'=>'type','type'=>'hidden','title'=>'','help'=>'','option'=>''],
		['name'=>'number','type'=>'text','title'=>'编号','help'=>'','option'=>''],
		['name'=>'title','type'=>'text','title'=>'名称','help'=>'','option'=>'','required'=>true],
		['name'=>'remark','type'=>'textarea','title'=>'备注','help'=>'','option'=>''],
	];
	
	//问题类型
    public function  getTypeList(){
    	$items = [];
        $_items = $this->all(['type'=>2]);
        foreach ($_items as $key => $value) {
        	$items[$value['qid']] = $value['title'];
        }
        return $items;
    }

    //区域性问题
    public function getAreaList(){
        $items = [];
        $_items = $this->all(['type'=>1]);
        foreach ($_items as $key => $value) {
        	$items[$value['qid']] = $value['title'];
        }
        return $items;
    }
}
?>