<?php
/**
 * 会员模型
 *
 */
namespace app\common\model;
use think\Model;
use think\Db;
class Member extends Model
{

	 /**
     * 会员详细信息
     * @param array $condition
     * @param string $field
     * @return array
     */
	public function getMemberInfo($condition=array(),$field='*'){
		return $this->where($condition)->find();
	}

	/**
     * 会员列表
     * @param array $condition
     * @param string $field
     * @param number $page
     * @param string $order
     */
    public function getMemberList($condition = array(),$order = 'userid desc') {
        $member_list = $this->where($condition)->order($order)->select();
        $DEPT = cache('dept');
        if($member_list){
            foreach($member_list as $k=>$row){
                $member_list[$k]['dept_name'] = $DEPT[$row['dept']]['dept_name'];
            }
        }
        return $member_list;
    }

    /**
     * 会员信息详情
     * @param int $userid
     * @return array
     */
    public function getUserInfo($userid){
    	$userinfo = $this->get($userid);
    	if($userinfo){
    		$DEPT = cache('dept');
    		$userinfo['dept_name'] = $DEPT[$userinfo['dept']]['dept_name'];
            $userinfo = $userinfo->toArray();
    	}
    	return $userinfo;
    }

    /**
     * 部门会员
     * @param array $condition 条件
     * @return array
     */
    public function getDeptMemberList($condition=[]){
        $DEPT = cache('dept');
        $member_list = $this->where($condition)->order('dept ASC')->select();
        foreach($member_list as $row){
            $DEPT[$row['dept']]['member_list'][] = $row->toArray();
        }
        return $DEPT;
    }


    /**
     * 创建项目环节 可接收项目的人员
     * @param array $condition 条件
     * @return array
     */
    public function getTaskMemberList($condition=[]){
        $map = [
           
        ];
        $member_list = Db::view('dept','*')
                        ->view('member','userid,realname','userid=dept_muid')
                        ->where($map)
                        ->order('depid ASC')
                        ->select();
        return $member_list;
    }

	public function getAll(){
		
	}

	public function getOne(){

	}

	public function getCount(){

	}

	public function getSum(){

	}

	public function execInsert(){
		
	}

	public function execDelete(){
		
	}

	public function execUpdate(){
		
	}



}
?>