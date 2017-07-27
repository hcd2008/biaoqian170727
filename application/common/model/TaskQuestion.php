<?php
/**
 * 评审问题模型
 *
 */
namespace app\common\model;
use think\Model;
use think\Db;
class TaskQuestion extends Model{
	protected $auto = [];
    protected $insert = ['qs_addtime'=>TIMES]; 
    protected $update = [];

    /** 
	* 评审问题信息列表
	* @access public 
	* @param array $map 条件
	* @param string $order 排序
	* @return  mixed
	*/
    public function getQuestionList($map=[],$order='qsid ASC'){
        $model_question = model('question');
    	$question_type = $model_question->getTypeList();
    	$question_area = $model_question->getAreaList();
    	$question_list = $this->all($map);
    	foreach ($question_list as $k => $v) {
    		$v['question_type'] = $question_type[$v['qs_typeid']];
    		$v['question_area'] = $question_area[$v['qs_areaid']];
    		$question_list[$k] = $v->toArray();
    	}
    	return $question_list;
    }
}
?>