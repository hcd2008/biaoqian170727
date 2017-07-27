<?php
namespace app\member\controller;
use app\common\controller\Member;
use \think\Db;
class Diy EXTENDS Member
{
	function __construct(){
		parent::__construct();
		$this->model = model('userSetting');
        $this->model_project = model('project');
        $this->model_diy = model('diy');
        $this->logic_project = model('project','logic');
	}

    //创建自定义流程
    public function create($id){
        $output = [];

        $project = $this->model_project->where([
            'userid'    =>  $this->_userid,
            'id'        =>  $id,
        ])->find() or $this->error('项目部存在...');

        if(IS_POST){

            $diy_content = $this->GET['diy_content'];
            $diy_content = json_decode( $diy_content,true);
            $steps = $deptids = $usersids = [];
            #print_r($diy_content);exit;
            $diy_content = serialize($diy_content);
            $diy_id = $this->model_diy->save([
                'pro_id'    =>  $id,
                'diy_content'   =>  $diy_content,
            ]);

            $this->model_project->isUpdate(true)->save([
                'id'        =>  $id,
                'diy_id'    =>  $diy_id,
            ]);
            //echo $this->model_project->getLastSql();
            //exit;
            $this->success('创建自定义流程成功，请启动流程.','project/index');
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
        $output['project'] = $project;


    	return $this->fetch('',$output);
    }

    //启动自定义流程
    public function start($id,$force=false){
        $project = $this->model_project->where([
            'userid'    =>  $this->_userid,
            'id'        =>  $id,
        ])->find() or $this->error('项目部存在...');
        if($project['diy_id'] == 0){
            $this->error('该项目尚未设置自定义流程...');
        }
        if($project['progress_total']>0 && $force == false){
            $this->error('自定义流程已经启动...');
        }
        $diy = $this->model_diy->where(['diy_id'=>$project['diy_id']])->find() or $this->error('自定义流程不存在...');
        $diy_config = unserialize($diy['diy_content']);

        //总进度
        $progress_total = count($diy_config);
        $step1_conf = $diy_config[1];
        
        $depts = $users = [];
        
        $depts = $step1_conf['depts']?$step1_conf['depts']:'';
        $users = $step1_conf['users']?$step1_conf['users']:'';

        
        if(!empty($depts)){
            $result = $this->logic_project->addDept([
                'id'                =>  $project['id'],
                'depts'             =>  $depts,
                'notice_content'    =>  'ok',
            ]);
        }

        if(!empty($users)){
            $result = $this->logic_project->addUser([
                'id'                =>  $project['id'],
                'notice_users'      =>  $users,
                'notice_content'    =>  'ok',
            ]);
        }

        $this->model_project->isUpdate(true)->save([
            'id'                =>  $project['id'],
            'diy_step'          =>  1,
            'progress'          =>  0,
            'progress_total'    =>  $progress_total,
        ]);
        //print_r($diy_config);
        $this->success('启动自定义流程成功...','project/index');
    }

    public function index(){
        $output = [];
        $map['diy_status'] = 0;
        $map['diy_userid'] = $this->_userid;
        if(!empty($this->kw)){
            $map['diy_title'] = ['like',"%{$this->kw}%"];
            $output['kw'] = $this->kw;
        }
        $output['items'] = $this->model_diy->where($map)->order('diy_id ASC')->select();
        foreach ($output['items'] as $key => $value) {
          $output['items'][$key]['count'] = count(unserialize($value['diy_content']));
        }
        return $this->fetch('',$output);
    }

    public function edit($id=''){
        $output = $item = $uddata = [];
        if($id){
            $item = $this->model_diy->get(['diy_id'=>$id]) or $this->error('自定义流程不存在');
        }
        if(IS_POST){
            $diy_content = $this->GET['diy_content'];
            $diy_title = $this->GET['diy_title'];
            $diy_content = json_decode( $diy_content,true);
            $diy_content = serialize($diy_content);

            if($item){
                $project_count = model('project')->where(['diy_id'=>$id])->count();
                if($project_count>0){
                    $this->error('该流程已有项目正在使用,禁止修改。');
                }
            }
            $diy_id = $this->model_diy->isUpdate($id)->save([
                'diy_id'        =>  $id?$id:0,
                'diy_userid'    =>  $this->_userid,
                'diy_title'     =>  $diy_title,
                'diy_content'   =>  $diy_content,
            ]) or $this->error($this->model_diy->getError());
            $this->success('操作成功...','member/diy/index');
            exit;
        }
        if($item){
            $_DEPT = model('dept')->select();
            foreach ($_DEPT as $key => $value) {
                $DEPT[$value['depid']] = $value; 
            }
            $_USERS = model('member')->select();
            foreach ($_USERS as $key => $value) {
                $USERS[$value['userid']] = $value; 
            }
            unset($_DEPT,$_USERS);
            $diy_content = unserialize($item['diy_content']);
            if(!$diy_content) $diy_content = $data = [];
            foreach ($diy_content as $key => $value) {
                $html = '';
                if($value['depts']){
                    foreach ($value['depts'] as $k => $v) {
                        $uddata['depts'][$v] = $v;
                        $data[$key]['depts'][$k] = ['depid'=>$v,'dept_name'=>$DEPT[$v]['dept_name']];
                    }
                }
                if($value['users']){
                     foreach ($value['users'] as $k => $v) {
                         $uddata['users'][$v] = $v;
                        $data[$key]['users'][$k] = ['userid'=>$v,'realname'=>$USERS[$v]['realname']];
                    }
                }
            }
           # print_r( $data);
            $item['diy_content'] = $data;

        }
        //可接收任务部门列表
        $dept_list = model('member')->getTaskMemberList();
        //所有人员
        $dept_group = model('member')->getDeptMemberList([
            'work_state'    =>  3
        ]);
        $output['uddata'] = $uddata;
        $output['item'] = $item;
        $output['dept_list'] = $dept_list;
        $output['dept_group_list'] = $dept_group;
        return $this->fetch('',$output);
    }

    public function delete($id){
        $this->model_diy->where(['diy_id'=>$id,'diy_userid'=>$this->_userid])->update(['diy_status'=>1]) or $this->error('删除失败...');
        $this->success('删除成功...');
    }
    public function t1($id){
        Db::query('TRUNCATE bq_diy');
        Db::query('TRUNCATE bq_log');
        Db::query('TRUNCATE bq_pm');
        Db::query('TRUNCATE bq_dept_task');
        Db::query('TRUNCATE bq_task_question');
        Db::query('TRUNCATE bq_task');
        Db::query('TRUNCATE bq_project_review');
        Db::query('TRUNCATE bq_dept_task_check');
        Db::query('TRUNCATE bq_archive');
        Db::name('project')->where(['id'=>$id])->update([
            'state'     =>  1,
            'result_state'  =>  0,
            'diy_id'    =>  0,
            'diy_step'  =>  0,
            'progress_total'    =>  0,
            'progress'  =>  0,
        ]);
        echo 'ok1';
    }

    public function t2($id){
        Db::query('TRUNCATE bq_log');
        Db::query('TRUNCATE bq_pm');
        Db::query('TRUNCATE bq_dept_task');
        Db::query('TRUNCATE bq_task_question');
        Db::query('TRUNCATE bq_task');
        Db::query('TRUNCATE bq_project_review');
        Db::query('TRUNCATE bq_dept_task_check');
        Db::query('TRUNCATE bq_archive');
        Db::name('project')->where(['id'=>$id])->update([
            'diy_step'  =>  0,
            'progress_total'    =>  0,
            'progress'  =>  0,
        ]);
        echo Db::getLastSql();
        echo 'ok2';
    }
}
