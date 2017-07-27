function upload_thumb(_id){
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
	  content: upload_html
	});
}

function isUpload(i){
	var v = Dd(i).value;
	if(v == '') {confirm('请选择文件'); return false;}
}