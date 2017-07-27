<?php
namespace think\template\taglib;
use think\template\TagLib;

/**
 * DT标签库解析类
 * @package  Think
 * @author    Lipeng
 */
class Dt extends Taglib
{
	// 标签定义
    protected $tags = [
    	// 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
    	'loop'	=> ['attr' => '', 'expression' => true],
    	'db'	=> ['attr' => '', 'expression' => true],
    ];

    public function tagLoop($tag,$content){
    	#return '';
    	$expression = ltrim(rtrim($tag['expression'], ')'), '(');
        $expression = $this->autoBuildVar($expression);
        $expression = preg_replace ( "/\s(?=\s)/","\\1", $expression );
        $arr = explode(' ',$expression);
        $arr_count = count($arr);
        if($arr_count == 3){
        	$parseStr = '<?php foreach('.$arr[0].' as '.$arr[1].'=>'.$arr[2].'): ?>';
        }else{
        	$parseStr = '<?php foreach('.$arr[0].' as '.$arr[1].'): ?>';
        }
        $parseStr .= $content;
        $parseStr .= '<?php endforeach; ?>';
        #echo $parseStr;exit;
        return $parseStr;
    }

    public function tagDb($tag,$content){
    	static $i = 1;
    	extract($tag);
    	$data_name = '$tags_'.$i;
    	$name = $name?'$'.$name:'$vo';
    	$key = $key?'$'.$key:'$key';
    	$parameter = "table=$table&field=$field&condition=$condition&order=$order&pagesize=$limit";
    	$parseStr = '<?php '.$data_name.'=tag(\''.$parameter.'\');?>';
    	$parseStr .= '<?php foreach('.$data_name.' as '.$key.'=>'.$name.'): ?>';
    	$parseStr .= $content;
        $parseStr .= '<?php endforeach; ?>';
    	return $parseStr;
    }
}
?>