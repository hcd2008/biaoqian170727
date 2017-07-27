<?php
namespace app\member\controller;
use app\common\controller\Member;
use app\common\model\Jiancha as model_Jiancha;
use app\common\model\Product as model_product;
use app\common\logic\Jiancha as logic_project;
use think\Request;
use think\Db;
class Jiancha EXTENDS Member
{
    function __construct(){
        parent::__construct();
    }

    //检查项目

    public function index(){
        //产品列表
       //   $model_member = model('Member');
       // $product_list = model('Product')->getProductList();
       //  $member_list = $model_member->all(['dept'=>$this->_dept]);
       //  if(!empty($task_list_users)){
       //      foreach ($member_list as $key => $value) {
       //          if(in_array($value['userid'],$task_list_users)){
       //              $member_list[$key]['locked'] = 1;
       //          }
       //      }
       //  }
       //  foreach ($product_list as $k => $v) {
       //      $product_list[$k]=$v->toArray();
       //  }
       //  foreach ($member_list as $k => $v) {
       //      $member_list[$k]=$v->toArray();
       //  }
       // // $product_list=model('Jiancha')->getProductList();
       //   $this->assign('product_list', $product_list);
       //   $this->assign('member_list', $member_list);
        $model_jiancha = model('Jiancha');
        //$chanpin= $model_member->chanPinList();
         $yifpchanpin= $model_jiancha->fenPeiList();//这个是两个表查但是分页不好使
        //$yifpchanpin= $model_jiancha->chanPinList();
       // $chanpinwu=$model_member->chanPinWuList($chanpin);
        $page = $yifpchanpin['sql']->render();//分页
        $this->assign('yifpchanpin', $yifpchanpin['sql']);
        //$this->assign('chanpinwu', $chanpinwu);
       $this->assign('page', $page);//分页
       $this->assign('count', $yifpchanpin['count']);//分页
        return $this->fetch();
    }
    public function addbiaoqian(){

        $model_member = model('Member');
        $product_list = model('Product')->getProductList();
        $member_list = $model_member->all(['dept'=>$this->_dept]);
        if(!empty($task_list_users)){
            foreach ($member_list as $key => $value) {
                if(in_array($value['userid'],$task_list_users)){
                    $member_list[$key]['locked'] = 1;
                }
            }
        }
        foreach ($product_list as $k => $v) {
            $product_list[$k]=$v->toArray();
        }
        foreach ($member_list as $k => $v) {
            $member_list[$k]=$v->toArray();
        }
       
       // $product_list=model('Jiancha')->getProductList();
         $this->assign('product_list', $product_list);
         $this->assign('member_list', $member_list);
        return $this->fetch();
        }

    public function add(){
        // $cahin=input('post.product_id');
        // $proname=input('get.proname');
        // $model_member = model('Member');
        // $member_list = $model_member->all(['dept'=>$this->_dept]);
        //  foreach ($member_list as $k => $v) {
        //     $member_list[$k]=$v->toArray();
        // }
        //这里开始导入excel表格
         if (! empty ( $_FILES ['file_stu'] ['name'] )){
                $tmp_file = $_FILES ['file_stu'] ['tmp_name'];
                $file_types = explode ( ".", $_FILES ['file_stu'] ['name'] );
                $file_type = $file_types [count ( $file_types ) - 1];
                 /*判别是不是.xls文件，判别是不是excel文件*/
                 if (strtolower ( $file_type ) != "xls"){
                      $this->error ( '不是Excel文件，重新上传' );
                 }
                  /*设置上传路径*/
                /*$savePath = ROOT_PATH . 'public/uploads/Excel/Excel';*/
				$savePath = ROOT_PATH. 'public'.DS.'uploads'.DS.'Excel';
                 /*以时间来命名上传的文件*/
                 $str = date ( 'Ymdhis' ); 
                 $file_name = $str . "." . $file_type;
                 /*是否上传成功*/
                 if (!copy ( $tmp_file, $savePath . $file_name )){
                      $this->error ( '上传失败' );
                  }else{
                    $filename=$savePath . $file_name;
					
                    require(THINK_PATH.'library/Vendor/PHPExcel/PHPExcel.php');//引入PHP EXCEL类
                    $reader = \PHPExcel_IOFactory::createReader('Excel5');//设置以Excel5格式(Excel97-2003工作簿)
                    $PHPExcel = $reader->load($filename); 
                    $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
                    $highestRow = $sheet->getHighestRow(); // 取得总行数
                    $highestColumm = $sheet->getHighestColumn(); // 取得总列数
                    $map = array();
                    for ($row = 2; $row <= $highestRow; $row++){//行数是以第1行开始
                        if($column = 'A'){
                            $map['jcxiang'][] = $sheet->getCell($column.$row)->getValue();
                        }
                    }
                     $map['chanpinid']=input('post.product_id');
                     $map['userid']=input('post.biaoqian_users/a');

                     if($map['chanpinid']!='0'&&$map['userid']!=null&&$map['jcxiang']!=null){
						
                        $ad=model('Jiancha')->addJiancha($map);
                        if($ad){
                            $this->success("添加成功",'member/Jiancha/index');
                         }else{

                            $this->error("添加失败");
                         }
                     }else{
                        $this->error("请填写内容");
                     }
                  }
    
         }else{


         //结束

            $map['chanpinid']=input('post.product_id');{/*input('post.post.product_id')*/}
            $map['userid']=input('post.biaoqian_users/a');
            $map['jcxiang']=array_filter(input('post.jianchaxiang/a'));
           
           if ($map['chanpinid']!='0'&&$map['userid']!=null&&$map['jcxiang']!=null) { 
             
                 $ad=model('Jiancha')->addJiancha($map);
                 if($ad){
                    $this->success("添加成功",'member/Jiancha/index');
                 }else{

                    $this->error("添加失败");
                 }
           }else{
             $this->error("请填写内容");
           }
     
     
     }
      // $this->assign('id', $id);
      // $this->assign('proname', $proname);
      // $this->assign('member_list', $member_list);
      return $this->fetch('Jiancha/index');
  }
    public function edit(){
        $model_member = model('Member');
        $member_list = $model_member->all(['dept'=>$this->_dept]);
        if(!empty($task_list_users)){
            foreach ($member_list as $key => $value) {
                if(in_array($value['userid'],$task_list_users)){
                    $member_list[$key]['locked'] = 1;
                }
            }
        }
         foreach ($member_list as $k => $v) {
            $member_list[$k]=$v->toArray();
        }
         $this->assign('member_list', $member_list);

        $chanpinid=input('get.id');
        $proname=input('get.proname');
        $model_jiancha = model('Jiancha');
        // $cp_list=$model_jiancha->cpList($id);
        // $fpuser=$model_jiancha->fpUser($id);//选中的人
        // $jc_list=$model_jiancha->jcList($id);//检查项
        // $model_member = model('Member');
        // $member_list = $model_member->all(['dept'=>$this->_dept]);
        //  foreach ($member_list as $k => $v) {
        //     $member_list[$k]=$v->toArray();
        // }
        /*
            这里是修改
        */
        if(IS_POST){
            $map['chanpinid']=input('post.chanpinid');
           
            $map['userid']=input('post.userid');
            $map['jcxiang']=array_filter(input('post.jianchaxiang/a'));
            
            $list=$model_jiancha->editUser($map);
            if($list){
                $this->success("修改成功",'member/Jiancha/index');
            }else{
                $this->error("修改失败");
            }

        }
        /*
            结束
        */
        $this->assign('chanpinid', $chanpinid);
        $this->assign('member_list', $member_list);
        $this->assign('fpuser', $fpuser);
        $this->assign('proname', $proname);
        $this->assign('jc_list', $jc_list);
        return $this->fetch();
    }
    public function jianchaxiang(){
        $model_jiancha = model('Jiancha');
        $userid=$_POST['userid'];
        $chanpinid=$_POST['chanpinid'];
        $result=$model_jiancha->jianChaXiang($userid,$chanpinid);
        $a=0;
        foreach ($result as $k => $v) {

           $data.=" <tr id='shan$a' class='qingchu' bgcolor='#ffffff'>
            <th width='20%'>检查项</th>
              <td>
              <label><input  style='width:500px;height:21px; line-height:21px;' type='text' name='jianchaxiang[]'  value='$v[jcxiang]'/><a href='#' onclick=delRow1($a)>删除</a>
              </label>
              </td>
            </tr>";
            $a++;
        }
        return $data;
    }
    public function jianchaxiang1(){
        $model_jiancha = model('Jiancha');
        $userid=$_POST['userid'];
        $chanpinid=$_POST['chanpinid'];
        $result=$model_jiancha->jianChaXiang($userid,$chanpinid);
        $a=0;
        foreach ($result as $k => $v) {

           $data.=" <tr id='shan$a' class='qingchu' bgcolor='#ffffff'>
            <th width='20%'>检查项</th>
              <td>
              <label><input  style='width:500px;' type='text' name='jianchaxiang[]'  value='$v[jcxiang]'/>
              </label>
              </td>
            </tr>";
            $a++;
        }
        return $data;
    }
    public function chakan(){
        $id=input('get.id');
        $proname=input('get.proname');
        $model_jiancha = model('Jiancha');
        $cp_list=$model_jiancha->cpList($id);
        $fpuser=$model_jiancha->fpUser($id);//选中的人
        $jc_list=$model_jiancha->jcList($id);//检查项
        $model_member = model('Member');
        $member_list = $model_member->all(['dept'=>$this->_dept]);
         foreach ($member_list as $k => $v) {
            $member_list[$k]=$v->toArray();
        }
        $this->assign('id', $id);
        $this->assign('member_list', $member_list);
        $this->assign('fpuser', $fpuser);
        $this->assign('proname', $proname);
        $this->assign('jc_list', $jc_list);
        return $this->fetch();
    }
    public function chakan1(){
       $model_member = model('Member');
        $member_list = $model_member->all(['dept'=>$this->_dept]);
        if(!empty($task_list_users)){
            foreach ($member_list as $key => $value) {
                if(in_array($value['userid'],$task_list_users)){
                    $member_list[$key]['locked'] = 1;
                }
            }
        }
         foreach ($member_list as $k => $v) {
            $member_list[$k]=$v->toArray();
        }
         $this->assign('member_list', $member_list);

        $chanpinid=input('get.id');
        $proname=input('get.proname');
        $model_jiancha = model('Jiancha');
        // $cp_list=$model_jiancha->cpList($id);
        // $fpuser=$model_jiancha->fpUser($id);//选中的人
        // $jc_list=$model_jiancha->jcList($id);//检查项
        // $model_member = model('Member');
        // $member_list = $model_member->all(['dept'=>$this->_dept]);
        //  foreach ($member_list as $k => $v) {
        //     $member_list[$k]=$v->toArray();
        // }
        /*
            这里是修改
        */
        if(IS_POST){
            $map['chanpinid']=input('post.chanpinid');
           
            $map['userid']=input('post.userid');
            $map['jcxiang']=array_filter(input('post.jianchaxiang/a'));
            
            $list=$model_jiancha->editUser($map);
            if($list){
                $this->success("修改成功",'member/Jiancha/index');
            }else{
                $this->error("修改失败");
            }

        }
        /*
            结束
        */
        $this->assign('chanpinid', $chanpinid);
        $this->assign('member_list', $member_list);
        $this->assign('fpuser', $fpuser);
        $this->assign('proname', $proname);
        $this->assign('jc_list', $jc_list);
        return $this->fetch();
    }
    public function delete(){
        $id=$_POST['id'];

        $sql=Db::table('bq_jcbq')->where('chanpinid',$id)->delete();
        //$sql1=Db::table('bq_product')->where('pid',$id)->update(['zhuangtai' => 0]);
       return $sql;

    }
    public function downexcel(){
      $file_path=ROOT_PATH . 'public\down\\';
     
      $file_name = "Label.xls";
      if (! file_exists ( $file_path . $file_name )) {    
        $this->error("文件找不到！");    
      } else {   
        $file = fopen ( $file_path . $file_name, "r" );
        Header ( "Content-type: application/octet-stream" );    
        Header ( "Accept-Ranges: bytes" );    
        Header ( "Accept-Length: " . filesize ( $file_path . $file_name ) );    
        Header ( "Content-Disposition: attachment; filename=" . $file_name );
        echo fread ( $file, filesize ( $file_path . $file_name ) );    
        fclose ( $file );    
            }    
        return $this->fetch('addbiaoqian');
    }
    // //创建与编辑项目
    // public function create($id=''){
    //     $op = isset($this->GET['op'])?$this->GET['op']:'';
    //     $logic_project = model('Project','logic');
    //     if(IS_POST){
    //         $logic_project->diyEditAfter($id);
    //         $post = $this->GET;
    //         $post['userid'] = $this->_userid;
    //         $result = $logic_project->create($post);
    //         if(isset($result['state']) && $result['state'] == false){
    //             $this->error($result['msg']);
    //         }
    //         $this->success('操作成功','member/project/index');
    //         exit;
    //     }
    //     if($id){
    //         $map = array();
    //         $map['userid'] = $this->_userid;
    //         $map['id'] = $id;
    //         $project = db('project')->where($map)->find() or $this->error('项目不存在...');
    //         $project['content'] =  db('projectData')->where(['id'=>$id])->value('content');
    //         $project['starttime'] = timetodate($project['starttime'],3);
    //         $project['endtime'] = timetodate($project['endtime'],3);
    //         $project['remindtime'] = timetodate($project['remindtime'],3);
    //         $output['project'] = $project;
    //     }
    //     //默认选中部门
    //     $default_depts = model('userSetting')->where(['userid'=>$this->_userid])->value('setting');
    //     if($default_depts){
    //         $default_depts = explode(',',$default_depts);
    //     }else{
    //         $default_depts = [];
    //     }
    //     //产品列表
    //     $product_list = model('Product')->getProductList();
    //     //可接收任务部门列表
    //     $member_list = model('member')->getTaskMemberList();
    //     //自定义流程
    //     $diy_list = model('diy')->where(['diy_status'=>0,'diy_userid'=>$this->_userid])->select();
    //     $output['diy_list'] = $diy_list;
    //     $output['default_depts'] = $default_depts;
    //     $output['product_list'] = $product_list;
    //     $output['member_list'] = $member_list;
    //     $output['op'] = $op;
    //     return $this->fetch('',$output);
    // }

    // //编辑项目
    // public function edit($id){
    //     $model_project = model('project');
    //     $logic_project = model('Project','logic');
    //     $map = [];
    //     $map['userid'] = $this->_userid;
    //     $map['id'] = $id;
    //     $project = $model_project->where($map)->find() or $this->error('项目不存在...');
    //     $project = $project->toArray();
    //     if(IS_POST){
    //         $result = $logic_project->edit($this->GET);
    //         if(isset($result['state']) && $result['state'] == false){
    //             $this->error($result['msg']);
    //         }
    //         if($project['diy_id']>0){
    //             $editResult = $logic_project->diyEditAfter($id);
    //             $this->redirect('member/diy/start',['id'=>$id,'force'=>true]);
    //         }
    //         $this->success($result['msg'],'member/project/index');
    //         exit;
    //     }
        
    //     $project['content'] =  Db::name('projectData')->where(['id'=>$id])->value('content');
    //     $project['starttime'] = timetodate($project['starttime'],3);
    //     $project['endtime'] = timetodate($project['endtime'],3);
    //     $project['remindtime'] = timetodate($project['remindtime'],3);
    //     $output['project'] = $project;
    //     //产品列表
    //     $product_list = model('Product')->getProductList();
    //     //自定义流程
    //     if($project['progress_total'] == 0){
    //        $diy_list = model('diy')->where(['diy_status'=>0,'diy_userid'=>$this->_userid])->select();
    //         $output['diy_list'] = $diy_list;
    //     }
    //     $output['product_list'] = $product_list;
    //     return $this->fetch('',$output);
    // }


    // //添加评审员
    // public function adduser($id){
    //     $model_project = model('Project');
    //     $model_member = model('member');
    //     $task_member_userids = $notice_users = $notice_users_list = $member_list = $project = [];
    //     $logic_project = model('Project','logic');
    //     if(IS_POST){
    //         $result = $logic_project->addUser($this->GET);
    //         if($result['state'] == false){
    //             $this->error($result['msg']);
    //         }
    //         $this->success($result['msg']);
    //         exit;
    //     }
    //     $project = $model_project->get($id) or $this->error('项目不存在');

    //     //已经收到任务的部门
    //     $dept_task_list = model('deptTask')->where(['project_id'=>$id])->select();
    //     if($dept_task_list){
    //         foreach ($dept_task_list as $key => $value) {
    //             $task_member_userids[$value['task_userid']] = $value['task_userid'];
    //         }
    //     }

    //     //已经收到评审任务的人员
    //     $task_list = model('task')->where(['project_id'=>$id])->select();
    //     if($task_list){
    //         foreach ($task_list as $key => $value) {
    //             $task_member_userids[$value['rev_userid']] = $value['rev_userid'];
    //         }
    //     }

    //     //所有部门
    //     $dept_group = $model_member->getDeptMemberList([
    //         'work_state'    =>  3
    //     ]);
    //     if($dept_group){
    //         foreach ($dept_group as $key => $value) {
    //             if(!empty($value['member_list'])){
    //                 foreach ($value['member_list'] as $k => $v) {
    //                     if(in_array($v['userid'],$task_member_userids)){
    //                         $dept_group[$key]['member_list'][$k]['locked'] = 1;
    //                     }
    //                 }
    //             }else{
    //                 unset($dept_group[$key]);
    //             }
    //         }
    //     }
        
    //     //所有人员
        
    //     $member_list = $model_member->getMemberList([
    //         'work_state'    =>  3
    //     ],'dept ASC');

    //     if($task_member_userids){
    //         foreach ($member_list as $key => $value) {
    //             if(in_array($value['userid'],$task_member_userids)){
    //                 $member_list[$key]['locked'] = 1;
    //             }else{
    //                 $member_list[$key]['locked'] = 0;
    //             }
    //         }
    //     }
        
    //     $output['notice_content'] = '您有一个新标签评审任务，<br>项目名称：'.$project['title'].'，<br />开始日期：'.timetodate($project['starttime']).'<br />截止日期：'.timetodate($project['endtime']).'，<br />【'.$this->_dept_name.'】'.$this->_realname.'<br><br />请及时处理。';
    //     $output['project'] = $project->toArray();
    //     $output['dept_group_list'] = $dept_group;
    //     return $this->fetch('',$output);
    // }

    // //项目添加部门
    // public function adddept($id){
    //     $model_project = model('Project');
    //     $model_member = model('member');
    //     $notice_users = $notice_users_list = $member_list = $project = [];
    //     $logic_project = model('Project','logic');
    //     if(IS_POST){
    //         $result = $logic_project->addDept($this->GET);
    //         if($result['state'] == false){
    //             $this->error($result['msg']);
    //         }
    //         $this->success($result['msg']);
    //         exit;
    //     }
    //     $project = $model_project->get($id) or $this->error('项目不存在');

    //     //已经收到任务的部门
    //     $dept_task_list = model('deptTask')->where(['project_id'=>$id])->select();
    //     if($dept_task_list){
    //         foreach ($dept_task_list as $key => $value) {
    //             $task_deptids[$value['task_deptid']] = $value['task_deptid'];
    //         }
    //     }

    //     $member_list = $model_member->getTaskMemberList();
    //     if(!empty($task_deptids)){
    //         foreach ($member_list as $key => $value) {
    //             if(in_array($value['depid'],$task_deptids) ){
    //                 $member_list[$key]['locked'] =1;
    //             }
    //         }
    //     }
    //     $output['notice_content'] = '您有一个新标签评审任务，<br>项目名称：'.$project['title'].'，<br />开始日期：'.timetodate($project['starttime']).'<br />截止日期：'.timetodate($project['endtime']).'，<br />【'.$this->_dept_name.'】'.$this->_realname.'<br><br />请及时处理。';
    //     $output['project'] = $project->toArray();
    //     $output['member_list'] = $member_list;
    //     return $this->fetch('',$output);
        
    // }

    // //终止项目
    // public function stop($id){
    //     $map = array();
    //     $map['userid'] = $this->_userid;
    //     $map['id'] = $id;
    //     $project = db('project')->where($map)->find() or $this->error('项目不存在无法终止...');
    //     $result = db('project')->where($map)->update(['state' => PROJECT_STATE_STOP,'stoptime'=>TIMES]);
    //     $this->addActionLog('project',$id,'用户 {realname}({username}) 终止项目');
    //     if($result){
    //         $this->success('终止项目成功');
    //         exit;
    //     }
    //     $this->error('终止项目失败');
    // }

    // //项目详情
    // public function show($id){
    //     $model_project = model('Project');
    //     $model_diy = model('diy');
    //     $project = $model_project->get(['id'=>$id]) or $this->error('项目不存在...');
    //     $project_detail = $project->toArray();
    //     if($diy_id = $project['diy_id']){//自定义流程
    //         $diy_conf = $model_diy->get(['diy_id'=>$diy_id]) or $this->error('自定义流程不存在...');
    //         $diy_content = unserialize($diy_conf['diy_content']);
    //         foreach ($diy_content as $k => $v) {
    //             $depts = $users = [];
    //             $depts = $v['depts'];
    //             $users = $v['users'];
    //             if($k > $project['diy_step']){
    //                 $diy_content[$k]['status'] = 0;
    //                 if($depts){
    //                     $diy_content[$k]['depts'] = Db::name('dept')->where(['depid'=>['in',$depts]])->select();
                       
    //                 }
    //                 if($users){
    //                     $diy_content[$k]['users'] = Db::name('member')->where(['userid'=>['in',$users]])->select();
    //                 }
    //             }else{
    //                 $diy_content[$k]['status'] = 1;
    //                 if($depts){
    //                     $diy_content[$k]['depts'] = Db::view('deptTask','*')->view('dept','dept_name','deptTask.task_deptid=dept.depid')->where(['project_id'=>$id,'task_deptid'=>['in',$depts]])->select();
    //                 }
    //                 if($users){
    //                     $diy_content[$k]['users'] = Db::view('task','*')->view('member','realname','task.rev_userid=member.userid')->where(['project_id'=>$id,'task.rev_userid'=>['in',$users]])->select();
    //                 }
    //             }
    //         }
            
    //         $project_detail['task_list'] = $diy_content;
    //     }else{
    //         $project_detail = $model_project->getProjectDetail($id) or $this->error('项目不存在...');
    //     }
        
    //     $output['project_detail'] = $project_detail;
    //     return $this->fetch('',$output);
    // }

    // public function review_report($id,$revid){
    //     $output = [];
    //     $model_project = model('Project');
    //     $model_task = model('Task');
    //     $model_question = model('taskQuestion');
    //     $project = $model_project->get(['id'=>$id]) or $this->error('项目不存在...');
    //     $rev_info = $model_task->getReviewInfo([
    //         'revid' =>  $revid,
    //     ]);
    //     $user_info = model('member')->getUserInfo($rev_info['rev_userid']);
    //     $question_list = $model_question->getQuestionList([
    //         'revid' =>  $revid,
    //     ]);
    //     $output['project'] = $project;
    //     $output['rev_info'] = $rev_info;
    //     $output['question_list'] = $question_list;
    //     $output['user_info'] = $user_info;
    //     return $this->fetch('',$output);
    // }

}