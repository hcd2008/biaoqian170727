<?php
/**
 * 企业模型
 *
 */
namespace app\common\model;
use think\Model;
class Company extends Model{

	public $modelName = '企业信息';

	public $insertFields = [
		['name'=>'com_id','type'=>'hidden','title'=>'','help'=>'','option'=>'','required'=>true],
		['name'=>'com_type','type'=>'select','title'=>'企业类型','help'=>'','option'=>'getTypes','required'=>true],
		['name'=>'com_name','type'=>'text','title'=>'企业名称','help'=>'','required'=>true],
		['name'=>'com_code','type'=>'text','title'=>'企业代号','help'=>'','option'=>'','required'=>false],
		['name'=>'com_address','type'=>'text','title'=>'企业地址','help'=>'','option'=>'','required'=>true],
		['name'=>'com_origin','type'=>'text','title'=>'产地','help'=>'','option'=>'','required'=>true],
		['name'=>'com_zipcode','type'=>'text','title'=>'邮编','help'=>'','option'=>'','required'=>false],
		['name'=>'com_qs','type'=>'text','title'=>'QS号','help'=>'','option'=>'','required'=>true],
		['name'=>'com_otherinfo','type'=>'textarea','title'=>'其他信息','help'=>'','option'=>'','required'=>false],
	];

	public function getTypes(){
		$ST = cache('setting');
		return explode('|','请选择|'.$ST['com_type']);
	}

	public function createTable(){
		$prefix = config('database.prefix');
		
		$sql = "CREATE TABLE `{$prefix}company` (";
		$fileds = "`id` int(10) unsigned NOT NULL AUTO_INCREMENT,";
		foreach ($this->insertFields as $k => $v) {
			$fileds .= "`{$v['name']}` varchar(255) NOT NULL DEFAULT '' COMMENT '{$v['title']}',";
		}
		$fileds .= ' PRIMARY KEY (`id`) ';
		$fileds = trim($fileds,',');
		$sql .= $fileds.") ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='".$this->modelName."';";
		$this->query($sql);
	}
}
?>