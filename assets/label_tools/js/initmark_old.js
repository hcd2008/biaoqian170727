//撤销的array
var cancelList = new Array();
// 撤销的次数
var cancelIndex = 0;

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
    $.post("/file/report",{"base64":canvas.toDataURL(),"fileName":file_name},function(data){
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
    cxt.clearRect(0, 0, canvasWidth, canvasHeight);
    var image = new Image();
    var index = cancelList.length - 1 - cancelIndex;
    if (index >= 0) {
        var url = cancelList[index];
        image.src = url;

        canvasWidth = image.width;
        canvasHeight = image.height;
        initCanvas(canvasWidth, canvasHeight);

        image.onload = function() {
            cxt.drawImage(image, 0, 0, image.width, image.height, 0, 0, canvasWidth, canvasHeight);
        }
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
    }

    return true;
}

function openImage() {
    $("#doc").trigger("click");
}