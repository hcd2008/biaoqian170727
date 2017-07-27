$(function(){
	$(".member").click(function(e){
		e.preventDefault();
		var link=$(this).attr('href');
		layer.open({
		    type: 2,
		    title: '人员信息',
		    maxmin: true,
		    shadeClose: true, //点击遮罩关闭层
		    area : ['600px' , '420px'],
		    content: link
		});
	})
	$(".chose").click(function(e){
		var url=$(this).attr('href');
	  e.preventDefault();
	  layer.open({
	      type: 2,
	      title: '人员列表',
	      maxmin: true,
	      shadeClose: true, //点击遮罩关闭层
	      area : ['800px' , '520px'],
	      content: url
	      });
	})
	$(".bgpic").click(function(e){
		e.preventDefault();
		var link=$(this).attr('href');
		layer.open({
		    type: 1,
		    title: '评审图片',
		    maxmin: true,
		    shadeClose: true, //点击遮罩关闭层
		    area : ['80%' , '80%'],
		    content: "<div><img src='"+link+"' alt='' /></div>"
		});
	})
	//发送消息提醒
	$(".fstx").click(function(e){
		var url=$(this).attr('href');
	  e.preventDefault();
	  layer.open({
	      type: 2,
	      title: '发送消息提醒',
	      maxmin: true,
	      shadeClose: true, //点击遮罩关闭层
	      area : ['1000px' , '520px'],
	      content: url
	      });
	})
	//清空消息提醒人
	$("#qingkong").click(function(){
		$("#yxid").val("");
		$("#yxren").text("");
	})
	//清空部门委托人
	$("#qingchu").click(function(){
		$("#chosid").val("");
		$("#chosname").text("");
	})
	//选择提醒人
	$(".dept").click(function(){
		var m=$(this).parents(".list").find(".member");
		var j=$(this).find(".jia");
		if(m.is(":visible")){
			m.hide();
			j.text("+");
		}else{
			m.show();
			j.text("-");
		}
	})
	$(".myli").click(function(){
		var uid=$(this).attr('alt');
		var txt=$(this).text();
		var nid='';
		var ntxt='';
		

			var oid=parent.document.getElementById("yxid").value;
			var oren=parent.document.getElementById("yxren").innerHTML;
			if(oid){
				nid=oid+','+uid;
			}else{
				nid=uid;
			}
			if(oren){
				ntxt=oren+','+txt;
			}else{
				ntxt=txt;
			}
			
			parent.document.getElementById("yxid").value=nid;
			parent.document.getElementById("yxren").innerHTML=ntxt;
		
			
		
		var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
		parent.layer.close(index);
	})
	//选择委托人
	$(".mli").click(function(e){
		var userid=$(this).attr('alt');
		var username=$(this).text();
		parent.document.getElementById("chosid").value=userid;
		parent.document.getElementById("chosname").innerHTML=username;
		var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
		parent.layer.close(index);
	})
	//编辑评审信息
	$(".edit_myreport").click(function(e){
		e.preventDefault();
		var link=$(this).attr('href');
		layer.open({
		    type: 2,
		    title: '评审信息编辑',
		    maxmin: true,
		    shadeClose: true, //点击遮罩关闭层
		    area : ['1000px' , '650px'],
		    content: link,
		    
		});
	})
	
})

	
