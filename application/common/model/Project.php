<?php
/**
 * 项目模型
 *
 */
namespace app\common\model;
use think\Model;
use think\Db;
class Project extends Model{
    protected $insert = ['state'=>PROJECT_STATE_NEW,'addtime','version'=>1];  
    protected $update = [];

    public $editFields = [
        ['name'=>'id','type'=>'hidden','title'=>'','help'=>'','option'=>'','required'=>true],
        ['name'=>'type','type'=>'select','title'=>'项目类型','help'=>'','option'=>'getFormType','required'=>true],
        ['name'=>'title','type'=>'text','title'=>'项目名称','help'=>'','option'=>'','required'=>true],
        ['name'=>'content','type'=>'textarea','title'=>'项目描述','help'=>'','option'=>''],
        ['name'=>'product_id','type'=>'select','title'=>'所属产品','help'=>'','option'=>'getFormProduct'],
        ['name'=>'version','type'=>'text','title'=>'版本','help'=>'','option'=>'','disabled'=>true],
        ['name'=>'bqfile','type'=>'file','title'=>'标签文件','help'=>'','option'=>''],
        ['name'=>'addtime','type'=>'date','title'=>'创建时间','help'=>'','option'=>''],
        ['name'=>'starttime','type'=>'date','title'=>'开始时间','help'=>'','option'=>''],
        ['name'=>'endtime','type'=>'date','title'=>'结束时间','help'=>'','option'=>''],
        ['name'=>'remindtime','type'=>'date','title'=>'预警日期','help'=>'','option'=>''],
        ['name'=>'real_length','type'=>'text','title'=>'标签尺寸-长','help'=>'','option'=>''],
        ['name'=>'real_width','type'=>'text','title'=>'标签尺寸-宽','help'=>'','option'=>''],
        ['name'=>'unit','type'=>'select','title'=>'标签尺寸-单位','help'=>'','option'=>'getFormUnit'],
        ['name'=>'state','type'=>'radio','title'=>'状态','help'=>'','option'=>'getFormState'],
    ];

    protected function setAddtimeAttr($value){
        return $this->dateToTime($value);
    }

    protected function setStarttimeAttr($value){
        return $this->dateToTime($value);
    }

    protected function setEndtimeAttr($value){
        return $this->dateToTime($value);
    }

    protected function setRemindtimeAttr($value){
        return $this->dateToTime($value);
    }

    

    private function dateToTime($date){
        if(empty($date)){
            return TIMES;
        }
        return is_numeric($date)?$date:strtotime($date);
    }

    public function getFormType(){
        return \think\View::instance()->label_type;
    }

    public function getFormProduct(){
        $items = model('product')->all();
        foreach ($items as $key => $value) {
            $lists[$value['pid']] = $value['proname'];
        }
        return $lists;
    }

    public function getFormState(){
        return [
            PROJECT_STATE_NEW=>project_state(PROJECT_STATE_NEW),
            PROJECT_STATE_STOP=>project_state(PROJECT_STATE_STOP),
            PROJECT_STATE_END=>project_state(PROJECT_STATE_END),
        ];
    }

    public function getFormUnit(){
        return \think\View::instance()->label_size_unit;
    }

    /** 
	* 项目信息
	* @access public 
	* @param int $id 项目id
	* @return  array
	*/
    public function getProjectInfo($id){
    	$project = $this->get($id);
    	if($project){
    		return $project->toArray();
    	}
    	return $project;
    }

    /** 
	* 项目详情
	* @access public 
	* @param int $id 项目id
	* @return  array|boolean
	*/
    public function getProjectDetail($id){
    	$task_ids = [];
    	$dept_task_list = $task_list = [];
    	$DEPT = cache('dept');
    	//项目
    	$project = $this->where('id',$id)->find();
    	if(!$project){
    		return false;
    	}
    	$project = $project->toArray();
    	//部门任务
    	$dept_task_list = Db::view('deptTask','*')
                        ->view('dept','dept_name,dept_muid as userid','task_deptid=dept.depid','LEFT')
                        ->view('member','realname','dept.dept_muid=member.userid','LEFT')
						->where(['project_id'=>$id])
						->order('dept.depid ASC')
						->select();
        //直接发给评审员的任务
        $task_list = Db::view('task','*')
                        ->view('member','dept,realname','task.rev_userid=member.userid','LEFT')
                        ->view('dept','dept_name','member.dept=dept.depid',"LEFT")
                        ->where(['task.project_id'=>$id,'rev_mode'=>1])
                        ->order('revid ASC')
                        ->select();

    	$project['dept_task_list'] = $dept_task_list;
        $project['task_list'] = $task_list;
    	return $project;
    }


    /** 
	* 根据任务获取项目
	* @access public 
	* @param int $userid 用户id
	* @return  array
	*/
    public function getProjectByTaskList($userid){
    	$map = [
    		'task_userid' => $userid,
    	];
    	$order = 'taskid DESC';
    	$task_list = Db::view('deptTask','*')
                        ->view('project','*','project_id=id')
                        ->where($map)
                        ->order($order)
                        ->paginate();
        return $task_list;
    }


	/** 
	* 添加项目内容
	* @access public 
	* @param int $id 项目id
	* @param string $content 内容
	* @return  int||boolean
	*/
    public function addContent($id,$content=''){
    	return Db::name('projectData')->insert(['id'=>$id,'content'=>$content]);
    }

    /** 
    * 编辑项目内容
    * @access public 
    * @param int $id 项目id
    * @param string $content 内容
    * @return  int||boolean
    */
    public function editContent($id,$content=''){
        return Db::name('projectData')->where(['id'=>$id])->update(['content'=>$content]);
    }

    /** 
	* 生成项目id
	* @access public 
	* @return  int
	*/
    public function getMaxProid(){
    	$proid = $this->max('proid');
    	return intval($proid)+1;
    }

}
?>