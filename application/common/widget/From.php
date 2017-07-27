<?php
/**
 * 智能表单
 * @Author: lipeng
 * @Date:   2016-09-18 10:32:38
 * @Last Modified by:   lipeng
 * @Last Modified time: 2016-09-24 09:14:01
 */
namespace app\common\widget;
use think\Controller;
class From extends Controller
{
	function __construct()
	{
		parent::__construct();
		$this->view->engine->layout(false);
	}

	public function show($model,$act,$data=''){
		
		$model = model($model);
		$f = $act.'Fields';
		$this->view->model = $model;
		if(isset($model->$f) === false){
			throw new \InvalidArgumentException('智能表单异常,模型:'.get_class($model).'不存在'.$f.'属性',20000);
		}
		$fields = $model->$f;

		if(!empty($data)){
			if(is_object($data)){
				$data = $data->toArray();
			}
			foreach ($fields as $k=>$v) {
				$fields[$k]['value'] = !empty($data[$v['name']])?$data[$v['name']]:'';
			}
		}
		$this->view->fields = $fields;
		return $this->fetch('admin@common/form');
	}
}
?>