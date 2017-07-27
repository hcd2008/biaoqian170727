<?php
namespace app\member\controller;
use app\common\controller\Member;
class DeptJob EXTENDS Member
{
	function __construct(){
		parent::__construct();
	}

    public function index(){
    	$model_check = model('deptTaskCheck');
    	$output = [];
    	$check_list = $model_check->getCheckList([
    		'check_userid'	=>	$this->_userid,
    	]);
    	
    	$output['check_list'] = $check_list;
    	#echo $model_check->getLastSql();
    	#print_r($output);
        return $this->fetch('',$output);
    }

    public function show($checkid){
    	$model_check = model('deptTaskCheck');
    	$model_task = model('deptTask');
    	$model_review = model('task');
    	$logic_project = model('project','logic');
    	//项目信息
    	$project = $model_check->getCheckInfo([
    		'check_userid'	=>	$this->_userid,
    		'checkid'		=>	$checkid,
    	]) or $this->error('项目不存在....');
    	if(IS_POST){

    		$this->GET['taskid'] = $project['taskid'];
    		$result = $logic_project->task_check($this->GET);
    		if($result['state']){
    			$this->success($result['msg']);
    		}else{
    			$this->error($result['msg']);
    		}
    		exit;
    	}

    	$taskid = $project['taskid'];
    	//部门评审报告
    	$task_info = $model_task->getTaskInfo([
    		'taskid'	=>	$taskid
    	]);
    	config('paginate.list_rows',100);
    	//员工评审报告
    	$review_list = $model_review->getReviewList([
    		'dept_taskid'	=>	$taskid
    	]);
        #print_r($review_list);
    	#echo $model_review->getLastSql();
    	#print_r($review_list);
    	$output['project']	= $project;
    	$output['task_info'] = $task_info;
    	$output['review_list'] = $review_list;
    	#echo  $model_check->getLastSql();
    	#print_r($output);
        return $this->fetch('',$output);
    }
}
