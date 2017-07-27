<?php 
namespace app\common\logic;
class Task {

	//检查任务行为
	public function check_step($step,$item){
		switch ($step) {
			case '2'://是否可以汇总部门意见
				if(!in_array($item['task_state'], array(TASK_STATE_REWIEW,TASK_STATE_SUM))){
					return callback(false,'该任务状态不正确，不能汇总部门意见');
				}
				break;
			case '3'://是否可以提交报告
				if(!in_array($item['task_state'], array(REVIEW_STATE_REJECT,REVIEW_STATE_PASS))){
					return callback(false,'该任务状态不正确，不能提交评审报告');
				}
				break;
		}
		return callback(true);
	}

}
?>