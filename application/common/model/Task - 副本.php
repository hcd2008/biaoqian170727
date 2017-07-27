<?php
/**
 * 用户评审模型
 *
 */
namespace app\common\model;
use think\Model;
use think\Db;
class Task extends Model{
	protected $auto = [];
    protected $insert = ['rev_addtime'=>TIMES,'rev_state'=>REVIEW_STATE_NEW]; 
    protected $update = [];

    /** 
	* 评审列表
	* @access public 
	* @param array $map 条件
	* @param string $order 排序
	* @return  mixed
	*/
    public function getReviewList($map=[],$order=''){
    	$order = 'revid DESC';
    	if(!$order){
    		$order = 'revid DESC';
    	}
        if($map['state']){
            $map['project.state'] = $map['state'];
            unset($map['state']);
        }
    	$review_list = Db::view('Task','*')
    					//->view('deptTask','*','Task.dept_taskid=deptTask.taskid')
                        //->view('project','*','deptTask.project_id=project.id')
                        ->view('project','*','Task.project_id=project.id')
                        ->view('member',['realname'=>'rev_realname'],'Task.rev_userid=member.userid')
                        ->where($map)
                        ->order($order)
                        ->paginate();
        return $review_list;
    }

    /** 
    * 评审状态
    * @access public 
    * @param array $map 条件
    * @return  mixed
    */
    public function getReviewState($map=[],$order='revid ASC'){
        $review_list = Db::view('Task','*')
                        ->view('member','realname','Task.rev_userid=member.userid')
                        ->where($map)
                        ->order($order)
                        ->select();
        return $review_list;
    }

    //评审任务详情
    public function getReviewInfo($map = []){
        if(!$map) return false;
        $review = Db::view('Task','*')
                        //->view('deptTask','*','Task.dept_taskid=deptTask.taskid')
                        //->view('project','*','deptTask.project_id=project.id')
                        ->view('project','*','Task.project_id=project.id')
                        ->where($map)
                        ->find();
        return $review;
    }

    public function member(){
        return $this->hasOne('Member','userid');
    }
}
?>