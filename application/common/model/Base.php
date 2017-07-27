<?php
/**
 * 自定义模型父类
 *
 */
namespace app\common\model;
use think\Model;
class Base extends Model{

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

	/**
     * 父类Model对象重写.自动将数组按","号切割
     * @access public
     * @param array     $data 数据
     * @param array     $where 更新条件
     * @param bool      $getId 新增的时候是否获取id
     * @param bool      $replace 是否replace
     * @return integer
     */
	public function save($data = [], $where = [], $getId = true, $replace = false){
		foreach ($data as $key => $value) {
			if(is_array($value)){
				$data[$key] = implode(',',$value);
			}
		}
		return parent::save($data, $where, $getId, $replace);
	}

}
?>