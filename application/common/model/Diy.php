<?php
/**
 * 自定义流程模型
 *
 */
namespace app\common\model;
use think\Model;
use think\Db;
class Diy extends Model{
	protected $insert = ['diy_addtime'=>TIMES];  

	//Diy流程详情
	public function diyInfo($id){
		$project = model('project')->get($id); 
		if(!$project)return false;
		$project  = $project->toArray();
		$diy_conf = $this->get(['diy_id'=>$project['diy_id']]);
		$diy_content = unserialize($diy_conf['diy_content']);
		if(!$diy_content){return false;}
        foreach ($diy_content as $k => $v) {
            $depts = $users = [];
            $depts = $v['depts'];
            $users = $v['users'];
            if($k > $project['diy_step']){
                $diy_content[$k]['status'] = 0;
                if($depts){
                    $diy_content[$k]['depts'] = Db::name('dept')->where(['depid'=>['in',$depts]])->select();
                    foreach ($diy_content[$k]['depts'] as $kk => $vv) {
                        $info=Db::name('task')->where('project_id',$vv['project_id'])->where('dept_taskid',$vv['taskid'])->select();
                        $diy_content[$k]['depts'][$kk]['member']=$info;
                    }
                }
                if($users){
                    $diy_content[$k]['users'] = Db::name('member')->where(['userid'=>['in',$users]])->select();
                }
            }else{
                $diy_content[$k]['status'] = 1;
                if($depts){
                    $diy_content[$k]['depts'] = Db::view('deptTask','*')->view('dept','dept_name','deptTask.task_deptid=dept.depid')->where(['project_id'=>$id,'task_deptid'=>['in',$depts]])->select();
                    foreach ($diy_content[$k]['depts'] as $kk => $vv) {
                        $info=array();
                        $info=Db::name('task')->alias('a')->join('bq_member b','a.rev_userid=b.userid')->field('a.revid,a.rev_userid,b.realname')->where('a.project_id',$vv['project_id'])->where('a.dept_taskid',$vv['taskid'])->select();
                        $diy_content[$k]['depts'][$kk]['member']=$info;
                        
                    }  
                }       
                if($users){
                    $diy_content[$k]['users'] = Db::view('task','*')->view('member','realname','task.rev_userid=member.userid')->where(['project_id'=>$id,'task.rev_userid'=>['in',$users]])->select();
                }
            }
        }
        return $diy_content;

	}
}
?>