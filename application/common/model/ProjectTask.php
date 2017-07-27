<?php
/**
 * 任务模型
 *
 */
namespace app\common\model;
use think\Model;
use think\Db;
class ProjectTask extends Model{
	protected $auto = [];
    protected $insert = ['task_addtime'=>TIMES,'task_state'=>TASK_STATE_NEW];
    protected $update = ['task_edittime'=>TIMES];

    //任务列表
    public function getTaskList($map = [],$order = 'taskid DESC'){

    	$task_list = Db::view('projectTask','*')
                        ->view('project','*','pro_id=id')
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
    	$task = Db::view('projectTask','*')
                        ->view('project','*','pro_id=id')
                        ->view('member','realname as task_realname','projectTask.task_userid=member.userid')
                        ->where($map)
                        ->find();
    	return $task;
    }
}
?>