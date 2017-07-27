<?php 
namespace app\common\logic;
class Member {

	public static function find(){
		
	}

	public static function select(){
		
	}

	public static function update(){

	}

	public static function delete(){

	}

	public static function add(){

	}

	public static function count(){

	}



	public function __call($method, $args){
		throw new \think\Exception($method.'函数不存在');
	}

	public static function __callStatic($method,$arg){
		throw new \think\Exception($method.'静态函数不存在');
	}

}
?>