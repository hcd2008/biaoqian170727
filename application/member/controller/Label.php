<?php
namespace app\member\controller;
use app\common\controller\Member;
use think\Db;
class Label EXTENDS Member
{
	function __construct(){
		parent::__construct();
		$this->model_project = model('project');
		$this->model_archive = model('archive');
        $this->model_task = model('deptTask');
        $this->task = model('Task');
        $this->model_diy = model('diy');
	}

    public function index(){
        $map = [];
        $map['arc_replace'] = 0;
        if(!empty($this->kw)){
            $map['arc_title'] = ['like',"%{$this->kw}%"];
        }
    	$project_list = Db::view('archive','*')
                          ->view('project','*','project_id=id')
                          ->where($map)
                          ->order('arc_addtime DESC')//原来是archive
                          ->paginate(15);
        $output['project_list'] = $project_list;
        return $this->fetch('',$output);
    }
    //代办事项
    public function daiban(){
        $U=db('member')->where('userid',$this->_userid)->find();
        $output['U']=$U;
        //代办项目
        $map = array();
        $map['userid'] = $this->_userid;
        $map['state']=1;
        $output['state'] = $state;
        $output['kw'] = $kw;
        $output['xms'] = db('project')->where($map)->order('addtime DESC')->limit(7)->select();
        //部门任务
        $map =  [];
        $map['task_deptid'] = $this->_dept;
        $map['task_state'] = ['neq',TASK_STATE_REPORT];
        $task_list = $this->model_task->getTaskList($map);
        $output['task_list']=$task_list;
        // print_r($task_list);
        //待评审标签
        $map = [];
        $map['rev_userid'] = $this->_userid;
        $map['state']=1;
        $map['rev_state'] = ['in',[REVIEW_STATE_NEW,REVIEW_STATE_OK]];
        $review_list = $this->task->getReviewList($map);
        // print_r($review_list);
        $output['review_list'] = $review_list;
        return $this->fetch('',$output);
    }

    //下载标签
    public function down($arcid){
    	$arc_info = Db::view('archive','*')
                          ->view('project','*','project_id=id')
                          ->where(['arcid' => $arcid])->find() or $this->error('该项目未归档...');
        $bqfile = $arc_info['bqfile'];
        $parse = parse_url($bqfile);
        $file_path = $parse['path'];
        $file_path = ROOT_PATH.trim(str_replace(array('/','\\'),DS,$file_path),DS);
        if(!is_file($file_path)){
            $this->error('标签附件不存在...');
        }
        $ext = pathinfo($file_path, PATHINFO_EXTENSION);
        $file_name = $arc_info['title'].'_V'.$arc_info['version'].'_'.$arc_info['arc_version'].'.'.$ext;
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$file_name.'"'); 
        readfile($file_path);
        exit;
    }

    //历史版本
    public function history($proid){
        $output = [];
        $items =  Db::view('project','*')
                    ->view('archive','*','project.id=archive.project_id','LEFT')
                    ->view('member','realname','archive.arc_userid=member.userid','LEFT')
                    ->where(['proid'=>$proid])
                    ->order('version ASC')
                    ->select() or $this->error('项目不存在...');
/*
        Db::view('archive','*')
                    ->view('member','realname','arc_userid=member.userid')
                    ->where(['arc_proid'=>$proid])
                    ->select() or $this->error('项目不存在...');
*/
        $output['items'] = $items;
        return $this->fetch('',$output);
    }


    //评审过程
    public function show($proid){
        $output = [];
        $items = $this->model_project->where([
            'proid' =>  $proid,
            'state' =>  PROJECT_STATE_END,
        ])->order('version ASC')->select();
        // print_r($items);
        foreach ($items as $k => $v) {
            $v = $v->toArray();
            if($v['diy_id']){
                $v['diy_list']=  $this->model_diy->diyInfo($v['id']);
            }else{
                $v['dept_task_list'] = $this->getDeptTask($v['id']);
                $v['member_task_list']  = $this->getMemberList($v['id']);
            }
            $lists[] = $v;
        }
        // print_r($lists);exit;
        $output['proid']=$proid;
        $output['items'] = $lists;
        return $this->fetch('',$output);
    }


    private function getDeptTask($id){
        $map = [
            'project_id'    =>  $id,
        ];
        $items = [];
        $items = Db::view('deptTask','*')->view('dept','dept_name','task_deptid=depid','LEFT')->where($map)->select();
        foreach ($items as $key => $value) {
            $items[$key]['taks_list'] = $this->getTask($value['taskid']);
        }
        return $items;
    }

    private function getTask($dept_taskid){
        $map = [
            'dept_taskid'   =>  $dept_taskid,
        ];
        $items = [];
        $items = Db::view('task','*')->view('member','realname','rev_userid=userid','LEFT')->where($map)->select();
        return $items;
    }

    private function getMemberList($id){
        $map = [
            'project_id'    =>  $id,
            'dept_taskid'   =>  0,
        ];
        $items = [];
        // $items = Db::view('task','*')->view('member','realname','rev_userid=userid','LEFT')->where($map)->select();
        $items=Db::name('task')->where('project_id',$id)->select();
        return $items;
    }

}
