<?php
/**
 * @Author: lipeng
 * @Date:   2016-09-18 10:32:38
 * @Last Modified by:   lipeng
 * @Last Modified time: 2016-09-24 16:06:45
 */
namespace app\admin\controller;
use app\common\controller\Admin;
use \think\Db;
class Project EXTENDS Admin
{
	function __construct(){
		parent::__construct();
        $this->view->location = '项目';
		$this->model = model('project');
        
		$map = [];
		if(!empty($this->kw)){
			$map['title'] = ['like',"%{$this->kw}%"];
		}
    	$items = $this->model->view('project','*')
                            ->view('product','proname','project.product_id=product.pid','LEFT')
                            ->where($map)
                            ->order('id DESC')
                            ->paginate('','',['query'=>[]]);
        foreach ($items as $key => $value) {
            $items[$key]['type'] =  empty($this->view->label_type[$value['type']])?'':$this->view->label_type[$value['type']];
        }
       
        $this->view->item = [];
    	$this->view->items = $items;

	}

    public function index(){
    	return $this->fetch();
    }

    public function add(){
        $post = $this->GET;
    	if(IS_POST){
    		$result = $this->model->validate(true)->allowField(true)->save($post);
    		if(!$result){
    			$this->error($this->model->getError());
    		}
    		$this->success('操作成功...','project/index');
    	}
    }

    public function edit($id){
    	if(IS_POST){
    		$post = $this->GET;
    		$result = $this->model->allowField(true)->isUpdate(true)->save($post);
    		if(!$result){
    			$this->error($this->model->getError());
    		}
            model('projectData')->allowField(true)->isUpdate(true)->save($post);
    		$this->success('操作成功...','project/index');
    		exit;
    	}
    	$item = $this->model->view('project','*')
                            ->view('projectData','content','project.id=projectData.id','LEFT')
                            ->where(['project.id'=>$id])
                            ->find() or $this->error('数据不存在...');
    	$this->view->item = $item;
    	return $this->fetch('index');
    }

    public function delete($id){
		$this->model->destroy(['id'=>$id]) or $this->error('删除失败');
		$this->success('删除成功...');
	}

    public function report($id){
        $this->view->location = '评审报告';
        $item = $this->model->where(['id'=>$id])->find() or $this->error('数据不存在...');

        if($item['diy_id']>99){

        }else{
            $dept_list = Db::view('deptTask','*')
                        ->view('dept','dept_name','task_deptid=depid','LEFT')
                        ->view('member','realname','dept.dept_muid=member.userid','LEFT')
                        ->where(['deptTask.project_id'=>$id])->select();
            //部门评审
            foreach ($dept_list as $key => $value) {
                $dept_list[$key]['task_list'] = model('task')->where(['project_id'=>$id,'dept_taskid'=>$value['task_deptid']])->select();
            }

            //个人评审
            $member_list = Db::view('task','*')
                            ->view('member','realname','task.rev_userid=member.userid','LEFT')
                            ->view('dept','dept_name','member.dept=dept.depid','LEFT')
                            ->where(['project_id'=>$id,'dept_taskid'=>0])->select();

            $this->view->dept_list = $dept_list;
            $this->view->member_list = $member_list;
        }

        $this->view->item = $item->toArray();
        return $this->fetch('');
    }

    public  function report_detail($revid){
        $this->view->location = false;
        $item = Db::view('task','*')
                    ->view('project','title,version,bqfile','task.project_id=project.id','LEFT')
                    ->view('member','realname','task.rev_userid=member.userid','LEFT')
                    ->where(['task.revid'=>$revid])
                    ->find()or $this->error('报告不存在...');
        $this->view->item = $item;
        return $this->fetch('');
    }

    public function dept_task($taskid){
        $item = Db::view('deptTask','*')
                        ->view('dept','dept_name','task_deptid=depid','LEFT')
                        ->where(['deptTask.taskid'=>$taskid])->find() or $this->error('部门任务不存在...');

        $items = Db::view('task','*')
                    ->view('member','realname','task.rev_userid=member.userid','LEFT')
                    ->where(['dept_taskid'=>$taskid])
                    ->select();
        $this->view->item = $item;
        $this->view->items = $items;
        $this->view->engine->layout(false);
        return $this->fetch('');
    }

}
