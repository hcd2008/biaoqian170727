<?php
namespace app\member\controller;
use app\common\controller\Member;
use app\common\model\Product as model_product;
use app\common\logic\project as logic_project;
use think\Db;
class Project EXTENDS Member
{
	function __construct(){
		parent::__construct();
	}

    //我的项目
    public function index($state=1,$kw=''){
        #model('project','logic')->updateProjectProgress(1);
        $map = array();
        $map['userid'] = $this->_userid;
        if(in_array($state, array(1,2,3))){
            $map['state'] = $state;
        }else{
            $map['state'] = 1;
        }
        if($kw){
            $map['xmname'] = ['like','%'.$kw.'%'];
            
        }
        $output['state'] = $state;
        $output['kw'] = $kw;
        $output['list'] = db('project')->where($map)->order('addtime DESC')->paginate(10,false,['query'=>$output]);
        #echo db('project')->getLastSql();
        return $this->fetch('',$output);
    }

    //创建与编辑项目
    public function create($id=''){
        $op = isset($this->GET['op'])?$this->GET['op']:'';
        $logic_project = model('Project','logic');
        if(IS_POST){
            $logic_project->diyEditAfter($id);
            $post = $this->GET;
            $post['userid'] = $this->_userid;
            $result = $logic_project->create($post);
            if(isset($result['state']) && $result['state'] == false){
                $this->error($result['msg']);
            }
            //插入的id
            $infoid=$result['data']['last_insert_id'];
            $version=$result['data']['version'];
            $proid=$result['data']['proid'];
            $infoid>0 or $this->error('非法访问');
            $arr['pid']=$infoid;
            $arr['version']=$version;
            $arr['proid']=$proid;
            $files=$this->request->file();
            if($files){
                foreach ($files as $file) {
                    // 移动到框架应用根目录/public/uploads/ 目录下
                    $info = $file->validate(['ext'=>'png,jpg,gif,doc,docx'])->move(ROOT_PATH . 'public' . DS . 'uploads');
                    if($info){
                        // 成功上传后 获取上传信息
                        $filename= $info->getFilename(); 
                        $filename=date('Ymd') . DS.$filename;
                        //插入图片信息
                        
                        $arr['file']=$filename;
                        $res=Db::name('project_file')->insert($arr);
                    }else{
                        // 上传失败获取错误信息
                        echo $file->getError();
                    }   
                }
            }else{
                $this->error('请上传标签文件');
            }
            
            $this->success('操作成功','member/project/index');
            exit;
        }
        if($id){
            $map = array();
            $map['userid'] = $this->_userid;
            $map['id'] = $id;
            $project = db('project')->where($map)->find() or $this->error('项目不存在...');
            $project['content'] =  db('projectData')->where(['id'=>$id])->value('content');
            $project['starttime'] = timetodate($project['starttime'],3);
            $project['endtime'] = timetodate($project['endtime'],3);
            $project['remindtime'] = timetodate($project['remindtime'],3);
            $output['project'] = $project;
        }
        //默认选中部门
        $default_depts = model('userSetting')->where(['userid'=>$this->_userid])->value('setting');
        if($default_depts){
            $default_depts = explode(',',$default_depts);
        }else{
            $default_depts = [];
        }
        //产品列表
        $product_list = model('Product')->getProductList();
        //可接收任务部门列表
        $member_list = model('member')->getTaskMemberList();
        //自定义流程
        $diy_list = model('diy')->where(['diy_status'=>0,'diy_userid'=>$this->_userid])->select();
        $output['diy_list'] = $diy_list;
        $output['default_depts'] = $default_depts;
        $output['product_list'] = $product_list;
        $output['member_list'] = $member_list;
        $output['op'] = $op;
        return $this->fetch('',$output);
    }

    //编辑项目
    public function edit($id){
        $model_project = model('project');
        $logic_project = model('Project','logic');
        $map = [];
        $map['userid'] = $this->_userid;
        $map['id'] = $id;
        $project = $model_project->where($map)->find() or $this->error('项目不存在...');
        $project = $project->toArray();
        if(IS_POST){
            $result = $logic_project->edit($this->GET);
            
            if(isset($result['state']) && $result['state'] == false){
                $this->error($result['msg']);
            }
            if($project['diy_id']>0){
                $editResult = $logic_project->diyEditAfter($id);
                $this->redirect('member/diy/start',['id'=>$id,'force'=>true]);
            }
            $this->success($result['msg'],'member/project/index');
            exit;
        }
        
        $project['content'] =  Db::name('projectData')->where(['id'=>$id])->value('content');
        $project['starttime'] = timetodate($project['starttime'],3);
        $project['endtime'] = timetodate($project['endtime'],3);
        $project['remindtime'] = timetodate($project['remindtime'],3);
        $output['project'] = $project;
        //产品列表
        $product_list = model('Product')->getProductList();
        //自定义流程
        if($project['progress_total'] == 0){
           $diy_list = model('diy')->where(['diy_status'=>0,'diy_userid'=>$this->_userid])->select();
            $output['diy_list'] = $diy_list;
        }
        $output['product_list'] = $product_list;
        return $this->fetch('',$output);
    }


    //添加评审员
    public function adduser($id){
        $model_project = model('Project');
        $model_member = model('member');
        $task_member_userids = $notice_users = $notice_users_list = $member_list = $project = [];
        $logic_project = model('Project','logic');
        if(IS_POST){
            $result = $logic_project->addUser($this->GET);
            if($result['state'] == false){
                $this->error($result['msg']);
            }
            $this->success($result['msg']);
            exit;
        }
        $project = $model_project->get($id) or $this->error('项目不存在');

        //已经收到任务的部门
        $dept_task_list = model('deptTask')->where(['project_id'=>$id])->select();
        if($dept_task_list){
            foreach ($dept_task_list as $key => $value) {
                $task_member_userids[$value['task_userid']] = $value['task_userid'];
            }
        }

        //已经收到评审任务的人员
        $task_list = model('task')->where(['project_id'=>$id])->select();
        if($task_list){
            foreach ($task_list as $key => $value) {
                $task_member_userids[$value['rev_userid']] = $value['rev_userid'];
            }
        }

        //所有部门
        $dept_group = $model_member->getDeptMemberList([
            'work_state'    =>  3
        ]);
        if($dept_group){
            foreach ($dept_group as $key => $value) {
                if(!empty($value['member_list'])){
                    foreach ($value['member_list'] as $k => $v) {
                        if(in_array($v['userid'],$task_member_userids)){
                            $dept_group[$key]['member_list'][$k]['locked'] = 1;
                        }
                    }
                }else{
                    unset($dept_group[$key]);
                }
            }
        }
        
        //所有人员
        /*
        $member_list = $model_member->getMemberList([
            'work_state'    =>  3
        ],'dept ASC');

        if($task_member_userids){
            foreach ($member_list as $key => $value) {
                if(in_array($value['userid'],$task_member_userids)){
                    $member_list[$key]['locked'] = 1;
                }else{
                    $member_list[$key]['locked'] = 0;
                }
            }
        }
        */
        $output['notice_content'] = '您有一个新标签评审任务，<br>项目名称：'.$project['title'].'，<br />开始日期：'.timetodate($project['starttime']).'<br />截止日期：'.timetodate($project['endtime']).'，<br />【'.$this->_dept_name.'】'.$this->_realname.'<br><br />请及时处理。';
        $output['project'] = $project->toArray();
        $output['dept_group_list'] = $dept_group;
        return $this->fetch('',$output);
    }

    //项目添加部门
    public function adddept($id){
        $model_project = model('Project');
        $model_member = model('member');
        $notice_users = $notice_users_list = $member_list = $project = [];
        $logic_project = model('Project','logic');
        if(IS_POST){
            $result = $logic_project->addDept($this->GET);
            if($result['state'] == false){
                $this->error($result['msg']);
            }
            $this->success($result['msg']);
            exit;
        }
        $project = $model_project->get($id) or $this->error('项目不存在');

        //已经收到任务的部门
        $dept_task_list = model('deptTask')->where(['project_id'=>$id])->select();
        if($dept_task_list){
            foreach ($dept_task_list as $key => $value) {
                $task_deptids[$value['task_deptid']] = $value['task_deptid'];
            }
        }

        $member_list = $model_member->getTaskMemberList();
        if(!empty($task_deptids)){
            foreach ($member_list as $key => $value) {
                if(in_array($value['depid'],$task_deptids) ){
                    $member_list[$key]['locked'] =1;
                }
            }
        }
        $output['notice_content'] = '您有一个新标签评审任务，<br>项目名称：'.$project['title'].'，<br />开始日期：'.timetodate($project['starttime']).'<br />截止日期：'.timetodate($project['endtime']).'，<br />【'.$this->_dept_name.'】'.$this->_realname.'<br><br />请及时处理。';
        $output['project'] = $project->toArray();
        $output['member_list'] = $member_list;
        return $this->fetch('',$output);
        
    }

    //终止项目
    public function stop($id){
        $map = array();
        $map['userid'] = $this->_userid;
        $map['id'] = $id;
        $project = db('project')->where($map)->find() or $this->error('项目不存在无法终止...');
        $result = db('project')->where($map)->update(['state' => PROJECT_STATE_STOP,'stoptime'=>TIMES]);
        $this->addActionLog('project',$id,'用户 {realname}({username}) 终止项目');
        if($result){
            $this->success('终止项目成功');
            exit;
        }
        $this->error('终止项目失败');
    }

    //项目详情
    public function show($id){
        $model_project = model('Project');
        $model_diy = model('diy');
        $project = $model_project->get(['id'=>$id]) or $this->error('项目不存在...');
        $project_detail = $project->toArray();
        if($diy_id = $project['diy_id']){//自定义流程
            $diy_conf = $model_diy->get(['diy_id'=>$diy_id]) or $this->error('自定义流程不存在...');
            $diy_content = unserialize($diy_conf['diy_content']);
            foreach ($diy_content as $k => $v) {
                $depts = $users = [];
                $depts = $v['depts'];
                $users = $v['users'];
                if($k > $project['diy_step']){
                    $diy_content[$k]['status'] = 0;
                    if($depts){
                        $diy_content[$k]['depts'] = Db::name('dept')->where(['depid'=>['in',$depts]])->select();
                       
                    }
                    if($users){
                        $diy_content[$k]['users'] = Db::name('member')->where(['userid'=>['in',$users]])->select();
                    }
                }else{
                    $diy_content[$k]['status'] = 1;
                    if($depts){
                        $diy_content[$k]['depts'] = Db::view('deptTask','*')->view('dept','dept_name','deptTask.task_deptid=dept.depid')->where(['project_id'=>$id,'task_deptid'=>['in',$depts]])->select();
                    }
                    if($users){
                        $diy_content[$k]['users'] = Db::view('task','*')->view('member','realname','task.rev_userid=member.userid')->where(['project_id'=>$id,'task.rev_userid'=>['in',$users]])->select();
                    }
                }
            }
            
            $project_detail['task_list'] = $diy_content;
        }else{
            $project_detail = $model_project->getProjectDetail($id) or $this->error('项目不存在...');
        }
        
        $output['project_detail'] = $project_detail;
        return $this->fetch('',$output);
    }

    public function review_report($id,$revid){
        $output = [];
        $model_project = model('Project');
        $model_task = model('Task');
        $model_question = model('taskQuestion');
        $project = $model_project->get(['id'=>$id]) or $this->error('项目不存在...');
        $rev_info = $model_task->getReviewInfo([
            'revid' =>  $revid,
        ]);
        $user_info = model('member')->getUserInfo($rev_info['rev_userid']);
        $question_list = $model_question->getQuestionList([
            'revid' =>  $revid,
        ]);
        $output['project'] = $project;
        $output['rev_info'] = $rev_info;
        $output['question_list'] = $question_list;
        $output['user_info'] = $user_info;
        return $this->fetch('',$output);
    }

}
