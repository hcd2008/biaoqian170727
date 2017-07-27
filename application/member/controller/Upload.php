<?php
namespace app\member\controller;
use app\common\controller\Base;
class Upload EXTENDS Base
{
	function __construct(){
		parent::__construct();
	}

    public function index(){
        $file = request()->file('file');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 jpg
            //echo $info->getExtension();
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            //echo $info->getFilename();
            $url = $info->getFilename();
            echo '<script type="text/javascript">';
            echo 'try{parent.document.getElementById("bqfile").src="'.$url.'";}catch(e){}parent.document.getElementById("bqfile").value="'.$url.'";window.parent.layer.closeAll();';
            echo '</script>';
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }

    public function iframe(){
        $file = request()->file('file');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            $file_name = $info->getFilename();
            $url = $this->request->domain().'/public/uploads/'.date('Ymd').'/'.$file_name;
            echo '<script type="text/javascript">';
            echo 'parent.document.getElementById("bqfile").value="'.$url.'";window.parent.layer.closeAll();';
            echo '</script>';
        }else{
            echo '<script type="text/javascript">';
            echo 'alert("'.$file->getError().'")';
            echo '</script>';
        }
    }
}
