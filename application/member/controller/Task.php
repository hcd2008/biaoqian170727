<?php
namespace app\member\controller;
use app\common\controller\Member;
use think\Db;
class Task EXTENDS Member
{
    function __construct(){
        parent::__construct();
        $this->model_task = model('Task');
        $this->model_task_file = model('taskFile');
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
        /* 2017.4.6 评审*/
        $anew=$this->GET['anew'];

        $pingshen=$this->model_task_file->pingShen($revid,$this->_userid);
        $arr=array();
        foreach ($pingshen as $k => $v) {
            
            $p['qs_ext1']=$v['qs_ext1'];
            $p['qs_ext2']=$v['qs_ext2'];
            $p['qs_ext3']=$v['qs_ext3'];
            $p['type']=Db::table('bq_question')->where('qid',$v['qs_typeid'])->value('title');
            $p['area']=Db::table('bq_question')->where('qid',$v['qs_areaid'])->value('title');
            $arr[]=$p;
        }
        $chanpinidm=$this->model_task_file->chanpPnidM($revid,$this->_userid);
        foreach ($chanpinidm as $k => $v) {
            $pid=$v['pid'];
        }

        $chanpinjc=$this->model_task_file->chanPinJc($pid,$this->_userid);
        // foreach ($chanpinjc as $k => $v) {
        //     if($v['zhuangtai']==1){$checked='checked';}else{$checked='';}
        //     $data.=" <input type='checkbox' id='jianchaxiang[]' $checked    name='itemid[]' value='$v[itemid]'>$v[jcxiang]<br/>";
        // }
        /* 2017.4.6 评审*/
        $pingshen_area=$this->model_task_file->pingshen_area($revid,$this->_userid);

        /* 2017.4.6 检查项*/
        $pingshen_area=$this->model_task_file->pingshen_area($revid,$this->_userid);
        //检查表
        // $jiancha=$this->model_task->jianChabiao($this->_userid);
        //按照userid查找他的产品
        $chanpin=$this->model_task_file->chanPin($this->_userid,$chanpinid);
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
        //图片及附件 by hcd 0713
        $filearr=Db::name('project_file')->where('proid',$proid)->order('id')->select();
        $filelist=array();
        foreach($filearr as $kk=>$vv){
            //查一下已经评审的信息数量
            $pssum=Db::name('task_question')->where('fileid',$vv['id'])->where('qs_userid',$this->_userid)->count();
            // // 是否已经评审
            $psres=Db::name('task_file')->where('fid',$vv['id'])->where('rev_userid',$this->_userid)->find();
            isset($psres['rev_result_state']) or $pares['rev_result_state']=0;
            //判断文件后缀
            $fileexts=explode(".", $vv['file']);
            $fileext=$fileexts[1];
            if($fileext=='doc'||$fileext=='docx'){
                $vv['ext']=1;
            }elseif($fileext=='pdf'){
                $vv['ext']=2;
            }else{
                $vv['ext']=0;
            }
            if(!empty($psres['rev_attach'])){
                $vv['psfile']=$psres['rev_attach'];
            }
            $vv['pssum']=$pssum;
            $vv['psres']=$psres['rev_result_state'];
            $filelist[$vv['id']]=$vv;
        }
        // print_r($filelist);exit;
        $param=$this->request->param();
        $banb=$param['banb'];
        $fileid=isset($param['fileid'])?$param['fileid']:$filearr[0]['id'];
        $this->assign('banb',$banb);
        $this->assign('fileid',$fileid);
        $this->assign('revid',$param['revid']);
        $output['files']=$filelist;


        $project_info['product_name']=Db::name("product")->where('pid',$project_info['product_id'])->value('proname');
        $project_info['content']=Db::name("project_data")->where('id',$project_info['id'])->value('content');
        $project_info['data']=date("Y-m-d H:i:s", $project_info['addtime']);
        $output['project_info'] = $project_info;

        $output['jiancha']=$jiancha;
        $output['chanpin']=$chanpin;

        $output['chanpinidm']=$chanpinidm;
        $output['arr']=$arr;
        $output['chanpinjc']=$chanpinjc;
       $output['data']=$data;
       $output['anew']=$anew;


       return $this->fetch('',$output);
        
    }
    /**
     * wordp评审
     * @Author   黄传东
     * @DateTime 2017-07-20T08:09:59+0800
     * @return   [type]                   [description]
     */
    public function stepword(){
        $param=$this->request->param();
        $banb=$param['banb'];
        $fileid=isset($param['fileid'])?$param['fileid']:$filearr[0]['id'];
        $revid=$param['revid'];
        $this->assign('banb',$banb);
        $this->assign('fileid',$fileid);
        $this->assign('revid',$revid);
        //任务详情
        $taskinfo=Db::name('task')->where('revid',$revid)->find();
        $pid=$taskinfo['project_id'];
        $proinfo=Db::name('project')->where('id',$pid)->find();
        $proid=$proinfo['proid'];
        //图片及附件 by hcd 0713
        $filearr=Db::name('project_file')->where('proid',$proid)->order('id')->select();
        $filelist=array();
        foreach($filearr as $kk=>$vv){
            //查一下已经评审的信息数量
            $pssum=Db::name('task_question')->where('fileid',$vv['id'])->where('qs_userid',$this->_userid)->count();
            // // 是否已经评审
            $psres=Db::name('task_file')->where('fid',$vv['id'])->where('rev_userid',$this->_userid)->find();
            isset($psres['rev_result_state']) or $pares['rev_result_state']=0;
            //判断文件后缀
            $fileexts=explode(".", $vv['file']);
            $fileext=$fileexts[1];
            if($fileext=='doc'||$fileext=='docx'){
                $vv['ext']=1;
            }elseif($fileext=='pdf'){
                $vv['ext']=2;
            }else{
                $vv['ext']=0;
            }
            if(!empty($psres['rev_attach'])){
                $vv['psfile']=$psres['rev_attach'];
            }
            $vv['pssum']=$pssum;
            $vv['psres']=$psres['rev_result_state'];
            $filelist[$vv['id']]=$vv;
        }
        $this->assign('proid',$proid);
        $this->assign('files',$filelist);
        //判断是否已经评审过了
        $istj=Db::name('task_file')->where('fid',$fileid)->where('rev_userid',$this->_userid)->count();
        if($istj){
            $list=Db::name('task_file')->where('fid',$fileid)->where('rev_userid',$this->_userid)->find();
            $this->assign('list',$list);
            return $this->fetch('stepword_edit');
        }else{
            return $this->fetch();
        }
        
        
    }
    /**
     * 总体评价
     * @Author   黄传东
     * @DateTime 2017-07-20T11:02:58+0800
     * @return   [type]                   [description]
     */
    public function stephuizong(){
        $param=$this->request->param();
        $banb=$param['banb'];
        // $fileid=isset($param['fileid'])?$param['fileid']:$filearr[0]['id'];
        $revid=$param['revid'];
        $this->assign('banb',$banb);
        // $this->assign('fileid',$fileid);
        $this->assign('revid',$revid);
        //任务详情
        $taskinfo=Db::name('task')->where('revid',$revid)->find();
        $pid=$taskinfo['project_id'];
        $proinfo=Db::name('project')->where('id',$pid)->find();
        $proid=$proinfo['proid'];
        //图片及附件 by hcd 0713
        $filearr=Db::name('project_file')->where('proid',$proid)->order('id')->select();
        $filelist=array();
        foreach($filearr as $kk=>$vv){
            //查一下已经评审的信息数量
            $pssum=Db::name('task_question')->where('fileid',$vv['id'])->where('qs_userid',$this->_userid)->count();
            // // 是否已经评审
            $psres=Db::name('task_file')->where('fid',$vv['id'])->where('rev_userid',$this->_userid)->find();
            isset($psres['rev_result_state']) or $pares['rev_result_state']=0;
            //判断文件后缀
            $fileexts=explode(".", $vv['file']);
            $fileext=$fileexts[1];
            if($fileext=='doc'||$fileext=='docx'){
                $vv['ext']=1;
            }elseif($fileext=='pdf'){
                $vv['ext']=2;
            }else{
                $vv['ext']=0;
            }
            if(!empty($psres['rev_attach'])){
                $vv['psfile']=$psres['rev_attach'];
            }
            $vv['pssum']=$pssum;
            $vv['psres']=$psres['rev_result_state'];
            $filelist[$vv['id']]=$vv;
        }

        $this->assign('proid',$proid);
        $this->assign('files',$filelist);
        //判断是否已经评审过了
        $istj=Db::name('task')->where('revid',$revid)->count();
        $list=array();
        if($istj){
            $list=Db::name('task')->where('revid',$revid)->find();
        }else{
            $list['rev_result_state']=20;
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
    //显示检查项
    public function jianchaxiang(){
        //  $d=$_GET['chanpinid'];
        // dump($id);exit();
        //检查表
        $id=$_POST['chanpinid'];
       
        $jiancha=$this->model_task->jianChabiao($this->_userid,$id);
        
        foreach ($jiancha as $k => $v) {
            if($v['zhuangtai']==1){$checked='checked';}else{$checked='';}
            $data.=" <input type='checkbox' id='jianchaxiang[]' $checked    name='itemid[]' value='$v[itemid]'>$v[jcxiang]<br/>";
        }
         return $data;
    }
    //勾选检查项，修改状态
    public function gouxuan(){
        $map=[];
        $revid=input('post.revid');
      
        $map['chanpinid']=input('post.chanpinid');
        $map['itemid']=input('post.itemid/a');
        $map['userid']=$this->_userid;
        $edit=$this->model_task->edit($map);
        if(edit){
           $url=url('member/Task/step1','revid='.$revid);
           
             $this->error("评审成功",$url);
         }else{
             $this->error("评审失败");
         }
    }
    // //查找已经评审的检查项
    // public function wancheng(){
    //      $id=$_POST['chanpinid1'];
    //      $wan=$this->model_task->wanCheng($this->_userid,$id);
    //      foreach ($wan as $k => $v) {
    //          $data.="<span>$v[jcxiang]</span><br/>";
    //      }
    //      return $data;
    // }

    //提交评审结果
    public function step2(){
        $proid = $this->GET['proid'];
        $revid = $this->GET['revid'];
        $fid=$this->GET['fid'];
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
        $filename = $path.$this->_userid.'_'.$fid.'.'.$ext;
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
        $data['fid']=$fid;
        $data['revid']=$revid;
        $data['rev_userid']=$this->_userid;

        // $resutl = $this->model_task_file->save($data,['fid'=>$fid,'revid'=>$revid,'rev_userid'=>$this->_userid]);
        // if($this->model_task_file->getError()){
        //     $this->error($this->model_task_file->getError());
        // }
        $isup=Db::name('task_file')->where('fid',$fid)->where('rev_userid',$this->_userid)->count();
        if($isup){
            unset($data['fid']);
            unset($data['rev_userid']);
            Db::name('task_file')->where('fid',$fid)->where('rev_userid',$this->_userid)->update($data);
        }else{
            Db::name('task_file')->insert($data);
        }
        $this->success('操作成功');
    }
    /**
     * word结果提交
     * @Author   黄传东
     * @DateTime 2017-07-20T09:49:57+0800
     * @return   [type]                   [description]
     */
    public function step2word(){
        $param=$this->request->param();
        $fid=$param['fileid'];
        $revid=$param['revid'];
        $rev_state=$param['result'];
        $fileinfo=$this->request->file('resfile');
        if($fileinfo){
            $info=$fileinfo->validate(['ext'=>'doc,docx'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                $filename= $info->getFilename(); 
                $filename=date('Ymd') . DS.$filename;
                //插入图片信息
                
                $data['rev_attach']=$filename;
            }else{
                $this->error('请上传一个word文件');
            }
        }
       
        $data['rev_state'] = REVIEW_STATE_OK;
        $data['rev_result_state'] = $rev_state==1?REVIEW_RESULT_STATE_PASS:REVIEW_RESULT_STATE_REJECT;
        $data['fid']=$fid;
        $data['revid']=$revid;
        $data['rev_userid']=$this->_userid;

        $isup=Db::name('task_file')->where('fid',$fid)->where('rev_userid',$this->_userid)->count();
        if($isup){
            unset($data['fid']);
            unset($data['rev_userid']);
            Db::name('task_file')->where('fid',$fid)->where('rev_userid',$this->_userid)->update($data);
        }else{
            Db::name('task_file')->insert($data);
        }
        $this->success('操作成功');
    }

    public function step2huizong(){
        $param=$this->request->param();
        $revid=$param['revid'];
        $rev_state=$param['result'];
        $data['rev_state'] = REVIEW_STATE_OK;
        $data['rev_result_state'] = $rev_state==1?REVIEW_RESULT_STATE_PASS:REVIEW_RESULT_STATE_REJECT;
        $data['revid']=$revid;
        $data['rev_userid']=$this->_userid;

        unset($data['rev_userid']);
        Db::name('task')->where('revid',$revid)->where('rev_userid',$this->_userid)->update($data);
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

            $tupian1=strstr( $res[0]['bqfile'], 'public'); 
            $tupian1=ROOT_PATH .$tupian1;        
            $tupian1=$this->base64EncodeImage($tupian1);
            //$res[0]['bqfile']=$this->base64EncodeImage($res[0]['bqfile']);
            $tupian2=strstr( $res[1]['bqfile'], 'public'); 
            $tupian2=ROOT_PATH .$tupian2;
            $tupian2=$this->base64EncodeImage($tupian2);
           //$res[1]['bqfile']=$this->base64EncodeImage($res[1]['bqfile']);
            $output['v1']=$res[0];
            $output['v2']=$res[1];
            $output['tupian1']=$tupian1;
            $output['tupian2']=$tupian2;
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
    public function report_detail(){
        // $output['can_edit'] = false;
        // $project_info = $this->getReviewInfo($revid);
        // $question_list = $this->model_question->getQuestionList([
        //     'revid' =>  $revid,
        // ]);
        // $output['question_list'] = $question_list;
        // $output['project_info'] = $project_info;
        // //print_r($project_info);
        // $touser_info = model('member')->getUserInfo($project_info['rev_userid']);
        // if($project_info['rev_userid'] == $this->_userid || $touser_info['dept'] == $this->_dept){
        //     $output['can_edit'] = true;
        // }
        // // print_r($output);exit;
        // return $this->fetch('',$output);
        $param=$this->request->param();
        isset($param['revid']) or $this->error('非法访问');
        //总体评价
        $ztres=Db::name('task')->where('revid',$param['revid'])->where('rev_userid',$this->_userid)->find();
        $this->assign('ztres',$ztres);
        //各个文件评价
        $reslists=Db::name('task_file')->where('revid',$param['revid'])->where('rev_userid',$this->_userid)->select();
        $newlist=array();
        foreach ($reslists as $k => $v) {
            $img=$v['rev_attach'];
            if($img!=''){
                $imgarr=explode('.',$img);
                if($imgarr[1]=='doc'||$imgarr[1]=='docx'){
                    $v['state']=1;
                }else{
                    $v['state']=0;
                }
            }
            //评审详情
            $psdetail=Db::name('task_question')->where('fileid',$v['fid'])->where('revid',$v['revid'])->where('qs_userid',$this->_userid)->order('qsid')->select();
            $v['question']=$psdetail;
            $newlist[]=$v;
        }
        $areaarr=Db::name('question')->where('type',1)->select();
        $typearr=Db::name('question')->where('type',2)->select();
        foreach ($areaarr as $k => $v) {
            $arealist[$v['qid']]=$v['title'];
        }
        foreach ($typearr as $kk=> $vv) {
            $typelist[$vv['qid']]=$vv['title'];
        }
        $this->assign('arealist',$arealist);
        $this->assign('typelist',$typelist);
        $this->assign('lists',$newlist);
        return $this->fetch();
    }

    //问题
    public function question_add(){
        
        $revid = $this->GET['revid'];
        $qdesc = $this->GET['qdesc'];
        $qctype = $this->GET['qctype'];
        $qarea = $this->GET['qarea'];
        $xiangqing = $this->GET['xiangqing'];
        $fileid=$this->GET['fileid'];
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
        $data['fileid']  =  $fileid;
        
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
   function base64EncodeImage ($file) { 
    $type=getimagesize($file);//取得图片的大小，类型等 
    switch($type[2]){//判读图片类型  
    case 1:$img_type="gif";break;  
    case 2:$img_type="jpg";break;  
    case 3:$img_type="png";break;  
    }
    $binary =  file_get_contents($file);//此函数可安全用于二进制对象。也可用来获取网络URL数据，但是如果目标服务器关闭了allow_url_fopen选项，那么此方法将失败。说到这里，见网上有高人用此方法模拟referer,cookie,proxy ：
    $file_content = base64_encode($binary);
    $img='data:image/'.$img_type.';base64,'.$file_content;//合成图片的base64编码  
    return $img;
}
}
