<?php
/**
 * 归档模型
 *
 */
namespace app\common\model;
use think\Model;
class Archive extends Model{

	protected $auto = [];
    protected $insert = ['arc_addtime'=>TIMES];
    protected $update = [];
}
?>