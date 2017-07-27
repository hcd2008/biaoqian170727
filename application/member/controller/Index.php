<?php
namespace app\member\controller;
use app\common\controller\Member;
class Index EXTENDS Member
{
	function __construct(){
		parent::__construct();
	}

    public function index(){
    	$this->redirect('member/label/index');
    }

}
