<?php
namespace app\member\controller;
use app\common\controller\Member;
use think\Db;
class Task EXTENDS Member
{
    function __construct(){
        parent::__construct();
        $this->model_task = model('Task');
        $this->model_qu = model('question');
        $this->model_question = model('taskQuestion');
        $this->logic_review = model('Review','logic');
        $this->question_area_list = $this->model_qu->getAreaList();
        $this->question_type_list = $this->model_qu->getTypeList();
    }

    //标签评审首页
    public function index(){
        $map = [];
        $state = isset($this->GET['state'])?$this->GET['state']:'1';
        if($state == REVIEW_STATE_REPORT){
            $map['rev_state'] = REVIEW_STATE_REPORT;
            $map['state'] = 3;
        }else{
            $state = 1;
            $map['state'] = 1;
            $map['rev_state'] = ['in',[REVIEW_STATE_NEW,REVIEW_STATE_OK]];
        }
        $map['rev_userid'] = $this->_userid;
        if(!empty($this->kw)){
            $map['title'] = ['like',"%{$this->kw}%"];
        }
        
        $review_list = $this->model_task->getReviewList($map);
        #echo  $this->model_task->getLastSql();
        $output['review_list'] = $review_list;
        $output['state'] = $state;
        return $this->fetch('',$output);
    }

    //查看项目基本信息
    public function show($revid){
        $project_info = $this->getReviewInfo($revid);
        $output['review'] = $project_info;
        return $this->fetch('',$output);
    }

    //评审
    public function step1($revid){

        $output = [];

        //评审任务信息
        $project_info = $this->getReviewInfo($revid);

        //检查是否可以评审
        $result = $this->logic_review->check_step(1,$project_info);
        if(!$result['state']){
            $this->error($result['msg']);
        }
        
        //拼接信息
        if($project_info['rev_attach'] && empty($this->GET['anew'])){
            $project_info['pic'] = $project_info['rev_attach'];
        }else{
            $project_info['pic'] = $project_info['bqfile'];
        }
        $project_info['pic'] = $project_info['pic'].'?t='.time();
        
        $unit = $project_info['unit'];
        if($unit == "m"){
            $project_info['size'] = $project_info['real_length']*1000;
        }elseif($unit == "cm"){
            $project_info['size'] = $project_info['real_length']*10;
        }elseif($unit == "mm"){
            $project_info['size'] = $project_info['real_length'];
        }
        $project_info['question_type'] = $this->question_type_list;
        $project_info['question_area'] = $this->question_area_list;
        
        //该项目的版本
        $proid=$project_info['proid'];
        $bb=Db::name("project")->where('proid',$proid)->order("id")->select();
        $output['bb']=$bb;
        $output['project_info'] = $project_info;
        return $this->fetch('',$output);
    }

    //提交评审结果
    public function step2(){
        $proid = $this->GET['proid'];
        $revid = $this->GET['revid'];
        $rev_state = $this->GET['rev_state'];
        $base64_data = $this->GET['base64_data'];
        
        //评审任务信息
        $project_info = $this->getReviewInfo($revid);
        
        //检查是否可以提交评审结果
        $result = $this->logic_review->check_step(2,$project_info);
        if(!$result['state']){
            $this->error($result['msg']);
        }

        //检查上传的图片
        if (!preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_data, $match)){
            $this->error('图片保存失败(1)');
        }

        $ext = $match[2];
        $bin = base64_decode(str_replace($match[1], '', $base64_data));
        $path = ROOT_PATH.'public'.DS.'review'.DS.$proid.DS;
        $filename = $path.$this->_userid.'.'.$ext;
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                $this->error("目录 {$path} 创建失败！");
            }
        }
        if(!file_put_contents($filename, $bin)){
            $this->error('图片保存失败(2)');
        }

        $data['rev_state'] = REVIEW_STATE_OK;
        $data['rev_result_state'] = $rev_state==1?REVIEW_RESULT_STATE_PASS:REVIEW_RESULT_STATE_REJECT;
        $data['rev_attach'] = $this->request->domain().'/'.str_replace('\\','/',str_replace(ROOT_PATH,'',$filename));
        $resutl = $this->model_task->save($data,['revid'=>$revid]);
        if($this->model_task->getError()){
            $this->error($this->model_task->getError());
        }
        $this->success('操作成功');
    }

    //提交报告
    public function step3($revid){
        $logic_project = model('project','logic');
        $project_info = $this->getReviewInfo($revid);
        //检查是否可以提交评审报告
        $result = $this->logic_review->check_step(3,$project_info);
        if(!$result['state']){
            $this->error($result['msg']);
        }
        $result = $logic_project->review_report($project_info);
        $this->success($result['msg']);
    }
    //标签比对
    public function compare(){
        if($this->request->isPost()){
            $param=$this->request->param();
            $res=array();
            foreach ($param['lv'] as $k=> $v) {
                $info=Db::name('project')->where("id",$v)->find();
                $res[]=$info;
            }
            $output['v1']=$res[0];
            $output['v2']=$res[1];
            return $this->fetch('',$output);  
        }
       
    }
    private function getReviewInfo($revid){
        $project_info = $this->model_task->getReviewInfo([
            'revid' =>  $revid,
        ]) or $this->error('评审任务不存在...');
       return $project_info;
    }

    //报告详情
    public function report_detail($revid){
        $output['can_edit'] = false;
        $project_info = $this->getReviewInfo($revid);
        $question_list = $this->model_question->getQuestionList([
            'revid' =>  $revid,
        ]);
        $output['question_list'] = $question_list;
        $output['project_info'] = $project_info;
        //print_r($project_info);
        $touser_info = model('member')->getUserInfo($project_info['rev_userid']);
        if($project_info['rev_userid'] == $this->_userid || $touser_info['dept'] == $this->_dept){
            $output['can_edit'] = true;
        }
        return $this->fetch('',$output);
    }

    //问题
    public function question_add(){
        
        $revid = $this->GET['revid'];
        $qdesc = $this->GET['qdesc'];
        $qctype = $this->GET['qctype'];
        $qarea = $this->GET['qarea'];
        $xiangqing = $this->GET['xiangqing'];
        $yiju = $this->GET['yiju'];

        $project_info = $this->getReviewInfo($revid);
        //检查是否可以添加问题
        $result = $this->logic_review->check_step('question_add',$project_info);
        if(!$result['state']){
            $this->error($result['msg']);
        }

        $data['revid'] =  $revid;
        $data['qs_typeid'] =  $qctype;
        $data['qs_areaid'] =  $qarea;
        $data['qs_userid'] =  $this->_userid;
        $data['qs_ext1'] =  $qdesc;
        $data['qs_ext2'] =  $yiju;
        $data['qs_ext3'] =  $xiangqing;
        $resutl = $this->model_question->save($data);
        if($this->model_question->getError()){
            $this->error($this->model_question->getError());
        }
        $this->success('操作成功');
    }

    //问题编辑
    public function question_edit($qsid){
        
        $output = $this->model_question->get([
            'qsid' =>  $qsid,
        ]) or $this->error('报告问题不存在...');

        $project_info = $this->getReviewInfo($output['revid']);
        //检查是否可以编辑问题
        $result = $this->logic_review->check_step('question_edit',$project_info);
        if(!$result['state']){
            $this->error($result['msg']);
        }

        if(IS_POST){
            $data['qs_typeid'] = $this->GET['typeid'];
            $data['qs_areaid'] = $this->GET['areaid'];
            $data['qs_ext1'] = $this->GET['title'];
            $data['qs_ext2'] = $this->GET['yiju'];
            $data['qs_ext3'] = $this->GET['content'];
            $map['qsid'] = $this->GET['qsid'];
            $this->model_question->isUpdate(true)->save($data,$map);
            $this->success('编辑成功');
            exit;
        } 
        $output['question_type'] = $this->question_type_list;
        $output['question_area'] = $this->question_area_list;
        return $this->fetch('',$output->toArray());
    }

    //问题删除
    public function question_delete($qsid){
        
        $item  = $this->model_question->get([
            'qsid' =>  $qsid,
        ]) or $this->error('报告问题不存在...');
        $project_info = $this->getReviewInfo($item['revid']);

        //检查是否可以删除问题
        $result = $this->logic_review->check_step('question_delete',$project_info);
        if(!$result['state']){
            $this->error($result['msg']);
        }

        $this->model_question->where(['qsid' =>  $qsid])->delete();
        $this->success('删除成功');
    }
}
