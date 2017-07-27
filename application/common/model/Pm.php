<?php
/**
 * 短消息模型
 *
 */
namespace app\common\model;
use think\Model;
class Pm extends Model{

	protected $auto = [];
    protected $insert = ['addtime'=>TIMES,'state'=>PM_STATE_UNREAD];
    protected $update = [];

    //发送消息
    public function send($to_userid,$title,$content,$from_userid=0){
        if(!is_array($to_userid)){
            $to_userid = array($to_userid); 
        }
        foreach ($to_userid as $k => $uid) {
            $data[$k] = [
                'from_userid' => $from_userid,
                'userid' => $uid,
                'title' => $title,
                'content'   => $content,
            ];
        }
    	$result = $this->saveAll($data);    
    	//处理冗余的消息提示数量
        $this->updateNewPm($to_userid);
    	return $result;
    }

    //批量发送消息
    public function batchSend($users,$title,$content='',$from_userid=0){
        if(is_string($users)){
            $users = explode(',',$users);
        }
        if(!is_array($users)){
            return false;
        }
        return $this->send($users,$title,$content,$from_userid);
    }

    //更逊用户新短消息数量
    public function updateNewPm($userids = []){
        if(empty($userids)){
            return false;
        }
        if(!is_array($userids)){
            $userids = array($userids);
        }
        foreach ($userids as $k => $uid) {
            $newpm = $this->where(['userid' => $uid,'state' => PM_STATE_UNREAD])->count();
            db('member')->where(['userid' => $uid])->update(['newpm' => $newpm]);
        }
        return true;
    }

    public function sendEmail(){

    }

    public function batchSendEmail(){
        
    }

}

?>