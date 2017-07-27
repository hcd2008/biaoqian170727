//撤销的array
var cancelList = new Array();
// 撤销的次数
var cancelIndex = 0;
var winSlider;
$(function() {
    initDrag();

    // 加载测试图片
    var image = new Image();
    image.src = "/public/images/backimg/t1.jpg";
    image.onload = function() {
        canvasWidth = image.width;
        canvasHeight = image.height;
        initCanvas(canvasWidth, canvasHeight);
        cxt.drawImage(image, 0, 0, image.width, image.height, 0, 0, image.width, image.height);

        $("#imgSize").val("");
        // test
        /*
         * cxt_bak.beginPath(); cxt_bak.moveTo(0 , canvas_bak.height); cxt_bak.lineTo(canvas_bak.width ,0 ); cxt_bak.stroke();
         */

        // cxt_bak.fillStyle='rgba(255,0,0,0.25)';
        // cxt_bak.fillRect(90,90,300,300);
        // cxt_bak.tips(100,100,100,100,10,'hello','green').stroke();
        // cxt_bak.tipsRect(10,10,100,100).stroke();
        // cxt_bak.roundRect(20,20, 100, 100, 5, true, true);
        saveImageToAry();
    }
     
    // 点击弹出搜索框之外的区域隐藏
    $(document).die().live("click", function(e) {
        //console.log(e.target.id);
        if (e.target.id != 'magnifier_img' && $(e.target).parents("#zoomDiv").length == 0) {
            //$('#zoomDiv').hide();
            $("#zoomDiv").css({"top": "-1000px", "left": "-1000px"});
        }
        
        //console.log($(e.target).parents("#rgbLayerDiv").length);
        //console.log(e.target.id);
        if (e.target.id != 'cmykLayer_img' && $(e.target).parents("#cmykLayerDiv").length == 0) {
            $("#cmykLayerDiv").css({"top": "-1000px", "left": "-1000px"});
        }
    });
    

    $("#magnifier").die().live("click",function(e){
          //console.log("ok");
          if($("#sliderDemo3").html() == ''){
              winSlider = new neverModules.modules.slider(
                      {
                          targetId: "sliderDemo3",
                          sliderCss: "winSlider",
                          barCss: "winBar",
                          min: 10,
                          max: 500,
                          hints: "缩放百分比"
                      });
               winSlider.onstart  = function () {
                   //console.log(winSlider.getValue());
                   //$("#slideValue").html(winSlider.getValue());
                   //magnifier(winSlider.getValue());
                   $("#slideValue").val(winSlider.getValue());
               }
               winSlider.onchange = function () {
                   //console.log(winSlider.getValue());
                   $("#slideValue").val(winSlider.getValue());
                   //magnifier(winSlider.getValue());
               };
               winSlider.onend = function () {
                   //console.log(winSlider.getValue());
                   $("#slideValue").val(winSlider.getValue());
                   magnifier(winSlider.getValue());
               }
               winSlider.create();
               winSlider.setValue(100);
               
               $("#zoomOK").die().live("click", function(){
                   var slideValue = $("#slideValue").val();
                   winSlider.setValue(slideValue);
               });
          }
          
         //$("#zoomDiv").show();
         //$("#zoomDiv").toggle();
         $("#zoomDiv").css({"top":e.clientY + 10, "left":e.clientX + 10});
     });
    
    $("#rgbLayer").die().live("click",function(e){
         $("#rgbLayerDiv").css({"top":e.clientY + 10, "left":e.clientX + 10});
    });
    
    $("input[name=rgb_layer]").die().live("click",function(){
//         //var imgdata2=cxt_bak.getImageData(0,0,canvas.width,canvas.height);
//         //var data2=imgdata2.data;
//　　　　   /*灰度处理：求r，g，b的均值，并赋回给r，g，b*/
//         for(var i=0,n=data.length;i<n;i+=4){
//             var average=(data[i]+data[i+1]+data[i+2])/3;
//             data[i]=data[i];
//             data[i+1]=255-data[i+1];
//             data[i+2]=255-data[i+2];
//         }
//         cxt_bak.putImageData(imgdata,0,0);
//         console.log("ok");
    });
    
    $("#closeDownloadDiv").die().live("click",function(){
        $("#downloadDiv").css({"top":"-100px","left":"-100px"});
        $("#imgLoading").show();
        $("#download").hide();
        $("#download").attr("download", "图片未生成");
        $("#download").attr("href", "");
    });
    
    $("#cmykLayer").die().live("click",function(e){
        $("#cmykLayerDiv").css({"top":e.clientY + 10, "left":e.clientX + 10});
    });
    
    $("input[name=cmyk_layer]").die().live("click",function(){
         var layer = $(this).val();
         $(".cmykLayer").hide();
         if(layer == "CMYK"){
             return;
         }
         //console.log(layer);
         $("#canvas_" + layer).show();
         
         var isModify = $("#isModify").val();
         if(isModify == 1){
             var width = $(canvas).width();
             var height = $(canvas).height();
             var image_rgb = cxt.getImageData(0, 0, width, height);
             var data_rgb = image_rgb.data;
             //console.log(image_rgb);
             
             var newImage_c = cxt_c.getImageData(0, 0, width, height);
             var newImage_m = cxt_c.getImageData(0, 0, width, height);
             var newImage_y = cxt_c.getImageData(0, 0, width, height);
             var newImage_k = cxt_c.getImageData(0, 0, width, height);
             var newdata_c = newImage_c.data;
             var newdata_m = newImage_m.data;
             var newdata_y = newImage_y.data;
             var newdata_k = newImage_k.data;
             for(var i=0,n=newdata_c.length;i<n;i+=4){
                 var rgba_c = rgbLayer(data_rgb[i], data_rgb[i+1], data_rgb[i+2], data_rgb[i+3], "c");
                 newdata_c[i]= rgba_c.r;
                 newdata_c[i+1]= rgba_c.g;
                 newdata_c[i+2]= rgba_c.b;
                 newdata_c[i+3]= rgba_c.a;
                 
                 var rgba_m = rgbLayer(data_rgb[i], data_rgb[i+1], data_rgb[i+2], data_rgb[i+3], "m");
                 newdata_m[i]= rgba_m.r;
                 newdata_m[i+1]= rgba_m.g;
                 newdata_m[i+2]= rgba_m.b;
                 newdata_m[i+3]= rgba_m.a;
                 
                 var rgba_y = rgbLayer(data_rgb[i], data_rgb[i+1], data_rgb[i+2], data_rgb[i+3], "y");
                 newdata_y[i]= rgba_y.r;
                 newdata_y[i+1]= rgba_y.g;
                 newdata_y[i+2]= rgba_y.b;
                 newdata_y[i+3]= rgba_y.a;
                 
                 var rgba_k = rgbLayer(data_rgb[i], data_rgb[i+1], data_rgb[i+2], data_rgb[i+3], "k");
                 newdata_k[i]= rgba_k.r;
                 newdata_k[i+1]= rgba_k.g;
                 newdata_k[i+2]= rgba_k.b;
                 newdata_k[i+3]= rgba_k.a;
             }
             
             //newImage.data = image_rgb.data;
             //console.log(newImage);
             cxt_c.putImageData(newImage_c, 0, 0);
             cxt_m.putImageData(newImage_m, 0, 0);
             cxt_y.putImageData(newImage_y, 0, 0);
             cxt_k.putImageData(newImage_k, 0, 0);
             
             console.log("ok");
             $("#isModify").val(0); //未修改
         }
    });
    
//    $("#imgSize").keyup(function(){
//        var sizeValue = $("#imgSize").val();
//        $("#imgSize").val(parseFloat(sizeValue));
//    });
});

function initLayer(){
    $("input[name=cmyk_layer][value=CMYK]").attr("checked","checked");
    $(".cmykLayer").hide();
}

// 初始化
var initCanvas = function(w, h) {
    canvas = document.getElementById('canvas');
    canvas.width = w;
    canvas.height = h;

    canvasTop = $(canvas).offset().top;
    canvasLeft = $(canvas).offset().left;

    cxt = canvas.getContext('2d');

    canvas_bak = document.getElementById('canvas_bak');
    canvas_bak.width = w;
    canvas_bak.height = h;
    cxt_bak = canvas_bak.getContext('2d');
    
    canvas_c = document.getElementById('canvas_c');
    canvas_m = document.getElementById('canvas_m');
    canvas_y = document.getElementById('canvas_y');
    canvas_k = document.getElementById('canvas_k');
    cxt_c = canvas_c.getContext('2d');
    cxt_m = canvas_m.getContext('2d');
    cxt_y = canvas_y.getContext('2d');
    cxt_k = canvas_k.getContext('2d');
    canvas_c.width = w;
    canvas_c.height = h;
    canvas_m.width = w;
    canvas_m.height = h;
    canvas_y.width = w;
    canvas_y.height = h;
    canvas_k.width = w;
    canvas_k.height = h;
}

//旋转90初始化
var initTurn = function(w, h) {
    canvas_c = document.getElementById('canvas_c');
    canvas_m = document.getElementById('canvas_m');
    canvas_y = document.getElementById('canvas_y');
    canvas_k = document.getElementById('canvas_k');
    cxt_c = canvas_c.getContext('2d');
    cxt_m = canvas_m.getContext('2d');
    cxt_y = canvas_y.getContext('2d');
    cxt_k = canvas_k.getContext('2d');
    canvas_c.width = w;
    canvas_c.height = h;
    canvas_m.width = w;
    canvas_m.height = h;
    canvas_y.width = w;
    canvas_y.height = h;
    canvas_k.width = w;
    canvas_k.height = h;
}

// 下载图片
var downloadImage = function() {
    $("#downloadDiv").css({"top":"80px","left":"40px"});
    
    var file_path = $("#doc").val();
    // alert("d:/userAdmin/uploads/20120515_115146.jpg".match(/[^\/]*$/)[0]);
    // alert("C:\fakepath\left.png".match(/[^\/]*$/)[0]);

    var file_name = new Date().getTime() + '.png'; // getFileName(file_path);
    // console.log(file_name);
    //$("#save").attr("download", file_name);
    //$("#save").attr("href", canvas.toDataURL());
    //$("#save").click();
    
    //window.open("/Mark/downLoadImg?base64="+canvas.toDataURL());
    $.post("/Mark/downLoadImg",{"base64":canvas.toDataURL(),"fileName":file_name},function(data){
        //window.open(data);
        $("#download").attr("download", file_name);
        $("#download").attr("href", data);
        //$("#download").click();
        $("#imgLoading").hide();
        $("#download").show();
    });
}

function getFileName(str) {
    // str.replace('/\','/\\\\');
    var reg = /[^\\\/]*[\\\/]+/g;
    str = str.replace(reg, '');
    return str;
}

// 撤销上一个操作
var cancel = function() {
    cancelIndex++;
    // console.log(canvasWidth);
    // cxt.clearRect(0, 0, canvasWidth, canvasHeight); // 201603
    var image = new Image();
    var index = cancelList.length - 1 - cancelIndex;
    if (index >= 0) {
    	cxt.clearRect(0, 0, canvasWidth, canvasHeight); // 201603
    	
        var url = cancelList[index];
        image.src = url;

        canvasWidth = image.width;
        canvasHeight = image.height;
        initCanvas(canvasWidth, canvasHeight);

        image.onload = function() {
            cxt.drawImage(image, 0, 0, image.width, image.height, 0, 0, canvasWidth, canvasHeight);
        }
        //cancelIndex--;
    }
}

// 重做上一个操作
var next = function() {
    cancelIndex--;
    cxt.clearRect(0, 0, canvasWidth, canvasHeight);
    var image = new Image();
    var index = cancelList.length - 1 - cancelIndex;
    var url = cancelList[index];
    image.src = url;

    canvasWidth = image.width;
    canvasHeight = image.height;
    initCanvas(canvasWidth, canvasHeight);

    image.onload = function() {
        cxt.drawImage(image, 0, 0, image.width, image.height, 0, 0, canvasWidth, canvasHeight);
    }
}

// 保存历史 用于撤销
var saveImageToAry = function() {
    cancelIndex = 0;
    var dataUrl = canvas.toDataURL();
    cancelList.push(dataUrl);
}

// 处理文件拖入事件，防止浏览器默认事件带来的重定向
function handleDragOver(evt) {
    evt.stopPropagation();
    evt.preventDefault();
}

// 判断是否图片
function isImage(type) {
    switch (type) {
        case 'image/jpeg':
        case 'image/png':
        case 'image/gif':
        case 'image/bmp':
        case 'image/jpg':
            return true;
        default:
            return false;
    }
}

// 处理拖放文件列表
function handleFileSelect(evt) {
    evt.stopPropagation();
    evt.preventDefault();

    var files = evt.dataTransfer.files;

    for (var i = 0, f; f = files[i]; i++) {
        var t = f.type ? f.type : 'n/a';
        reader = new FileReader();
        isImg = isImage(t);

        // 处理得到的图片
        if (isImg) {
            reader.onload = (function(theFile) {
                return function(e) {
                    var image = new Image();
                    image.src = e.target.result;
                    image.onload = function() {
                        canvasWidth = image.width;
                        canvasHeight = image.height;
                        initCanvas(canvasWidth, canvasHeight);
                        cxt.drawImage(image, 0, 0, image.width, image.height, 0, 0, image.width, image.height);
                        saveImageToAry();
                        
                        $("#isModify").val(1);
                        initLayer();
                        
                        $("#imgSize").val("");
                    }

                };
            })(f)
            reader.readAsDataURL(f);
        }
    }
}

// 初始化拖入效果
var initDrag = function() {
    var dragDiv = document.getElementById("canvas");
    dragDiv.addEventListener('dragover', handleDragOver, false);
    dragDiv.addEventListener('drop', handleFileSelect, false);
}

function setImagePreview() {
    var docObj = document.getElementById("doc");

    var src = '';
    if (docObj.files && docObj.files[0]) {
        // 火狐下，直接设img属性
        // 火狐7以上版本不能用上面的getAsDataURL()方式获取，需要一下方式
        src = window.URL.createObjectURL(docObj.files[0]);
    } else {
        // IE下，使用滤镜
        docObj.select();
        var imgSrc = document.selection.createRange().text;

        // 图片异常的捕捉，防止用户修改后缀来伪造图片
        try {
            src = imgSrc;
        } catch (e) {
            alert("您上传的图片格式不正确，请重新选择!");
            return false;
        }
        document.selection.empty();
    }

    var image = new Image();
    // console.log(src);
    image.src = src;
    image.onload = function() {
        canvasWidth = image.width;
        canvasHeight = image.height;
        initCanvas(canvasWidth, canvasHeight);
        cxt.drawImage(image, 0, 0, image.width, image.height, 0, 0, image.width, image.height);
        // saveImageToAry();
        
        $("#isModify").val(1);
        initLayer();
        
        $("#imgSize").val("");
    }

    return true;
}

function openImage() {
    $("#doc").trigger("click");
}

window.onscroll = function(){ 
    var addHeight = document.documentElement.scrollTop || document.body.scrollTop;  
    //var top_div = document.getElementById( "top_div" ); 
    //console.log(t);
    
    // 水平线
    $("div.zxxRefLine_h").each(function(){
        var old = $(this).attr("old");
        var newTop = parseFloat(old) - parseFloat(addHeight);
        $(this).css("top", newTop);
    });
    
    var addWidth = document.documentElement.scrollLeft || document.body.scrollLeft;  
    // 垂直
    $("div.zxxRefLine_v").each(function(){
        var old = $(this).attr("old");
        var newTop = parseFloat(old) - parseFloat(addWidth);
        $(this).css("left", newTop);
    });
    
    // 水平标尺
    var newRulerLeft = 0 - parseFloat(addWidth);
    $("#zxxScaleRulerH").css("left", newRulerLeft);
    
    // 垂直标尺
    var newRulerTop = 0 - parseFloat(addHeight);
    $("#zxxScaleRulerV").css("top", newRulerTop);
} 

// 禁止滚动条 
// left: 37, up: 38, right: 39, down: 40,
// spacebar: 32, pageup: 33, pagedown: 34, end: 35, home: 36
var keys = [37, 38, 39, 40];

function preventDefault(e) {
  e = e || window.event;
  if (e.preventDefault)
      e.preventDefault();
  e.returnValue = false;  
}

function keydown(e) {
    for (var i = keys.length; i--;) {
        if (e.keyCode === keys[i]) {
            preventDefault(e);
            return;
        }
    }
}

function wheel(e) {
  preventDefault(e);
}

function disable_scroll() {
  if (window.addEventListener) {
      window.addEventListener('DOMMouseScroll', wheel, false);
  }
  window.onmousewheel = document.onmousewheel = wheel;
  document.onkeydown = keydown;
}

function enable_scroll() {
    if (window.removeEventListener) {
        window.removeEventListener('DOMMouseScroll', wheel, false);
    }
    window.onmousewheel = document.onmousewheel = document.onkeydown = null;  
}
