<?php
/**
 * 分类模型
 *
 */
namespace app\common\model;
use think\Model;
class Category extends Model{

	public $modelName = '分类';

	public $insertFields = [
		['name'=>'catid','type'=>'hidden','title'=>'','help'=>'','option'=>'','required'=>true],
		['name'=>'catname','type'=>'text','title'=>'分类名称','help'=>'','option'=>'getTypes','required'=>true],
		['name'=>'model','type'=>'hidden','title'=>'所属模型','help'=>'','option'=>'','required'=>false],
		['name'=>'listorder','type'=>'text','title'=>'排序','help'=>'','option'=>'','required'=>false],
	];

	public function createTable(){
		$prefix = config('database.prefix');
		
		$sql = "CREATE TABLE `{$prefix}category` (";
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