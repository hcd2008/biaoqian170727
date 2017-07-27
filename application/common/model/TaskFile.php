<?php
/**
 * 用户评审模型
 *
 */
namespace app\common\model;
use think\Model;
use think\Db;
class TaskFile extends Model{
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
        $review_list = Db::view('taskFile','*')
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
        $review_list = Db::view('taskFile','*')
                        ->view('member','realname','Task.rev_userid=member.userid')
                        ->where($map)
                        ->order($order)
                        ->select();
        return $review_list;
    }

    //评审任务详情
    public function getReviewInfo($map = []){
        if(!$map) return false;
        $review = Db::view('taskFile','*')
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
    public function jianChabiao($userid,$chanpinid){

        // $jiancha1=Db::table('bq_jcbq')->where(['userid'=>$userid])->select(); 
        $jiancha=Db::table('bq_jcbq')->alias('a')->join('bq_member b','a.userid=b.userid')->field('a.itemid,a.userid,a.chanpinid,a.jcxiang,a.zhuangtai,b.username')->where(['a.userid'=>$userid,'a.chanpinid'=>$chanpinid])->order('a.addtime ASC')->select();
        
            
       return $jiancha;
    }
    public function chanPin($userid){
        $chanpin=Db::table('bq_jcbq')->alias('a')->join('bq_product b','a.chanpinid=b.pid')->field('distinct a.chanpinid,b.proname')->where(['a.userid'=>$userid])->select();
        return $chanpin;
    }
    public function edit($map){
        $itemid=$map['itemid'];
        $chanpinid=$map['chanpinid'];
        $userid=$map['userid'];
        
        foreach ($itemid as $k => $v) {

            $jc=Db::table('bq_jcbq')->where('itemid',$v)->update(['zhuangtai'=>1]);

        }
        return $jc;
    }
    public function wanCheng($userid,$id){
        $wan=Db::table('bq_jcbq')->where('userid',$userid)->where('chanpinid',$id)->where('zhuangtai','1')->select();
        return $wan;
    }
    public function pingShen($revid,$userid){
       
        $pingshen=Db::table('bq_task_question')->field('qs_typeid,qs_areaid,qs_ext1,qs_ext2,qs_ext3')->where(['revid'=>$revid,'qs_userid'=>$userid])->select();
       
        return $pingshen;
    }
    public function pingshen_area($revid,$userid){
         $pingshen_area=Db::table('bq_task_question')->alias('a')->join('bq_question b', 'a.qs_areaid=b.qid')->field('b.title')->where(['a.revid'=>$revid,'a.qs_userid'=>$userid])->select();
        return $pingshen_area;
    }
    public function chanpPnidM($revid,$userid){
        $pingshen_area=Db::table('bq_task')->where(['revid'=>$revid,'rev_userid'=>$userid])->value('project_id');
        $ming=Db::table('bq_project')->alias('a')->join('bq_product b', 'a.product_id=b.pid')->field('b.pid,b.proname')->where('a.id',$pingshen_area)->select();
        return $ming;
    }
    public function chanPinJc($pid,$userid){
        $chanpin=db('jcbq')->where(['chanpinid'=>$pid,'userid'=>$userid])->select();
        return $chanpin;
    }
}
?>