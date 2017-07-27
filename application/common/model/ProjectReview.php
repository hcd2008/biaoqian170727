<?php
/**
 * 评审模型
 *
 */
namespace app\common\model;
use think\Model;
use think\Db;
class ProjectReview extends Model{
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
    	$review_list = Db::view('projectReview','*')
    					->view('projectTask','*','projectReview.taskid=projectTask.taskid')
                        ->view('project','*','projectTask.pro_id=project.id')
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
        $review_list = Db::view('projectReview','*')
                        ->view('member','realname','projectReview.rev_userid=member.userid')
                        ->where($map)
                        ->order($order)
                        ->select();
        return $review_list;
    }

    //评审任务详情
    public function getReviewInfo($map = []){
        if(!$map) return false;
        $review = Db::view('projectReview','taskid')
                        ->view('task','*','projectReview.taskid=task.revid')
                        ->view('project','*','projectTask.pro_id=project.id')
                        ->where($map)
                        ->find();
        return $review;
    }
}
?>