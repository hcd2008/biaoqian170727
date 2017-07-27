<?php
namespace app\member\controller;
use app\common\controller\Member;
USE think\Db;
class Ext EXTENDS Member
{
	function __construct(){
		parent::__construct();
	}

	//用户信息详情
    public function userinfo($userid){
    	$model_member = model('member');
    	$userinfo = $model_member->getUserInfo($userid) or $this->error('用户不存在...');
        return $this->fetch('',$userinfo);
    }

    //发送项目提醒
    public function send_message($userid,$id){
        $model_member = model('member');
        $model_project = model('project');
        $model_pm = model('pm');
        if(request()->isPost()){
            $post = input('post.');
            if(!isset($post['notice_type'])){
                $this->error('请选择至少一种提醒方式',null,['layer_close'=>1]);
            }
            if(!isset($post['userid'])){
                $this->error('请选择提醒人',null,['layer_close'=>1]);
            }
            $userid = $post['userid'];
            $notice_content = addslashes($post['notice_content']);
            if(isset($post['users']) && $post['users']){
                $users = explode(',', $post['users']);
                array_unshift($users,$userid);
            }else{
                $users = [$userid];
            }
            $result = $model_pm->batchSend($users,'',$notice_content,$this->_userid);
            if($result){
                $this->success('发送完毕',null,['layer_close'=>1]);
            }else{
                $this->error('发送失败:'.$model_pm->getError(),null,['layer_close'=>1]);
            }
            exit;
        }
        $output = [];
        if($id){
           $project = $model_project->get($id);
           $output['notice_text'] = '您负责的标签评审项目《'.$project['title'].'》还未提交部门评审报告请及时提交。';
        }
        $output['userid'] = $userid;
    	return $this->fetch('',$output);
    }

    //部门人员列表
    public function dept_member_list(){
    	$model_member = model('member');
    	$DEPT = cache('dept');
    	$member_list_obj = $model_member->select();
    	foreach($member_list_obj as $row){
    		$DEPT[$row['dept']]['member_list'][] = $row->toArray();
    	}
        $output['hid'] = isset($this->GET['hid'])?$this->GET['hid']:'yxid';
        $output['sid'] = isset($this->GET['sid'])?$this->GET['sid']:'yxren';
    	$output['dept_list'] = $DEPT;

    	return $this->fetch('',$output);
    }

    //菜单提示
    public function menuRemind(){
        $menu_remind['project'] = model('project')->where(['userid'=>$this->_userid,'state'=>PROJECT_STATE_NEW])->count();
        //$menu_remind['dept_task'] = model('deptTask')->where(['task_deptid'=>$this->_dept,'task_state'=>['neq',5]])->count();
        $menu_remind['dept_task'] = Db::name('deptTask','title')->view('project','taskid','project_id=id')->where(['state'=>1,'task_state'=>['neq',5],'task_deptid'=>$this->_dept])->count();

        //$menu_remind['task'] = model('task')->where(['rev_userid'=>$this->_userid,'rev_state'=>['in',[REVIEW_STATE_NEW,REVIEW_STATE_OK]]])->count();
        $menu_remind['task'] = Db::name('task','*')->view('project','taskid','project_id=id')->where(['state'=>1,'rev_state'=>['in','1,2'],'rev_userid'=>$this->_userid])->count();
        
        $menu_remind['dept_check'] = model('deptTaskCheck')->where(['check_userid'=>$this->_userid,'check_result_state'=>0])->count();
        
        $menu_remind['archive'] = model('project')->where(['state'=>PROJECT_STATE_END,'result_state'=>PROJECT_RESULT_STATE_PASS,'archive'=>0])->count();
        return json($menu_remind);
    }
}
