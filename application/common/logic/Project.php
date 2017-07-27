<?php
/**
 * 项目逻辑
 * By Lp.Add.2016.08.05
 */
namespace app\common\logic;
use think\Db;
class project{

	private $msg_project_new ='您有一个新标签评审任务需要分配';
	private $msg_dept_check = '您有一个新的任务需要审批';
	private $msg_task_new = '您有一个新的评审任务';
	/** 
	* 添加项目
	* @access public 
	* @param mixed $post 表单
	* @return  array
	*/
	public function create($post){#print_r($post);exit;
		global $_G;
		$model_project  = model('Project');
		$model_pm = model('pm');
		$project = $post['project'];
		if(!isset($project['userid'])){
			$project['userid'] = $_G['_userid'];
		}

		//重新提交项目
		if(!empty($post['op']) && $post['op'] == 'reset'){
			$proid = $post['proid'];
			$max_version = $model_project->where(['proid'=>$proid])->max('version');
			#echo $model_project->getLastSql();
			#print_r($item);exit;
			$project['version'] = $max_version +1;
			$project['proid'] = $proid;
			$project['diy_id'] = $post['diy_id'];
		}else{
			$project['version'] = isset($project['version'])?$project['version']:1;
			$project['proid'] = $model_project->getMaxProid();
		}
		
		$project['progress_total'] = isset($project['depts'])?count($project['depts']):0;
		$content = addslashes($project['content']);
		$depts = isset($project['depts'])?$project['depts']:'';
		$notice_type = isset($project['notice_type'])?$project['notice_type']:'';
		$notice_users_ext = isset($project['notice_users_ext'])?$project['notice_users_ext']:'';
		$notice_content = addslashes($project['notice_content']);

		$userinfo = model('member')->getUserInfo($project['userid']);
		if(!$userinfo){
			return callback(false,'用户不存在');
		}

		$project['userinfo_ext'] = isset($project['userinfo_ext'])?$project['userinfo_ext']:$userinfo['realname'].'('.$userinfo['dept_name'].')';
		
		if($depts){
			$project['depts'] = implode(',',$depts);
		}
	
		//转换日期为时间戳
		$project['starttime'] = strtotime($project['starttime']);
		$project['endtime'] = strtotime($project['endtime']);
		$project['remindtime'] = strtotime($project['remindtime']);

		//验证数据有效性
		$validate_project = validate('AddProject');
		if(!$validate_project->check($project)){
			return callback(false,$validate_project->getError());
		}

		//添加数据
		$last_insert_id = $model_project->allowField(true)->save($project);
		if(!$last_insert_id){
			return callback(false,$model_project->getError());
		}
		//添加内容
		$model_project->addContent($last_insert_id,$content);

		//发送项目到选中的部门列表
		if($depts){
			$result = $this->addDeptTask([
				'id' => $last_insert_id,
				'depts' => $depts,
				'userid'=> $project['userid'],
				'sign'=> $project['sign'],
			]);
			if($depts){
				$model_pm->batchSend($depts,$this->msg_project_new,$notice_content);
			}
			if($notice_type && $notice_users_ext){
				$this->remind($notice_users_ext,$notice_type,$notice_content);
			}
		}
		return callback(true,'创建项目成功',['last_insert_id'=>$last_insert_id,'version'=>$project['version'],'proid'=>$project['proid']]);
	}

	/** 
	* 编辑项目
	* @access public 
	* @param mixed $post 表单
	* @return  array
	*/
	public function edit($post){
		$model_project = model('Project');
		$id = $post['id'];
		$project= $post['project'];
		//转换日期为时间戳
		$project['starttime'] = strtotime($project['starttime']);
		$project['endtime'] = strtotime($project['endtime']);
		$project['remindtime'] = strtotime($project['remindtime']);

		//验证数据有效性
		$validate_project = validate('Project');
		if(!$validate_project->check($project)){
			return callback(false,$validate_project->getError());
		}
		//修改数据
		$model_project->allowField(true)->isUpdate(true)->save($project,['id'=>$id]);
		//修改内容
		$model_project->editContent($id,$project['content']);
		return callback(true,'重新编辑项目成功');
	}

	/** 
	* 分配项目到部门
	* @access public 
	* @param mixed $post 表单
	* @return  array
	*/
	public function addDept($post){
		global $_G;
		$model_pm = model('pm');
		$model_project = model('Project');
		$model_dept_task = model('deptTask');
		$userid = $_G['_userid'];
		$id = $post['id'];
		$depts = isset($post['depts'])?$post['depts']:'';
		$notice_users_ext = isset($post['notice_users_ext'])?$post['notice_users_ext']:'';
		$notice_type = isset($post['notice_type'])?$post['notice_type']:'';
		$notice_content = isset($post['notice_content'])?$post['notice_content']:'';
		$sign = isset($post['sign'])?$post['sign']:'';
		$project = $model_project->get($id);
		if(!$project){
			return callback(false,'项目不存在');
		}
		if(!$depts){
			return callback(false,'请选择评审部门');
		}
		if(!$notice_content){
			return callback(false,'请填写提醒内容');
		}

		//已经分配的部门
		$dept_task_list = $model_dept_task->where(['project_id' => $id])->select();
		$dept_task_users = [];
		if($dept_task_list){
			foreach ($dept_task_list as $key => $value) {
				$task_uid = $value['task_userid'];
				$dept_task_users[$task_uid] = $task_uid;
			}
		}

		//检查是否包含已经分配的部门
		if($dept_task_users){
			foreach ($dept_task_users as $k => $v) {
				foreach ($depts as $key => $value) {
					if($v == $value){
						return callback(false,'评审部门已经存在');
					}
				}
			}
		}

		//取出部门负责人userid
		$dept_manager = [];
		$DEPT = model('dept')->where(['depid'=>['in',$depts]])->select();

		foreach ($DEPT as $k => $v) {
			$dept_manager[$v['dept_muid']] = $v['dept_muid'];
		}
		//插入部门数据
		$result = $this->addDeptTask([
					'id' => $project['id'],
					'depts' => $depts,
					'userid' => $userid,
					'sign' => $sign,
				]);
		$this->updateProjectProgress($project['id']);
		if($result){
			$model_pm->batchSend($dept_manager,$this->msg_project_new,$this->pmLang('dept_task_new',['title'=>$project['title']]));
		}
		return callback(true,'添加评审部门成功');
	}

	/** 
	* 分配项目到评审人员
	* @access public 
	* @param mixed $post 表单
	* @return  array
	*/
	public function addUser($post){
		$model_task = model('task');
		$model_project = model('project');
		$model_pm = model('pm');
		$id = $post['id'];
		$notice_users = isset($post['notice_users'])?$post['notice_users']:'';
		$notice_content = isset($post['notice_content'])?$post['notice_content']:'';
		$sign = isset($post['sign'])?$post['sign']:'';
		$project = $model_project->get($id);
		if(!$project){
			return callback(false,'项目不存在');
		}
		if(!$notice_users){
			return callback(false,'请选择评审人员');
		}
		if(!$notice_content){
			return callback(false,'请填写提醒内容');
		}
		foreach ($notice_users as $key => $uid) {
			$data_list[] = [
				'project_id'	=>	$id,
				'dept_taskid'	=>	0,
				'rev_userid'	=>	$uid,
				'rev_from_userid'	=>	$project['userid'],
				'rev_sign'		=>	$sign,
				'rev_mode'		=>	1,
			];
		}
		$result = $model_task->saveAll($data_list);
		if($result){
			//更新项目进度状态
			$this->updateProjectProgress($id);
			//默认系统提醒
			foreach ($result as $key => $value) {
				$model_pm->send($value['rev_userid'],$this->msg_project_new,$this->pmLang('task_new',['title'=>$project['title']]));
			}
		}else{
			return callback(false,'添加评审人员失败');
		}
		return callback(true,'添加评审人员成功');
	}


	/** 
	* 分发任务到部门
	* @access private 
	* @param array $post
	* @return  boolean
	*/
	private function addDeptTask($item){
		if(!isset($item['id']) || !isset($item['depts'])){
			return false;
		}
		$model_task = model('DeptTask');
		$list = [];
		foreach ($item['depts'] as $deptid) {
			$list[] = [
				'project_id'		=>	$item['id'],
				'task_userid'		=>	0,
				'task_deptid'		=>	$deptid,
				'task_from_userid'	=>	$item['userid'],
				'task_sign'			=>	$item['sign'],
			];
		}
		return $model_task->saveAll($list);
	}

	/** 
	* 分配任务到部门成员
	* @access public 
	* @param arrat $post 表单
	* @return  array
	*/
	public function addMemberTask($item,$post){
		global $_G;
		$model_dept_task = model('deptTask');
		$model_member_task = model('Task');
		$model_pm = model('pm');
		$taskid = $post['taskid'];
		$notice_users = isset($post['notice_users'])?$post['notice_users']:'';
		if(!$item){
			return callback(false,'项目不存在...');
		}
		if(!$taskid){
			return callback(false,'提交数据异常...');
		}
		if($item['task_state'] != TASK_STATE_NEW){
			return callback(false,'任务状态不正常,不可分配...');
		}
		if(!$notice_users){
			return callback(false,'请选择评审人员...');
		}
		$dept_progress_total = count($notice_users);
		$sign = addslashes($post['sign']);
		$notice_type = isset($post['notice_type'])?$post['notice_type']:'';
		$notice_users_ext = isset($post['notice_users_ext'])?$post['notice_users_ext']:'';
		$notice_content = addslashes($post['notice_content']);
		//添加到个人评审
		foreach ($notice_users as $key => $rev_userid) {
			$list[] = [
				'project_id'		=>	$item['project_id'],
				'dept_taskid'		=>	$taskid,
				'rev_userid'		=>	$rev_userid,
				'rev_from_userid'	=>	$item['task_userid'],
				'rev_sign'			=>	$sign,
			];
		}
		$result = $model_member_task->saveAll($list);
		if(!$result){
			return callback(false,$model_member_task->getError());
		}

		//修改部门任务信息
		$task_data = [
			'taskid'	=>	$taskid,
			'task_state'			=>	TASK_STATE_SEND,
			'task_progress'			=>	0,
			'task_progress_total'	=>	$dept_progress_total,
		];
		$model_dept_task->isUpdate(true)->save($task_data);

		//系统默认提醒
		$model_pm->batchSend($notice_users,$this->msg_task_new,$notice_content);
		if($notice_type && $notice_users_ext){
			$this->remind($notice_users_ext,$notice_type,$notice_content);
		}
		return callback(true,'分配任务成功...');
	}

	/**
	 * 更新项目进度
	 */
	public function updateProjectProgress($id){
		$model_project = model('project');
		$model_task = model('task');
		$model_dept_task = model('deptTask');
		$total_progress = $progress = 0;

		$project_info = $model_project->get(['id' => $id]);

		if($project_info['diy_id']){
			return false;
		}

		//评审员总进度
		$total_progress_task = $model_task->where([
			'project_id'	=>	$id,
			'rev_mode'			=>	1,
		])->count();

		//评审员完成进度
		$progress_task = $model_task->where([
			'project_id'		=>	$id,
			'rev_mode'			=>	1,
			'rev_state'			=>	REVIEW_STATE_REPORT,
		])->count();

		//部门总进度
		$total_progress_dept_task = $model_dept_task->where([
			'project_id'	=>	$id,
		])->count();

		$total_progress = $total_progress_task + $total_progress_dept_task;

		//部门完成进度
		$progress_dept_task = $model_dept_task->where([
			'project_id'	=>	$id,
			'task_state'	=>	TASK_STATE_REPORT,
		])->count();

		$progress = $progress_task + $progress_dept_task;

		//更新项目
		$model_project->isUpdate(true)->save([
			'id'		=>	$id,
			'progress'	=>	$progress,
			'progress_total'	=>	$total_progress,
		]);

		//自动完成项目
		if($project_info['progress_total'] && $project_info['progress_total'] == $progress){

			//完成项目状态
			$model_project->isUpdate(true)->save([
				'id'	=>	$project_info['id'],
				'state'	=>	PROJECT_STATE_END,
			]);

			#完成评审结果
			//部门审评结果
			$dept_reject_count = $model_dept_task->where([
				'project_id'	=>	$id,
				'task_result_state'	=>	TASK_RESULT_STATE_REJECT,
			])->count();

			//直接发给评审员的审评结果
			$users_reject_count = $model_task->where([
				'project_id'	=>	$id,
				'rev_result_state'	=>	REVIEW_RESULT_STATE_REJECT,
			])->count();
			$reject_count = $dept_reject_count+$users_reject_count;
			$model_project->isUpdate(true)->save([
				'id'			=>	$id,
				'state'			=>	$reject_count?PROJECT_STATE_NEW:PROJECT_STATE_END,
				'finishitime'	=>	TIMES,
				'result_state'	=>	$reject_count?PROJECT_RESULT_STATE_REJECT:PROJECT_RESULT_STATE_PASS,
			]);
		}

		return true;
	}

	/**
	 * 更新部门任务进度
	 */
	public function updateDeptTaskProgress($taskid){
		if(!$taskid) return false;
		$model_dept_task = model('deptTask');
		$model_task = model('task');
		$dept_task_info = $model_dept_task->get(['taskid' => $taskid]);

		//评审员总进度
		$total_progress = $model_task->where([
			'dept_taskid'	=>	$taskid,
			'rev_mode'			=>	0,
		])->count();

		//评审员完成进度
		$progress = $model_task->where([
			'dept_taskid'	=>	$taskid,
			'rev_mode'		=>	0,
			'rev_state'		=>	REVIEW_STATE_REPORT
		])->count();

		//更新部门任务进度
		$model_dept_task->isUpdate(true)->save([
			'task_progress'			=>	$progress,
			'task_progress_total'	=>	$total_progress,
			'taskid'			=>	$taskid
		]);
		
		//自动完成部门任务状态
		if($dept_task_info['task_progress_total'] && $total_progress  == $progress){
			$model_dept_task->isUpdate(true)->save([
				'taskid'		=>	$taskid,
				'task_state'	=>	TASK_STATE_REWIEW,
			]);
		}

		return true;
	}

	/** 
	* 评审员提交报告
	* @access public 
	* @param arrat $post 表单
	* @return  array
	*/
	public function review_report($project){
		$model_task = model('task');
		$model_dept_task = model('deptTask');

		//更新评审信息
		$model_task->save(['rev_state'=>REVIEW_STATE_REPORT,'rev_report_time'=>TIMES],['revid'=>$project['revid']]);

		//更新部门任务进度
		$this->updateDeptTaskProgress($project['dept_taskid']);

		//更新项目进度
		$this->updateProjectProgress($project['project_id']);
		/*
		$task_progress = $model_task->where(['dept_taskid'=>$project['taskid'],'rev_state'=>REVIEW_STATE_REPORT])->count();
		$model_dept_task->save(['task_progress'=>$task_progress],['taskid'=>$project['taskid']]);
		if($project['task_progress_total'] == $task_progress){
			$model_dept_task->save(['task_state'=>TASK_STATE_REWIEW],['taskid'=>$project['taskid']]);
		}
		*/
		$this->diyNext($project['project_id']);
		return callback(true,'提交报告成功...');
	}

	/** 
	* 任务-上报部门意见
	* @access public 
	* @param 
	* @return  array
	*/
	public function deptTaskReport($post){
		global $_G;
		$_userid = $_G['_userid'];
		$model_dept_task = model('deptTask');
		$model_check = model('deptTaskCheck');
		$model_project = model('project');
		$model_pm = model('pm');
		$taskid = $post['taskid'];

		$dept_task_item = $model_dept_task->get([
			'taskid'	=>	$taskid,
		]);
		if(!$dept_task_item){
			return callback(false,'任务不存在...');
		}
		$project_id = $dept_task_item['project_id']; 
		$project_item = $model_project->get([
			'id'	=>	$project_id,
		]);
		if(!$project_item){
			return callback(false,'项目不存在...');
		}

		//需要领导审批
		if(isset($post['check_users']) && $post['check_users']){
			$hq_content = $post['hq_content'];
			$check_users = explode(',',$post['check_users']);
			foreach ($check_users as $uid) {
				$check_data[] = [
					'tid'	=>	$taskid,
					'check_from_userid' =>	$_userid,
					'check_userid'	=>	$uid,
					'check_content'	=>	$hq_content,
				];
			}
			$result = $model_check->saveAll($check_data,true);
			if(!$result){
				return callback(false,'审批插入失败...');
			}
			$model_dept_task->isUpdate(true)->save([
				'taskid'		=>	$taskid,
				'task_check'	=>	1,
				'task_state'	=>	TASK_STATE_APPROVAL,
			]);
			$model_pm->batchSend($check_users,$this->msg_dept_check,$this->pmLang('dept_task_check',['title'=>$project_item['title']]));
			return callback(true,'已经提交到领导审批');
		}

		//更新部门任务状态
		$model_dept_task->isUpdate(true)->save([
			'taskid'			=>	$taskid,
			'task_result_time'  =>  TIMES,
			'task_state'		=>	TASK_STATE_REPORT,
		]);
		$this->updateProjectProgress($project_id);
		$this->diyNext($project_id);
		return callback(true,'上报部门意见成功...');
	}

	/** 
	* 任务-领导审批
	* @access public 
	* @param 
	* @return  array
	*/
	public function task_check($post){
		$model_project = model('project');
		$model_check = model('deptTaskCheck');
    	$model_dept_task = model('deptTask');
    	$model_task = model('task');
    	$model_pm = model('pm');
    	$checkid = $post['checkid'];
    	$taskid = $post['taskid'];

    	$dept_task_info  = $model_dept_task->get($taskid);
    	if(!$dept_task_info){
    		return callback(false,'任务不存在...');
    	}

    	$project = $model_project->get($dept_task_info['project_id']);
    	if(!$project){
    		return callback(false,'项目不存在...');
    	}

    	$check_tocontent = $post['check_tocontent'];
    	$check_result_state = $post['check_result_state'];
    	$model_check->isUpdate(true)->save([
    		'checkid'	=>	$checkid,
    		'check_tocontent'	=>	$check_tocontent,
    		'check_result_state'	=>	$check_result_state?CHECK_RESULT_STATE_PASS:CHECK_RESULT_STATE_REJECT,
    	]);
    	$model_dept_task->isUpdate(true)->save([
    		'taskid'	=>	$taskid,
    		'task_state'	=>	$check_result_state?TASK_STATE_APPROVAL_OK:TASK_STATE_APPROVAL_NO,
    		'task_result_state'	=>	$check_result_state?CHECK_RESULT_STATE_PASS:CHECK_RESULT_STATE_REJECT,
    	]);

    	//如果领导强制通过,把所有人的评审改变为通过...
    	if($check_result_state){
    		$model_task->where(['dept_taskid'=>$taskid])->update([
    			'rev_result_state'	=>	TASK_RESULT_STATE_PASS,
    		]);
    	}

    	$model_pm->send($dept_task_info['task_userid'],'您有一个任务领导已经审批',$this->pmLang('dept_task_checked',['title'=>$project['title']]));
    	return callback(true,'审批完成...');
	}

	/**
	 * 项目存档
	 */
	public function projectArchive($post){
		global $_G;
		$model_archive = model('archive');
		$model_project = model('project');
		if(empty($post['project_id'])){
			return callback(false,'项目部存在...');
		}
		if(empty($post['arc_title'])){
			return callback(false,'请填写标签名称...');
		}
		$project_info = $model_project->get(['id'=>$post['project_id']]);
		if(!$project_info){
			return callback(false,'项目部存在...');
		}
		if($project_info['archive'] > 0 ){
			return callback(false,'该项目已经存档...');
		}
		$post['arc_proid'] = $project_info['proid'];
		$post['arc_userid'] = $_G['_userid'];
		$arc_replace = empty($post['arc_replace'])?0:$post['arc_replace'];
		if($arc_replace){//代替标签
			$item  = $model_archive->get(['arcid'=>$arc_replace]);
			if(!$item){
				return callback(false,'替代标签不存在...');
			}
			$post['arc_replace'] = 0;
		}
		$result = $model_archive->allowField(true)->save($post);
		if($result){
			$model_archive->isUpdate(true)->save(['arc_replace'=>$result],['arcid'=>$arc_replace]);
			$model_project->isUpdate(true)->save([
				'id'		=>	$post['project_id'],
				'archive'	=>	1,
			]);
			return callback(true,'存档成功...');
		}
		
		return callback(false,$model_archive->getError());
	}


	/** 
	* 事务提醒
	* @access public 
	* @param array $users 用户id数组
	* @param array $notice_type 提醒方式
	* @param array $notice_content 提醒内容
	* @return  array
	*/
	public function remind($users,$notice_type,$notice_content){
		$model_pm = model('pm');
		$model_pm->batchSend($users,$notice_type,$notice_content);
		return true;
	}


	public function check_state($project){
		$state = $project['state'];
		if($state == PROJECT_STATE_END){
			return callback(false,'项目已经完成,不可操作...');
		}
		return callback(true);
	}

	//自定义流程下一步
	private function diyNext($id){
        $model_project = model('project');
        $model_diy = model('diy');
        $model_task = model('task');
        $model_dept_task = model('deptTask');
        $project = $model_project->where(['id'=>$id])->find();
        if($project['state'] != 1){
        	return fasle;
        }
        $diy_id = $project['diy_id'];
        $diy_step = $project['diy_step'];
        $diy_next_step = $diy_step+1;
        if(!$diy_id){
        	return false;
        }
        //检查评审中是否有拒绝的
    	$reject_task = $model_task->where(['project_id'=>$id,'dept_taskid'=>0,'rev_result_state'=>REVIEW_RESULT_STATE_REJECT])->count();
    	$reject_dept_task = $model_dept_task->where(['project_id'=>$id,'task_result_state'=>TASK_RESULT_STATE_REJECT])->count();
    	if($reject_task || $reject_dept_task){
    		$model_project->where(['id'=>$id])->update([
    			'progress'		=>	$project['progress']+1,
    			'result_state'	=>	PROJECT_RESULT_STATE_REJECT,
    		]);

    		return false;
    	}
    	//自定义流程
        $diy_conf = $model_diy->where(['diy_id'=>$diy_id])->find();
        $diy_content = unserialize($diy_conf['diy_content']);
        $curr_step_conf =  !empty($diy_content[$diy_step])?$diy_content[$diy_step]:'';
        $next_step_conf = !empty($diy_content[$diy_next_step])?$diy_content[$diy_next_step]:'';
        
        //下一步配置
        $depts = $users = [];
        if($next_step_conf){
	        $depts = $next_step_conf['depts']?$next_step_conf['depts']:'';
	        $users = $next_step_conf['users']?$next_step_conf['users']:'';
        }
        //当前步骤配置
        $curr_depts = $curr_users = [];
        if($curr_step_conf){
	        $curr_depts = $curr_step_conf['depts']?$curr_step_conf['depts']:'';
	        $curr_users = $curr_step_conf['users']?$curr_step_conf['users']:'';
        }
        //检查当前环节是否结束
        if(!empty($curr_depts)){
        	$dept_task_noend_count = $model_dept_task->where(['project_id'=>$id,'task_deptid'=>['in',$curr_depts],'task_state'=>['neq',TASK_STATE_REPORT]])->count();
        	if($dept_task_noend_count){
        		return false;
        	}
        }
        if(!empty($curr_users)){
        	$task_noend_count = $model_task->where(['project_id'=>$id,'rev_userid'=>['in',$curr_users],'rev_state'=>['neq',REVIEW_STATE_REPORT]])->count();
        	;
        	if($task_noend_count){
        		return false;
        	}
        }

        //更新项目进度
        if($project['progress']+1 >= $project['progress_total'] ){
        	$model_project->where(['id'=>$id])->update([
        		'progress'		=>	$project['progress_total'],
        		'state'			=>	PROJECT_STATE_END,
        		'finishitime'	=>	TIMES,
        		'result_state'	=>	PROJECT_RESULT_STATE_PASS,
        	]);
        }else{	
        	$model_project->where(['id'=>$id])->update(['progress'=>$project['progress']+1]);
        }

        //如果没有下一环节
        if(!$next_step_conf){
        	return false;
        }

        if(!empty($depts)){
            $result = $this->addDept([
                'id'                =>  $project['id'],
                'depts'      =>  $depts,
                'notice_content'    =>  '您有一个新的任务需要分配',
            ]);
        }
        if(!empty($users)){
            $result = $this->addUser([
                'id'                =>  $project['id'],
                'notice_users'      =>  $users,
                'notice_content'    =>  '您有一个新的评审任务',
            ]);
        }
        $model_project->isUpdate(true)->save([
            'id'                =>  $project['id'],
            'diy_step'          =>  $diy_next_step,
        ]);
    }

    //已定义流程拒绝后，重新编辑
    public function diyEditAfter($id){
    	$model_project = model('project');
    	$model_diy = model('diy');
    	$model_task = model('task');
    	$model_dept_task = model('deptTask');
    	$model_pm = model('pm');
    	$project = $model_project->where(['id'=>$id])->find();
    	if(empty($project))return false;
        $diy_id = $project['diy_id'];
        $diy_step = $project['diy_step'];
        if(!$diy_id){
        	return false;
        }
        $diy_conf = $model_diy->where(['diy_id'=>$diy_id])->find();
        $diy_content = unserialize($diy_conf['diy_content']);
        $step_conf =  $diy_content[$diy_step];
        $depts = $step_conf['depts'];
        $users = $step_conf['users'];
        //$model_dept_task->where(['project_id'=>$id])->delete();
        //$model_task->where(['project_id'=>$id])->delete();
        $model_project->where(['id'=>$id])->update([
        	'state'			=>	PROJECT_STATE_STOP,
        ]);
    	return true;
        exit;
        if($depts){
        	$dept_list = $model_dept_task->where(['project_id'=>$id,'task_result_state'=>TASK_RESULT_STATE_REJECT,'task_deptid'=>['in',$depts]])->select();
        	if($dept_list){
	        	foreach ($dept_list as $key => $item) {
	        		$model_dept_task->where(['taskid'=>$item['taskid']])->update([
	        			'task_state'	=>	2,
	        			'task_result_state'	=>	0,
	        			'task_progress'	=>	0,
	        			'task_result_time'	=>	0,
	        		]);
	        		$model_task->where(['dept_taskid'=>$item['taskid']])->update([
	    			 	'rev_report_time'	=>	0,
	    			 	'rev_result_state'	=>	0,
	    			 	'rev_state'			=>	1,
	    			 	'rev_attach'		=>	'',
        			]);
        			$task_list = $model_task->where(['dept_taskid'=>$item['taskid']])->select();
		        	foreach($task_list  as $v){
		        		$dept_users[] = $v['rev_userid'];
		        	}
	        	}
        	}
        	$DEPT = cache('dept');
        	foreach ($depts as $k => $v) {
        		$dept_users[] = $DEPT[$v]['dept_muid'];
        	}
        	$msg_title = $project['title'].' 由创建者重新编辑，请您重新评审';
        	$model_pm->batchSend($dept_users,$msg_title,$msg_title);
        }
        if($users){
        	$user_list = $model_task->where(['project_id'=>$id,'rev_result_state'=>REVIEW_RESULT_STATE_REJECT,'rev_userid'=>['in',$users]])->select();
        	if($user_list){
        		foreach ($user_list as $key => $item) {
        			$model_task->where(['revid'=>$item['revid']])->update([
	    			 	'rev_report_time'	=>	0,
	    			 	'rev_result_state'	=>	0,
	    			 	'rev_state'			=>	1,
	    			 	'rev_attach'		=>	'',
        			]);
        		}
        		$msg_title = $project['title'].' 由创建者重新编辑，请您重新评审';
        		$model_pm->batchSend($users,$msg_title,$msg_title);
        	}
        }
        $model_project->where(['id'=>$id])->update([
        	'state'			=>	PROJECT_STATE_NEW,
        	'result_state'	=>	0,
        ]);
        return true;
    }

    public function pmLang($i,$arr=[]){
    	$LANG = [
    		'dept_task_new'		=>	"您有一个新标签评审任务需要分配,【{title}】 [url=".SYS_DOMAIN.url('member/dept_task/index')."]点击查看[/url]",
    		'dept_task_check'	=>	"您有一个新的任务需要审批,【{title}】 [url=".SYS_DOMAIN.url('member/dept_job/index')."]点击查看[/url]",
    		'dept_task_checked'	=>	"您有一个任务已经审批完成,【{title}】 [url=".SYS_DOMAIN.url('member/dept_task/index')."]点击查看[/url]",
    		'task_new'			=>	"您有一个新的评审任务,【{title}】 [url=".SYS_DOMAIN.url('member/task/index')."]点击查看[/url]",
    	];
    	$L = $LANG[$i];
    	foreach ($arr as $k => $v) {
    		$L = str_replace('{'.$k.'}',$v,$L);
    	}
    	return $L;
    }

}
?>