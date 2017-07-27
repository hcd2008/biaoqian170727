<?php
namespace app\member\controller;
use app\common\controller\Member;
use think\Db;
class Message EXTENDS Member
{
	function __construct(){
		parent::__construct();
		$this->model_pm = model('pm');
	}

    public function index(){
        $map = [
           'pm.userid'    =>  $this->_userid,
        ];
    	$pm_list = Db::view('pm','*')
                          ->view('member','realname','pm.from_userid=member.userid','LEFT')
                          ->where($map)
                          ->order('addtime DESC')
                          ->paginate(15);
        $output['pm_list'] = $pm_list;
        $output['state'] = 1;
        return $this->fetch('',$output);
    }


    //详情
    public function show($id){
        $map = [
           'pm.userid'    =>  $this->_userid,
           'id'           =>  $id,
        ];
    	$output = [];
        $message = Db::view('pm','*')
                          ->view('member','realname','pm.from_userid=member.userid','LEFT')
                          ->where($map)->find() or $this->error('站内信不存在...');
        $output['message'] = $message;
        $this->model_pm->isUpdate(true)->save([
            'id'        =>  $id,
            'state'     =>  PM_STATE_READED,
        ]);
        $this->model_pm->updateNewPm($this->_userid);
        return $this->fetch('',$output);
    }

    //删除
    public function delete($id){
        $map = [];
        $message =  $this->model_pm->get(['id'=>$id,'userid'=>$this->_userid]) or $this->error('站内信不存在...');
        $this->model_pm->where(['id'=>$id,'userid'=>$this->_userid])->delete() or $this->error('删除失败...');
        $this->success('删除成功...');
    }

    //标记为已读
    public function markRead($id){
        $map = [];
        $output = [];
        $message =  $this->model_pm->get(['id'=>$id,'userid'=>$this->_userid]) or $this->error('站内信不存在...');
        $this->model_pm->where(['id'=>$id])->update(['state'=>PM_STATE_READED]) or $this->error('标记为已读失败...');
        $this->success('标记为已读成功...');
    }

}
