<?php
namespace app\member\controller;
use app\common\controller\Member;
use \think\Db;
class Setting EXTENDS Member
{
	function __construct(){
		parent::__construct();
		$this->model = model('userSetting');
	}

    public function index(){
    	if(IS_POST){
    		if( empty($this->GET['users'])){
    			$users = '';
    		}else{
    			$users = implode(',',$this->GET['users']);
    		}
    		$this->model->save([
    			'userid'	=>	$this->_userid,
    			'setting'	=>	$users,
    		],true);
    		$this->success('设置成功...');
    		exit;
    	}
    	$users = $this->model->where(['userid'=>$this->_userid])->value('setting');
    	if($users){
    		$users = explode(',',$users);
    	}else{
    		$users = [];
    	}
    	//可接收任务部门列表
        $member_list = model('member')->getTaskMemberList();
        $output['member_list'] = $member_list;
        $output['users'] = $users;
        return $this->fetch('',$output);
    }

    //自定义流程设置
    public function diy(){
        $model_diy = model('diy');
        $output = [];

        if(IS_POST){
            $diy_content = $this->GET['diy_content'];
            $diy_title = $this->GET['diy_title'];
            $diy_content = json_decode( $diy_content,true);
            $diy_content = serialize($diy_content);
            $diy_id = $model_diy->save([
                'diy_userid'    =>  $this->_userid,
                'diy_title'     =>  $diy_title,
                'diy_content'   =>  $diy_content,
            ]);
            exit;
        }

        //可接收任务部门列表
        $dept_list = model('member')->getTaskMemberList();

        //所有人员
        $dept_group = model('member')->getDeptMemberList([
            'work_state'    =>  3
        ]);

        $output['dept_list'] = $dept_list;
        $output['dept_group_list'] = $dept_group;
        return $this->fetch('',$output);
    }
}
