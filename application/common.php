<?php
error_reporting(7);
function tag($parameter, $expires = 0) {
	$parameter = str_replace(array('&amp;', '%'), array('', '##'), $parameter);
	parse_str($parameter, $par);
	if(!is_array($par)) return '';
	extract($par, EXTR_SKIP);
	isset($fields) or $fields = '*';
	isset($catid) or $catid = 0;
	isset($recmd) or $recmd = '';
	isset($condition) or $condition = '1';
	isset($group) or $group = '';
	isset($page) or $page = 1;
	isset($offset) or $offset = 0;
	isset($pagesize) or $pagesize = 10;
	if(!$pagesize) $pagesize = 10;
	isset($order) or $order = '';
	isset($length) or $length = 0;
	isset($debug) or $debug = 0;
	$offset or $offset = ($page-1)*$pagesize;
	$num = 0;
	$order = $order ? ''.$order : '';
	$table = $table?$table:get_table($moduleid);
	if(!$table) return '';
	$tags = array();
	$tags = db($table)->where($condition)->order($order)->limit($pagesize)->select();
	if($debug==1){
		echo "<font color='green'>".$parameter."</font>";
		echo "<br>";
		echo "<font color='red'>".db()->getLastSql()."</font>";
		echo "<br>";
	}
	if($tags){
		foreach ($tags as $k => $v) {
			if(isset($v['itemid'])){
				$tags[$k]['linkurl'] = Url(tabele2module($table).'/show',['itemid'=>$v['itemid']]);
			}
			
		}
	}
	return $tags;
}

function role_to_array($i=0){
	$array = [];
	switch ($i) {

		case '7':
			$array =  [4,2,1];
			break;
		case '6':
			$array =  [4,2];
			break;
		case '5':
			$array =  [4,1];
			break;
		case '4':
			$array =  [4];
			break;
		case '3':
			$array =  [2,1];
			break;
		case '2':
			$array =  [2];
			break;
		case '1':
			$array =  [1];
			break;
		default:
			
			break;
	}
	return $array;

}

function in_string($i,$mixed){
	if(!is_array($mixed)){
		$mixed = explode(',', $mixed);
	}
	return in_array($i,$mixed);
}


/**
 * 规范数据返回函数
 * @param unknown $state
 * @param unknown $msg
 * @param unknown $data
 * @return multitype:unknown
 */
function callback($state = true, $msg = '', $data = array()) {
    return array('state' => $state, 'msg' => $msg, 'data' => $data);
}

function get_dept($i){
	$DEPT = cache('dept');
	return isset($DEPT[$i]['dept_name'])?$DEPT[$i]['dept_name']:'';
}

function get_product_name($pid){
	return db('product')->where('pid',$pid)->value('proname');
}

function get_project_data($id){
	return db('projectData')->where('id',$id)->value('content');
}

function label_type($i){
	$label_type = \think\View::instance()->label_type;
	return isset($label_type[$i])?$label_type[$i]:'unknown';
}

function userinfo_url($userid){
	return url('member/ext/userinfo',['userid'=>$userid]);
}

function f_red($str){
	return '<span style="color:red">'.$str.'</span>';
}

function f_green($str){
	return '<span style="color:green">'.$str.'</span>';
}

function pm_state($i){
	$array[0] = '';
	$array[1] = '未读';
	$array[3] = '已读';
	return $array[$i]?$array[$i]:'';
}

function project_result_state($i){
	$array[0] = '';
	$array[10] = '未通过';
	$array[20] = '通过审核';
	return $array[$i]?$array[$i]:'';
}

function dept_task_result_state($i){
	$array[0] = '';
	$array[10] = '未通过';
	$array[20] = '通过审核';
	return $array[$i]?$array[$i]:'';
}



function project_task_state($i){
	$array[1] = '尚未分配任务';
	$array[2] = '任务已经下发';
	$array[3] = '评审中';
	$array[4] = '评审完毕';
	$array[5] = '评审完毕';
	$array[6] = '评审完毕';
	$array[7] = '评审完毕';
	return $array[$i]?$array[$i]:'';
}

function review_state($i){
	$array[1] = '未评审';
	$array[2] = '已经评审';
	$array[3] = '已经评审';
	return $array[$i]?$array[$i]:'';
}

function review_result_state($i){
	$array[0]  = '';
	$array[10] = '未通过';
	$array[20] = '通过审核';
	return $array[$i]?$array[$i]:'';
}

function task_state($i){
	$array[1] = '等待分配';
	$array[2] = '评审中';
	$array[3] = '评审完毕';
	$array[4] = '完成部门汇总';
	$array[5] = '完成';
	$array[6] = '等待领导审批';
	$array[7] = '领导审批通过';
	$array[8] = '领导审批未通过';
	return $array[$i]?$array[$i]:'';
}

function project_state($i){
	$array[1] = '进行中';
	$array[2] = '已终止';
	$array[3] = '已完成';
	return $array[$i]?$array[$i]:'';
}

function check_result_state($i){
	$array[0]  = '未处理';
	$array[10] = '拒绝';
	$array[20] = '通过';
	return $array[$i]?$array[$i]:'';
}

function task_result_state_icon($i){
	$html = '<img class="myimg" src="__ASSETS__/image/report/result_'.$i.'.jpg" border="0">';
	return $html;
}

function project_state_icon($i){
	$html = '<img class="myimg" src="__ASSETS__/image/project/'.$i.'.jpg" border="0">';
	return $html;
}

function work_state($i){
	$array[0]  = '未知';
	$array[1]  = '休假';
	$array[3]  = '正常';
	return $array[$i]?$array[$i]:'';
}

function get_archive2select($title='',$name='',$selected=0,$ext=''){
	$items = think\Db::name('archive')->select();
	$lists = [];
	foreach ($items as $k => $v) {
		$lists[$v['arcid']] = $v['arc_title'];
	}
	return array2select($lists,$title,$name,$selected,$ext);
}

function get_product2select($title='',$name='',$selected=0,$ext=''){
	$items = think\Db::name('product')->select();
	$lists = [];
	foreach ($items as $k => $v) {
		$lists[$v['pid']] = $v['proname'];
	}
	return array2select($lists,$title,$name,$selected,$ext);
}

function array2select($lists,$title,$name,$selected,$ext){
	$html = '<select name="'.$name.'" '.$ext.'>';
	if($title){
		$html .= '<option value="">'.$title.'</option>';
	}
	foreach ($lists as $k => $v) {
		$html .= '<option value="'.$k.'">'.$v.'</option>';
	}
	$html .= '</select>';
	return $html;
	
}	

function dpassword($password) {
	return is_md5($password) ? $password : md5($password);
}

function is_md5($password) {
	return preg_match("/^[a-f0-9]{32}$/", $password);
}

function encrypt($txt, $key = '', $expiry = 0) {
	strlen($key) > 5 or $key = SYS_KEY;
	$str = $txt.substr($key, 0, 3);
	return str_replace(array('+', '/', '0x', '0X'), array('-P-', '-S-', '-Z-', '-X-'), mycrypt($str, $key, 'ENCODE', $expiry));
}

function decrypt($txt, $key = '') {
	strlen($key) > 5 or $key = SYS_KEY;
	$str = mycrypt(str_replace(array('-P-', '-S-', '-Z-', '-X-'), array('+', '/', '0x', '0X'), $txt), $key, 'DECODE');
	return substr($str, -3) == substr($key, 0, 3) ? substr($str, 0, -3) : '';
}

function mycrypt($string, $key, $operation = 'DECODE', $expiry = 0) {
	$ckey_length = 4;
	$key = md5($key);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);
	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + TIMES : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);
	$result = '';
	$box = range(0, 255);
	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}
	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - TIMES > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}

function timetodate($time = 0, $type = 5) {
	if(!$time) return '';
	$types = array('Y-m-d', 'Y', 'm-d', 'Y-m-d', 'm-d H:i', 'Y-m-d H:i', 'Y-m-d H:i:s');
	if(isset($types[$type])) $type = $types[$type];
	$date = '';
	return $date ? $date : date($type, $time);
}

function ubb2html($Text) {
  $Text=trim($Text);
  $Text=preg_replace("/\\t/is","  ",$Text);
  $Text=preg_replace("/\[h1\](.+?)\[\/h1\]/is","<h1>\\1</h1>",$Text);
  $Text=preg_replace("/\[h2\](.+?)\[\/h2\]/is","<h2>\\1</h2>",$Text);
  $Text=preg_replace("/\[h3\](.+?)\[\/h3\]/is","<h3>\\1</h3>",$Text);
  $Text=preg_replace("/\[h4\](.+?)\[\/h4\]/is","<h4>\\1</h4>",$Text);
  $Text=preg_replace("/\[h5\](.+?)\[\/h5\]/is","<h5>\\1</h5>",$Text);
  $Text=preg_replace("/\[h6\](.+?)\[\/h6\]/is","<h6>\\1</h6>",$Text);
  $Text=preg_replace("/\[separator\]/is","",$Text);
  $Text=preg_replace("/\[center\](.+?)\[\/center\]/is","<center>\\1</center>",$Text);
  $Text=preg_replace("/\[url=http:\/\/([^\[]*)\](.+?)\[\/url\]/is","<a href=\"http://\\1\" >\\2</a>",$Text);
  $Text=preg_replace("/\[url=([^\[]*)\](.+?)\[\/url\]/is","<a href=\"http://\\1\" >\\2</a>",$Text);
  $Text=preg_replace("/\[url\]http:\/\/([^\[]*)\[\/url\]/is","<a href=\"http://\\1\" >\\1</a>",$Text);
  $Text=preg_replace("/\[url\]([^\[]*)\[\/url\]/is","<a href=\"\\1\" >\\1</a>",$Text);
  $Text=preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=\\1>",$Text);
  $Text=preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is","<font color=\\1>\\2</font>",$Text);
  $Text=preg_replace("/\[size=(.+?)\](.+?)\[\/size\]/is","<font size=\\1>\\2</font>",$Text);
  $Text=preg_replace("/\[sup\](.+?)\[\/sup\]/is","<sup>\\1</sup>",$Text);
  $Text=preg_replace("/\[sub\](.+?)\[\/sub\]/is","<sub>\\1</sub>",$Text);
  $Text=preg_replace("/\[pre\](.+?)\[\/pre\]/is","<pre>\\1</pre>",$Text);
  $Text=preg_replace("/\[email\](.+?)\[\/email\]/is","<a href='mailto:\\1'>\\1</a>",$Text);
  $Text=preg_replace("/\[i\](.+?)\[\/i\]/is","<i>\\1</i>",$Text);
  $Text=preg_replace("/\[u\](.+?)\[\/u\]/is","<u>\\1</u>",$Text);
  $Text=preg_replace("/\[b\](.+?)\[\/b\]/is","<b>\\1</b>",$Text);
  $Text=preg_replace("/\\n/is","<br/>",$Text);
  return $Text;
}