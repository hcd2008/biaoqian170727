function Dd(i){return document.getElementById(i);}
function Ds(i){Dd(i).style.display='';}
function Dh(i){Dd(i).style.display='none';}
function Dsh(i){Dd(i).style.display=Dd(i).style.display=='none'?'':'none';}
function Df(i){Dd(i).focus();}
function upload_thumb(_id){
	var css = '<style type="text/css">.sys_upload{padding: 10px;}.sys_upload ul li{padding: 10px;}.sys_upload ul li .sys_btn{padding: 2px;width: 70px;}</style>';
	var _id = _id;
	var upload_html = '<iframe name="UploadThumb" style="display:none;" src=""></iframe>';
    upload_html += '<form method="post" target="UploadThumb" enctype="multipart/form-data" action="/index.php?s=member/upload/iframe" onsubmit="return isUpload(\'upfile\')">';
    upload_html += '<div class="sys_upload">';
    upload_html += '<ul>';
    upload_html += '';
    upload_html += '<li><input type="file" name="file" id="upfile" onchange="this.form.submit();Dd(\'sys_upload_sbtn\').disabled=true;Dd(\'sys_upload_sbtn\').value=\'上传中...\'"></li>';
    upload_html += '<li><input type="submit" class="sys_btn" id="sys_upload_sbtn" value="上 传" />&nbsp;&nbsp;&nbsp;&nbsp;<input class="sys_btn" type="button" value="取 消" onclick="layer.closeAll();" /></li>';
    upload_html += '</ul>';
    upload_html += '</div>';
    upload_html += '</form>';
	layer.open({
	  'title': '上传',
	  type: 1,
	  skin: 'layui-layer-demo', //样式类名
	  closeBtn: 1, //不显示关闭按钮
	  shift: 2,
	  shadeClose: false, //开启遮罩关闭
	  content: css+upload_html
	});
}

function isUpload(i){
	var v = Dd(i).value;
	if(v == '') {confirm('请选择文件'); return false;}
}