<?php
/**
 * 领导审批模型
 *
 */
namespace app\common\model;
use think\Model;
use think\Db;
class DeptTaskCheck extends Model{
	protected $auto = [];
    protected $insert = ['check_addtime'=>TIMES]; 
    protected $update = [];

    //审批列表
    public function getCheckList($map = [],$order = 'check_addtime DESC'){

        $check_list = Db::view('deptTaskCheck','*')
                        ->view('deptTask','*','deptTaskCheck.tid=deptTask.taskid')
                        ->view('project','*','deptTask.project_id=project.id')
                        ->view('member','realname','deptTaskCheck.check_from_userid=member.userid')
                        ->where($map)
                        ->order($order)
                        ->paginate();
        return $check_list;
    }

    public function getCheckInfo($map = []){
        $check_info = Db::view('deptTaskCheck','*')
                        ->view('deptTask','*','deptTaskCheck.tid=deptTask.taskid')
                        ->view('project','*','deptTask.project_id=project.id')
                        ->view('member','realname as check_realname','deptTaskCheck.check_from_userid=member.userid')
                        ->where($map)
                        ->find();
        return $check_info;
    }

}
?>