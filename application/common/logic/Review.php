<?php 
namespace app\common\logic;
class Review {

	public function allot($userid,$post){
		$model_project = model('project');
		$model_review = model('review');
		$model_task = model('task');
		$project_id = $post['project_id'];
		
		if(!$project_id){
			return callback(false,'项目不存在(1)');
		}

		$project = $model_project->get($project_id);
		
		if(!$project_id){
			return callback(false,'项目不存在(2)');
		}

		if(!$project['state'] != TASK_STATE_NEW){
			return callback(false,'改项目已经分配过了');
		}

		$task_data = $review_data = [];
		$task_data['state'] = TASK_WAIT_REVIEW;
		

		if(isset($post['notice_users']) && is_array($post['notice_users'])){
			$task_data['notice_users'] = implode(',', $post['notice_users']);
		}

		if(isset($post['notice_type']) && is_array($post['notice_type'])){
			$task_data['notice_type'] = implode(',', $post['notice_type']);
		}

		foreach ($post['notice_users'] as $k=>$to_userid) {
			$review_data[$k]['state'] = REVIEW_STATE_NEW;
			$review_data[$k]['project_id'] = $project_id;
			$review_data[$k]['version'] = $project['version'];
			$review_data[$k]['userid'] = $to_userid;
			$review_data[$k]['from_userid'] = $userid;
		}
		$result = $model_review->allowField(true)->saveAll($review_data);
		if($result){
			$model_task->allowField(true)->save($task_data,['project_id'=>$project_id,'userid'=>$userid]);
		}else{
			return callback(false,$model_review->getError());
		}
		return callback(true);

	}

	//检查评审行为
	public function check_step($step,$item){
		switch ($step) {
			case '1'://是否可以评审
				if(!in_array($item['rev_state'], array(REVIEW_STATE_NEW,REVIEW_STATE_OK))){
					return callback(false,'该任务状态不正确，不能进行评审 1');
				}
				break;

			case '2'://是否可以提交评审结果
				if(!in_array($item['rev_state'], array(REVIEW_STATE_NEW,REVIEW_STATE_OK))){
					return callback(false,'该任务状态不正确，不能提交评审结果 2');
				}
				break;
			case '3'://是否可以提交报告
				if($item['rev_state'] != REVIEW_STATE_OK){
					return callback(false,'该任务状态不正确，不能提交评审报告 3');
				}
				break;
			case 'question_add'://是否可以提交评审问题
				if($item['rev_finished'] != 0){
					return callback(false,'该任务状态不正确，不能提交评审问题');
				}
				break;
			case 'question_edit'://是否可以编辑评审问题
				if($item['rev_finished'] != 0){
					return callback(false,'该任务状态不正确，不能编辑评审问题');
				}
				break;
			case 'question_delete'://是否可以删除评审问题
				if($item['rev_finished'] != 0){
					return callback(false,'该任务状态不正确，不能删除评审问题');
				}
				break;
			default:
				
				break;
		}
		return callback(true);
	}

}
?>