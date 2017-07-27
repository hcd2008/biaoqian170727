<?php
namespace app\admin\controller;
use app\common\controller\Admin;
class Common EXTENDS Admin
{
	function __construct(){
		parent::__construct();
		$this->view->engine->layout(false);
	}

	public function main(){
        return $this->fetch();
    }

    public function top(){
        return $this->fetch();
    }

    public function left(){
        return $this->fetch();
    }

}
