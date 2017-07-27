<?php
/**
 * 标签模型
 *
 */
namespace app\common\model;
use think\Model;
class ProjectVersion extends Model{
	protected $auto = [];
    protected $insert = ['addtime'=>TIMES,'state'=>1,'version'=>1]; 
    protected $update = [];  
}
?>