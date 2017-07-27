<?php
/**
 * 部门任务模型
 *
 */
namespace app\common\model;
use think\Model;
use think\Db;
class DeptTask extends Model{
	protected $auto = [];
    protected $insert = ['task_addtime'=>TIMES,'task_state'=>TASK_STATE_NEW];
    protected $update = ['task_edittime'=>TIMES];

    //任务列表
    public function getTaskList($map = [],$order = 'taskid DESC'){
        $map['state'] = ['neq',PROJECT_STATE_STOP];
    	$task_list = Db::view('deptTask','*')
                        ->view('project','*','project_id=id')
                        ->where($map)
                        ->order($order)
                        ->paginate();
        return $task_list;
    }

    //任务详情
    public function getTaskInfo($map=[]){
        if(is_numeric($map)){
           $map = ['taskid' => $map];
        }
    	$task = Db::view('deptTask','*')
                        ->view('project','*','project_id=id')
                        //->view('member','realname as task_realname','deptTask.task_userid=member.userid')
                        ->where($map)
                        ->find();
    	return $task;
    }
}
?>