<?php
namespace app\member\controller;
use app\common\controller\Member;
use think\Db;
class DeptTask EXTENDS Member
{
	function __construct(){
		parent::__construct();
        $this->model_task = model('deptTask');
        $this->logic_task = model('Task','logic');
        $this->logic_project = model('project','logic');
	}

    //我的任务
    public function index(){
        $map =  [];
        if(isset($this->GET['state']) && $this->GET['state'] == 3){
            $map['task_state'] = TASK_STATE_REPORT;
        }else{
            $this->GET['state'] = 1;
            $map['task_state'] = ['neq',TASK_STATE_REPORT];
        }
        if(!empty($this->kw)){
            $map['title'] = ['like',"%{$this->kw}%"];
        }
        $map['task_deptid'] = $this->_dept;
        $task_list = $this->model_task->getTaskList($map);
        #echo Db::getLastSql();
        #print_r($task_list);exit;
        $output['task_list'] = $task_list;
        $output['state'] = $this->GET['state'];
        
        return $this->fetch('',$output);
    }

    //分配任务
    public function step1($taskid){
        $logic_project = model('project','logic');
        $model_member = model('Member');
        $model_task = model('task');
        $project = $this->getTaskInfo($taskid);
        if($project['task_state'] != TASK_STATE_NEW){
            $this->error('该任务已经分配过了...');
        }
        if(IS_POST){
            $result = $logic_project->addMemberTask($project,$this->GET);
            if($result['state'] == true){
                $this->success('分配任务成功',url('member/deptTask/index'));
            }else{
                $this->error($result['msg']);
            }
            exit;
        }
        $task_list = $model_task->where(['project_id' => $project['id']])->select();
        if($task_list){
            foreach ($task_list as $k => $v) {
                $task_list_users[$v['rev_userid']] = $v['rev_userid'];
            }
        }
        $member_list = $model_member->all(['dept'=>$this->_dept]);
        if(!empty($task_list_users)){
            foreach ($member_list as $key => $value) {
                if(in_array($value['userid'],$task_list_users)){
                    $member_list[$key]['locked'] = 1;
                }
            }
        }
        $output['project'] = $project;
        $output['member_list'] = $member_list;
        return $this->fetch('',$output);
    }

    //汇总部门意见
    public function step2($taskid){
        $output = [];
        $task_info = $this->getTaskInfo($taskid);
        $result = $this->logic_task->check_step(2,$task_info);
        if(!$result['state']){
            $this->error($result['msg']);
        }
        $review_list = model('task')->getReviewState(['dept_taskid'=>$taskid]);
        $output['task_info'] = $task_info;
        $output['review_list'] = $review_list;
        return $this->fetch('',$output);
    }

    //汇总
    public function step3($taskid){
        $output = [];
        $task_info = $this->getTaskInfo($taskid);
        $result = $this->logic_task->check_step(2,$task_info);
        if(!$result['state']){
            $this->error($result['msg']);
        }
        if(IS_POST){
            isset($this->GET['task_result_state']) or $this->error('请选择部门评审结果');
           $task_result_state = ($this->GET['task_result_state'] ==1)?TASK_RESULT_STATE_PASS:TASK_RESULT_STATE_REJECT;
           $this->model_task->isUpdate(true)->save([
                'task_state'        =>  TASK_STATE_SUM,
                'task_result_state' =>  $task_result_state,
                'taskid'            =>  $taskid,
            ]);
           if($this->model_task->getError()){
                $this->error($this->model_task->getError());
           }else{
                $this->success('汇总成功','member/deptTask/index');
                exit;
           }
        }
        $review_list = model('task')->getReviewState(['dept_taskid'=>$taskid]);
        $output['task_info'] = $task_info;
        $output['review_list'] = $review_list;
        $output['taskid'] = $taskid;
        return $this->fetch('',$output);
    }

    //上报部门意见
    public function step4($taskid){
        $output = [];
        $task_info = $this->getTaskInfo($taskid);
        $result = $this->logic_task->check_step(4,$task_info);
        if(!$result['state']){
            $this->error($result['msg']);
        }
        if(IS_POST){
            $result = $this->logic_project->deptTaskReport($this->GET);
            if($result['state']){
                $this->success($result['msg'],'member/deptTask/index');
            }else{
                $this->error($result['msg']);
            }
           exit;
        }
        if($task_info['task_check']){
           $task_info['check_result_state'] = Db::name('deptTaskCheck')->where(['tid'=>$task_info['taskid']])->value('check_result_state');
        }
        $review_list = model('task')->getReviewState(['dept_taskid'=>$taskid]);
        $output['task_info'] = $task_info;
        $output['review_list'] = $review_list;
        return $this->fetch('',$output);
    }

    //任务项目信息
    public function show($taskid){
        $model_task = model('projectTask');
        $model_review = model('task');
        $task = $this->getTaskInfo($taskid);
        $output['review_list'] = $model_review->getReviewState([
            'dept_taskid'=>$task['taskid']
        ]);
        $output['task'] = $task;
        return $this->fetch('',$output);
    }

    private function getTaskInfo($taskid){
        $item = $this->model_task->getTaskInfo([
            'taskid'    =>  $taskid,
        ]) ;
        if($item){
            return $item;
        }
        $this->error('任务不存在');
    }
}
