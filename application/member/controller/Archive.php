<?php
namespace app\member\controller;
use app\common\controller\Member;
use think\Db;
class Archive EXTENDS Member
{
	function __construct(){
		parent::__construct();
		$this->model_project = model('project');
		$this->model_archive = model('archive');
	}

    public function index(){
    	$state = 1;
		if(!empty($this->GET['state']) && $this->GET['state'] ==3){
			$project_list = Db::view('archive','*')
							  ->view('project','*','project_id=id')
							  ->where([])->order('arc_addtime DESC')->select();//archive
			$output['state'] = 3;
		}else{
			$project_list = $this->model_project->where([
				    		'state'			=> PROJECT_STATE_END,
				    		'archive'		=> 0,
				    		'result_state'	=> PROJECT_RESULT_STATE_PASS,
				    	])->order('id ASC')->select();
			$output['state'] = 1;
		}
    	
    	#print_r($project_list);
    	$output['project_list'] = $project_list;
        return $this->fetch('',$output);
    }


    //选择存档类型
    public function step1($id){
    	$project_info = $this->model_project->get(['id' => $id]) or $this->error('项目不存在...');
    	if(IS_POST){
    		!empty($this->GET['type']) or $this->error('请选择存档类型');
    		if($this->GET['type'] == 1){
    			$this->redirect('member/archive/step2',['id' => $id]);
    		}else{
    			$this->redirect('member/archive/step3',['id' => $id]);
    		}
    		exit;
    	}
        $output['project_info'] = $project_info;
        return $this->fetch('',$output);
    }

    //新建存档
    public function step2($id){
    	$logic_project = model('project','logic');
    	$project_info = $this->model_project->get(['id' => $id]) or $this->error('项目不存在...');
    	if(IS_POST){
    		$this->GET['project_id'] = $id;
    		$result = $logic_project->projectArchive($this->GET);
    		if($result['state']){
    			$this->success($result['msg'],'archive/index');
    		}else{
    			$this->error($result['msg']);
    		}
    		exit;
    	}
    	$output['project_info'] = $project_info;
        return $this->fetch('',$output);
    }

    //替代标签
    public function step3($id){
        $logic_project = model('project','logic');
        $project_info = $this->model_project->get(['id' => $id]) or $this->error('项目不存在...');
        if(IS_POST){
            $this->GET['project_id'] = $id;
            $result = $logic_project->projectArchive($this->GET);
            if($result['state']){
                $this->success($result['msg'],'archive/index');
            }else{
                $this->error($result['msg']);
            }
            exit;
        }
        $output['archive_items'] = $this->model_archive->where(['arc_replace'=>0])->select();
        $output['project_info'] = $project_info;
        return $this->fetch('',$output);
    }

}
