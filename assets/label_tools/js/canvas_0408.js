var num = 0;
var canvas = document.getElementById('canvas');
var cxt = canvas.getContext('2d');

// 初始化设置
cxt.lineWidth = 1;
cxt.strokeStyle = "#000";

var canvas_bak = document.getElementById('canvas_bak');
var cxt_bak = canvas_bak.getContext('2d');
// var canvasLeft = $(canvas).offset().left;
// var canvasTop = $(canvas).offset().top;

// 初始化设置
cxt_bak.lineWidth = 1;
cxt_bak.strokeStyle = "#000";

var canvas_enlarge = document.getElementById('canvas_enlarge');
var cxt_enlarge = canvas_enlarge.getContext('2d');

var canvas_c = document.getElementById('canvas_c');
var canvas_m = document.getElementById('canvas_m');
var canvas_y = document.getElementById('canvas_y');
var canvas_k = document.getElementById('canvas_k');
var cxt_c = canvas_c.getContext('2d');
var cxt_m = canvas_m.getContext('2d');
var cxt_y = canvas_y.getContext('2d');
var cxt_k = canvas_k.getContext('2d');

var flag = 0;
var startX;
var endX;
var startY;
var endY;

var canvasWidth = $(canvas).width();
var canvasHeight = $(canvas).height();

// 默认画笔工具

// 设置按钮组
var Save = document.getElementById('save');
var Clear = document.getElementById('clear');
var Brush = document.getElementById('brush');
var Eraser = document.getElementById('eraser');
var Paint = document.getElementById('paint');
var Straw = document.getElementById('straw');
var Text = document.getElementById('text');
var Magnifier = document.getElementById('magnifier');
var Line = document.getElementById('line');
var Arc = document.getElementById('arc');
var Rect = document.getElementById('rect');
var Poly = document.getElementById('poly');
var ArcFill = document.getElementById('arcFill');
var RectFill = document.getElementById('rectFill');
var Rule = document.getElementById('rule');
var actions = [Save, Clear, Brush, Eraser, Paint, Straw, Text, Magnifier, Line, Arc, Rect, Poly, ArcFill, RectFill, Rule]
// 设置属性组
var Width_1 = document.getElementById('width_1');
var Width_3 = document.getElementById('width_3');
var Width_5 = document.getElementById('width_5');
var Width_8 = document.getElementById('width_8');
var lineWidths = [Width_1, Width_3, Width_5, Width_8];
// 设置颜色组
var red = document.getElementById('red');
var green = document.getElementById('green');
var blue = document.getElementById('blue');
var yellow = document.getElementById('yellow');
var white = document.getElementById('white');
var black = document.getElementById('black');
var pink = document.getElementById('pink');
var purple = document.getElementById('purple');
var cyan = document.getElementById('cyan');
var orange = document.getElementById('orange');
var colors = [red, green, blue, yellow, white, black, pink, purple, cyan, orange];
brush(2);
// 按钮状态改变方法
function selectStatus(array, num, style) {
    for (var i = 4; i < array.length; i++) {
        if (i == num) {
            if (style == 1) {
                array[i].style.background = 'yellow';
            } else {
                array[i].style.border = '1px solid #fff';
            }

        } else {
            if (style == 1) {
                array[i].style.background = '#ccc';
            } else {
                array[i].style.border = '1px solid #000';
            }

        }
    }
}

// 保存图片
/*
 * function saveas(num){ //alert(111111111111); var data=canvas.toDataURL(); var b64 = data.substring( 22 ); var
 * imgdata=document.getElementById('imgdata'); imgdata.value=b64; //alert(imgdata.value); //alert(b64); var
 * form=document.getElementById('form'); form.submit(); }
 */

// 清除画布
function clearAll(num) {
    // selectStatus(actions,num,1);
    cxt.clearRect(0, 0, canvasWidth, canvasHeight);
    // canvas.onmousedown=null;
    // canvas.onmousemove=null;
    // canvas.onmouseup=null;
    // canvas.onmouseout=null;

    // brush(2);
}

// 画笔工具
function brush(num) {
    selectStatus(actions, num, 1);
    canvas.onmousedown = function(evt) {
        evt = evt ? evt : window.event;
        startX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        startY = (evt.pageY - this.offsetLeft-139) * scalePencent;
        flag = 1;
        // alert(mouseX+'——————————————'+mouseY);
        cxt.closePath();
        cxt.beginPath();
        cxt.moveTo(startX, startY); // 起始位置
    }

    canvas.onmousemove = function(evt) {
        evt = evt ? evt : window.event;
        endX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        endY = (evt.pageY - this.offsetLeft-139) * scalePencent;
        // console.log(endX);

        if (flag) {
            cxt.lineTo(endX, endY);
            cxt.stroke();
        }
    }

    canvas.onmouseup = function(evt) {
        flag = 0;
        saveImageToAry();
    }
    canvas.onmouseout = function(evt) {
        flag = 0;
    }
}
// 橡皮工具
function eraser(num) {
    selectStatus(actions, num, 1);
    canvas.onmousedown = function(evt) {
        evt = evt ? evt : window.event;
        startX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        startY = (evt.pageY - this.offsetLeft-139) * scalePencent;
        flag = 1;
        cxt.clearRect(startX - cxt.lineWidth, startY - cxt.lineWidth, cxt.lineWidth, cxt.lineWidth);

    }

    canvas.onmousemove = function(evt) {
        evt = evt ? evt : window.event;
        endX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        endY = (evt.pageY - this.offsetLeft-139) * scalePencent;

        if (flag) {
            cxt.clearRect(endX - cxt.lineWidth, endY - cxt.lineWidth, cxt.lineWidth, cxt.lineWidth);
        }
    }

    canvas.onmouseup = function(evt) {
        flag = 0;
    }
    canvas.onmouseout = function(evt) {
        flag = 0;
    }
}
// 油漆桶工具
function paint(num) {
    selectStatus(actions, num, 1);
    canvas.onmousedown = function() {
        cxt.fillRect(0, 0, 800, 400);
        saveImageToAry();
    }

}
// 吸管工具
function straw(num) {
    selectStatus(actions, num, 1);
    canvas.onmousemove = null;
    canvas.onmouseup = null;
    canvas.onmouseout = null;
    canvas.onmousedown = function(evt) {
        evt = evt ? evt : window.event;
        X = (evt.pageX - this.offsetLeft-192) * scalePencent;
        Y = (evt.pageY - this.offsetLeft-139) * scalePencent;
        var imageData = cxt.getImageData(X, Y, 1, 1);
        // alert(imageData.data);
        var pxData = imageData.data;
        // alert(pxData);
        var color = 'rgba(' + pxData[0] + ',' + pxData[1] + ',' + pxData[2] + ',' + pxData[3] + ')';
        // alert(color);
        cxt.strokeStyle = color;
        cxt.fillStyle = color;
    }
}

// 画线工具
function line(num) {
    selectStatus(actions, num, 1);
    canvas.onmousedown = function(evt) {
        evt = evt ? evt : window.event;
        startX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        startY = (evt.pageY - this.offsetLeft-139) * scalePencent;
        cxt.closePath();
        cxt.beginPath();
        cxt.moveTo(startX, startY); // 起始位置
    }
    canvas.onmouseup = function(evt) {
        evt = evt ? evt : window.event;
        endX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        endY = (evt.pageY - this.offsetLeft-139) * scalePencent;
        cxt.lineTo(endX, endY);
        cxt.stroke();
        saveImageToAry();
    }
    canvas.onmousemove = null;
    canvas.onmouseout = null;
}
// 画圆圈工具
function arc(num) {
    selectStatus(actions, num, 1);
    canvas.onmousedown = function(evt) {
        evt = evt ? evt : window.event;
        startX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        startY = (evt.pageY - this.offsetLeft-139) * scalePencent;
        cxt.closePath();
        cxt.beginPath();
    }
    canvas.onmouseup = function(evt) {
        evt = evt ? evt : window.event;
        endX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        endY = (evt.pageY - this.offsetLeft-139) * scalePencent;
        cxt.arc(startX, startY, Math.sqrt(Math.pow(endX - startX, 2) + Math.pow(endY - startY, 2)), 0, 360, false);
        cxt.stroke();
        saveImageToAry();
    }
    canvas.onmousemove = null;
    canvas.onmouseout = null;
}

// 画方框
function rect(num) {
    selectStatus(actions, num, 1);
    canvas.onmousedown = function(evt) {
        evt = evt ? evt : window.event;
        startX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        startY = (evt.pageY - this.offsetLeft-139) * scalePencent;
    }
    canvas.onmouseup = function(evt) {
        evt = evt ? evt : window.event;
        endX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        endY = (evt.pageY - this.offsetLeft-139) * scalePencent;
        cxt.strokeRect(startX, startY, endX - startX, endY - startY);
        saveImageToAry();
    }
    canvas.onmousemove = null;
    canvas.onmouseout = null;
}
// 画三角形
function poly(num) {
    selectStatus(actions, num, 1);
    canvas.onmousedown = function(evt) {
        evt = evt ? evt : window.event;
        startX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        startY = (evt.pageY - this.offsetLeft-139) * scalePencent;

        cxt.beginPath();
    }
    canvas.onmouseup = function(evt) {
        evt = evt ? evt : window.event;
        endX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        endY = (evt.pageY - this.offsetLeft-139) * scalePencent;
        cxt.moveTo(endX, endY);
        cxt.lineTo(startX - (endX - startX), endY);
        cxt.lineTo(startX, startY - Math.sqrt(Math.sqrt(endX - startX, 2) + Math.sqrt(endY - startY, 2)));
        cxt.closePath();
        cxt.stroke();
        saveImageToAry();
    }
    canvas.onmousemove = null;
    canvas.onmouseout = null;
}
// 画实心圆形
function arcFill(num) {
    selectStatus(actions, num, 1);
    canvas.onmousedown = function(evt) {
        evt = evt ? evt : window.event;
        startX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        startY = (evt.pageY - this.offsetLeft-139) * scalePencent;
        cxt.closePath();
        cxt.beginPath();
    }
    canvas.onmouseup = function(evt) {
        evt = evt ? evt : window.event;
        endX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        endY = (evt.pageY - this.offsetLeft-139) * scalePencent;
        cxt.arc(startX, startY, Math.sqrt(Math.pow((endX - startX), 2) + Math.pow((endY - startY), 2)), 0, 360, false);
        cxt.fill();
        saveImageToAry();
    }
    canvas.onmousemove = null;
    canvas.onmouseout = null;
}
// 画实心矩形
function rectFill(num) {
    selectStatus(actions, num, 1);
    canvas.onmousedown = function(evt) {
        evt = evt ? evt : window.event;
        startX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        startY = (evt.pageY - this.offsetLeft-139) * scalePencent;
    }
    canvas.onmouseup = function(evt) {
        evt = evt ? evt : window.event;
        endX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        endY = (evt.pageY - this.offsetLeft-139) * scalePencent;
        cxt.fillRect(startX, startY, endX - startX, endY - startY);
        saveImageToAry();
    }
    canvas.onmousemove = null;
    canvas.onmouseout = null;
}
// 测量
function rule(num) {
    selectStatus(actions, num, 1);
    canvas.onmousedown = function(evt) {
        evt = evt ? evt : window.event;
        startX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        startY = (evt.pageY - this.offsetLeft-139) * scalePencent;
        cxt.closePath();
        cxt.beginPath();
        cxt.moveTo(startX, startY); // 起始位置
    }
    canvas.onmouseup = function(evt) {
        evt = evt ? evt : window.event;
        endX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        endY = (evt.pageY - this.offsetLeft-139) * scalePencent;
        cxt.lineTo(endX, endY);
        cxt.stroke();

        var distance = Math.sqrt((endX - startX) * (endX - startX) + (endY - startY) * (endY - startY));
        if (distance > 0) {
            cxt.fillText(distance, (endX + startX) / 2 + 10, (endY + startY) / 2 + 10);

            cxt.beginPath();

            // 起点
            var x1 = startX - 5 * (endY - startY) / distance
            var x2 = startX + 5 * (endY - startY) / distance
            var y1 = startY + 5 * (endX - startX) / distance
            var y2 = startY - 5 * (endX - startX) / distance
            // saveImageToAry();

            cxt.moveTo(x1, y1); // 起始位置
            cxt.lineTo(x2, y2);
            // cxt.stroke();

            // 终点
            var x_e1 = x1 + (endX - startX);
            var x_e2 = x2 + (endX - startX);
            var y_e1 = y1 + (endY - startY);
            var y_e2 = y2 + (endY - startY);
            cxt.moveTo(x_e1, y_e1); // 起始位置
            cxt.lineTo(x_e2, y_e2);
            cxt.stroke();
        }

        saveImageToAry();
    }
    canvas.onmousemove = null;
    canvas.onmouseout = null;
}
// 文本工具
function text(style, obj) {
    // selectStatus(actions,num,1);
    chooseImg(style, obj);

    canvas.onmousemove = null;
    canvas.onmouseup = null;
    canvas.onmouseout = null;
    canvas.onmousedown = function(evt) {
        debugger
        evt = evt ? evt : window.event;
        startX = (evt.pageX - this.offsetLeft-192) * scalePencent;
        startY = (evt.pageY - this.offsetLeft-139) * scalePencent;
        var content = window.prompt('请输入文字', '');
        if (content) {
            cxt.fillText(content, startX, startY);
            saveImageToAry();
        }
    }
}
// 放大镜工具
var scalePencent = 1;
function magnifierOld(num) {
    // selectStatus(actions,num,1);
    var scale = window.prompt('请输入缩放的倍数百分比', '');
    if (scale != null && scale != '') {
        scalePencent = 100 / scale;
        var scaleX = canvasWidth * scale / 100;
        var scaleY = canvasHeight * scale / 100;
        canvas.style.width = parseInt(scaleX) + 'px';
        canvas.style.height = parseInt(scaleY) + 'px';
        canvas_bak.style.width = parseInt(scaleX) + 'px';
        canvas_bak.style.height = parseInt(scaleY) + 'px';
    }
}
function magnifier(scale) {
    //debugger
    if (scale == null || scale == '') {
        scale = 100;
    }
    scalePencent = 100 / scale;
    var scaleX = canvasWidth * scale / 100;
    var scaleY = canvasHeight * scale / 100;
    canvas.style.width = parseInt(scaleX) + 'px';
    canvas.style.height = parseInt(scaleY) + 'px';
    canvas_bak.style.width = parseInt(scaleX) + 'px';
    canvas_bak.style.height = parseInt(scaleY) + 'px';
}


// 设置线宽
function setWidth(style, obj, num) {
    // selectStatus(lineWidths,num,1);
    chooseImg(style, obj);
    switch (num) {
        case 0 :
            size = 1;
            cxt_bak.lineWidth = 1;
            break;
        case 1 :
            size = 3;
            cxt_bak.lineWidth = 3;
            break;
        case 2 :
            size = 5;
            cxt_bak.lineWidth = 5;
            break;
        case 3 :
            size = 8;
            cxt_bak.lineWidth = 8;
            break;
        default :
            alert('error');
    }
    sizeTemp = size * 1;
    // alert(cxt.lineWidth);
}
// 设置颜色
var cxt_bak_fillStyle = "#000";
var cxt_bak_strokeStyle = "#000";
function setColor(style, obj, num) {
    // selectStatus(colors,num);
    chooseImg(style, obj, 1);
    cxt_bak.strokeStyle = obj.id;
    cxt_bak.fillStyle = obj.id;
    
    cxt_bak_fillStyle = obj.id;
    cxt_bak_strokeStyle = obj.id;
}

var tips_bgcolor = "rgba(255,0,0,0.25)";
function setTipBgColor(style, obj) {
    chooseImg(style, obj, 1);
    var bgcolor = $(obj).css("background-color");
    tips_bgcolor = bgcolor.replace("rgb","rgba").replace(")",",0.25)");
    //console.log(bgcolor.replace("rgb","rgba").replace(")",",0.25)"));
}

// //////////////function 2///////////////////
var canvasTop;
var canvasLeft;

// 画笔大小
var size = 1;
var sizeTemp = size * 1;
var color = '#000000';

// 选择功能按钮 修改样式
function chooseImg(style, obj, flag) {
    if (flag == 1) {
        $("." + style).css({
            'border': '1px solid #000'
        });
        $(obj).css({
            'border': '1px solid #fff'
        });
    } else {
        $("." + style).css({
            'background': '#ccc'
        });
        $(obj).css({
            'background': 'yellow'
        });
    }
}

// 画图形
//var turn_count = 0;
var draw_graph = function(graphType, style, obj) {
    //debugger
    var imgSize = $("#imgSize").val();
//    if(graphType=='rule' || graphType=='rect_rule'){
//        if(imgSize == '' || isNaN(imgSize)){
//            alert("请输入图像实际宽度,单位毫米");
//            $("#imgSize").addClass("red_border");
//            return false;
//        } else {
//            $("#imgSize").removeClass("red_border");
//        }
//    }
    
    // 初始化
    chooseImg(style, obj);
    
    $(canvas_bak).unbind();
    $("#canvas_bak").die("mousedown");
    $("#partEnlargeDiv").hide(); // 局部放大
    $("#zoomDiv").css({"top": "-1000px", "left": "-1000px"}); //$("#zoomDiv").hide();
    $("#cmykLayerDiv").css({"top": "-1000px", "left": "-1000px"});

    // 把蒙版放于画板上面
    // $("#canvas_bak").css("z-index",1);

    // 先画在蒙版上 再复制到画布上
    var graphTypes=",horizontal,vertical,same,drag,turn_right,turn_left,"
    if(graphTypes.indexOf(graphType)>-1){
        var img = new Image();
        img.src = canvas.toDataURL();
        if(graphType == 'horizontal'){
            // 水平翻转
            cxt.translate(canvas.width, 0);
            cxt.scale(-1, 1);
            
            // 画图
            cxt.drawImage(img, 0, 0);
            
            // 翻转回来
            cxt.translate(canvas.width, 0);
            cxt.scale(-1, 1);
        } else if(graphType == 'vertical'){
            // 垂直翻转
            cxt.translate(0, canvas.height);
            cxt.scale(1, -1);
            
            // 画图
            cxt.drawImage(img, 0, 0);
            
            // 翻转回来
            cxt.translate(0, canvas.height);
            cxt.scale(1, -1);
        } else if (graphType == 'turn_right'){
            // 顺时针90°
            $("#isModify").val(1);
            initLayer();
            
            //cxt.save();
            //turn_count++;
            cxt.clearRect(0,0,canvasWidth,canvasHeight); 
            canvas.width = canvasHeight;
            canvas.height = canvasWidth;
            cxt.translate(canvasHeight,0);
            cxt.rotate(Math.PI/2);
            cxt.drawImage(img, 0, 0);
            //cxt.restore(); 
            
            //cxt.translate(0-canvasHeight, 0);
            cxt.rotate(-Math.PI/2);
            cxt.translate(0-canvasHeight, 0);
            
            canvas_bak.width = canvasHeight;
            canvas_bak.height = canvasWidth;
            
            canvas = document.getElementById('canvas');
            cxt = canvas.getContext('2d');
            canvas_bak = document.getElementById('canvas_bak');
            cxt_bak = canvas_bak.getContext('2d');
            
            canvasWidth = canvas.width;
            canvasHeight = canvas.height;
            initTurn(canvasWidth, canvasHeight);
            
        } else if (graphType == 'turn_left'){
            // 逆时针90°
            $("#isModify").val(1);
            initLayer();
            
            //cxt.save();
            //turn_count++;
            cxt.clearRect(0,0,canvasWidth,canvasHeight); 
            canvas.width = canvasHeight;
            canvas.height = canvasWidth;
            cxt.translate(0, canvasWidth);
            cxt.rotate(-Math.PI/2);
            cxt.drawImage(img, 0, 0);
            //cxt.restore(); 
            
            //cxt.translate(0-canvasHeight, 0);
            cxt.rotate(Math.PI/2);
            cxt.translate(0, 0-canvasWidth);
            
            //cxt.fillText(turn_count+" cxt",60,60);
            
            canvas_bak.width = canvasHeight;
            canvas_bak.height = canvasWidth;
            
            canvas = document.getElementById('canvas');
            cxt = canvas.getContext('2d');
            canvas_bak = document.getElementById('canvas_bak');
            cxt_bak = canvas_bak.getContext('2d');
            
            //cxt_bak.fillText(turn_count+" cxt_bak",50,50);
            
            canvasWidth = canvas.width;
            canvasHeight = canvas.height;
            initTurn(canvasWidth, canvasHeight);
        } else if(graphType == 'same'){
            canvas.style.width = canvasWidth + 'px';
            canvas.style.height = canvasHeight + 'px';
            canvas_bak.style.width = canvasWidth + 'px';
            canvas_bak.style.height = canvasHeight + 'px';
            
            if(winSlider != null){
                winSlider.setValue(100);
            }
        } else if(graphType == 'drag'){
            var dragging = false;
            var iX, iY;
            $("#canvas_bak").die().live("mousedown",function(e) {
                dragging = true;
                iX = e.clientX - this.offsetLeft;
                iY = e.clientY - this.offsetTop;
                //this.setCapture && this.setCapture();
                //document.getElementById("canvas_bak").setCapture();
                $("#canvas_bak").css({"cursor": "move"});
                return false;
            });
            document.onmousemove = function(e) {
                if (dragging) {
                    $("#canvas_bak").css({"cursor": "move"});
                    var e = e || window.event;
                    var oX = e.clientX - iX;
                    var oY = e.clientY - iY;
                    $("#canvas_bak").css({"left":oX + "px", "top":oY + "px"});
                    $("#canvas").css({"left":oX + "px", "top":oY + "px"});
                    return false;
                }
            };
            $(document).mouseup(function(e) {
                dragging = false;
                //$("#canvas_bak")[0].releaseCapture();
                //document.getElementById("canvas_bak").releaseCapture();
                e.cancelBubble = true;
                $("#canvas_bak").css({"cursor": "crosshair"});
            })
        }
    } else {
        $("#isModify").val(1);
        initLayer();
        
        // 鼠标按下获取 开始xy开始画图
        var canDraw = false;
        var startX;
        var startY;
        
        var mousedown = function(e) {
            cxt.strokeStyle = color;
            // cxt_bak.strokeStyle= color;
            // cxt_bak.lineWidth = size;
            // e=e||window.event;
            e = e ? e: window.event

            startX = (e.pageX - this.offsetLeft-192) * scalePencent; // canvasLeft;
            startY = (e.pageY - this.offsetLeft-139) * scalePencent; // canvasTop;
            // console.log(this.offsetLeft-192+" " +this.offsetLeft-139);
            cxt_bak.moveTo(startX, startY);
            canDraw = true;
            
            //cxt_bak_fillStyle = cxt_bak.fillStyle;
            //cxt_bak_strokeStyle = cxt_bak.strokeStyle;
            
            cxt_bak.fillStyle = cxt_bak_fillStyle;
            cxt_bak.strokeStyle = cxt_bak_strokeStyle;
            //console.log("541 "+cxt_bak_strokeStyle);

            if (graphType == 'pencil') {
                cxt.closePath();
                cxt.beginPath();
            } else if (graphType == 'rubber') {
                cxt.clearRect(startX - sizeTemp / 2, startY - sizeTemp / 2, size * 1, size * 1);
            } else if (graphType == 'text') {
                /*var content = window.prompt('请输入文字', '');
                if (content) {
                    var tip_width = 100;
                    if (content.length > 8) {
                        tip_width = content.length * 12.5;
                    }
                    // cxt.fillText(content, startX, startY);
                    cxt.tips(startX, startY, tip_width, 30, 5, content, cxt_bak.fillStyle).stroke();
                    saveImageToAry();
                }*/
                
                cxt_bak.closePath();
                cxt_bak.beginPath();
                
                max_tip_x = startX;
                max_tip_y = startY;
                // 201603
                min_tip_x = startX;
                min_tip_y = startY;
            } else if(graphType == 'straw'){
                // 拾色器
                var imageData = cxt.getImageData(startX, startY, 1, 1);
                var pxData = imageData.data;
                //console.log(pxData);
                var color = 'rgba(' + pxData[0] + ',' + pxData[1] + ',' + pxData[2] + ',' + pxData[3] + ')';
                cxt_bak.strokeStyle = color;
                cxt_bak.fillStyle = color;
                
                var cmyk = rgb2cmyk(pxData[0], pxData[1], pxData[2], 0);
                $("#straw").attr("title","拾色器 "+ color + " ; cmyk("+cmyk.c+" , "+cmyk.m+" , "+cmyk.y+" , "+cmyk.k+") ; " );
                // var PantoneCard;
                // $.get("/Mark/getCMYK",{c:cmyk.c, m:cmyk.m, y:cmyk.y, k:cmyk.k}, function(data){
                //     //PantoneCard = data;
                //     //console.log(PantoneCard);
                //     $("#straw").attr("title","拾色器 "+ color + " ; cmyk("+cmyk.c+" , "+cmyk.m+" , "+cmyk.y+" , "+cmyk.k+") ; " + data.name + "("+data.c+" , "+data.m+" , "+data.y+" , "+data.k+")");
                // });
                
            } else if (graphType == 'rule' || graphType == 'rect_rule'){
                imgSize = $("#imgSize").val();
                if(imgSize != ''){
                    $("#imgSize").val(parseFloat(imgSize));
                    imgSize = $("#imgSize").val();
                }
                
                if(imgSize == '' || isNaN(imgSize)){
                    alert("请输入图像实际宽度,单位毫米");
                    $("#imgSize").addClass("red_border");
                    return false;
                } else {
                    $("#imgSize").removeClass("red_border");
                }
            }
//            else if(graphType == 'part_enlarge'){
//                canDraw = false;
//            }
        };

        // 鼠标离开 把蒙版canvas的图片生成到canvas中
        var mouseup = function(e) {
            // e=e||window.event;
            e = e ? e: window.even;
//            var xx = (e.pageX - this.offsetLeft-192) * scalePencent;// canvasLeft;
//            var yy = (e.pageY - this.offsetLeft-139) * scalePencent;// canvasTop;

            canDraw = false;
            var image = document.createElement("img"); // new Image();
            
            if (graphType == 'text' || graphType == 'linetext' || graphType == 'squaretext' || graphType == 'circletext') {
                var content = window.prompt('请输入文字', '');
                    //增加注释
                    if(content){
                        num += 1;
                        $('#info').val($('#info').val() + num+"."+content+" \r\n");  
                    }
                    
                if (content) {
                    var tip_width = 100;
                    if (content.length > 8) {
                        tip_width = content.length * 12.5;
                    }
                    // cxt.fillText(content, startX, startY);
                    //cxt.tips(startX, startY, tip_width, 30, 5, content, cxt_bak.fillStyle).stroke();
                    cxt_bak.tips(max_tip_x, max_tip_y,min_tip_x, min_tip_y, tip_width, 30, 5, content, cxt_bak.fillStyle).stroke();
                    //saveImageToAry();
                    
                    image.src = canvas_bak.toDataURL();
                    image.onload = function() {
                        cxt.drawImage(image, 0, 0, image.width, image.height, 0, 0, canvasWidth, canvasHeight);
                        clearContext();
                        saveImageToAry();// 201603
                    };
                } else {
                    clearContext();
                }
                
                 // 初始化 201603
                max_tip_x = -1;
                max_tip_y = -1;
                min_tip_x = canvasWidth + 1;
                min_tip_y = canvasHeight + 1;
            } else{
                if (graphType != 'rubber' && graphType != 'part_enlarge' && graphType != 'pencil') { //&& graphType != 'text' 

                    image.src = canvas_bak.toDataURL();
                    image.onload = function() {
                        cxt.drawImage(image, 0, 0, image.width, image.height, 0, 0, canvasWidth, canvasHeight);
                        //saveImageToAry(); // 201603
                        clearContext();
                        //setTimeout("clearContext()",500)
                    };
                }
                saveImageToAry(); // 201603
            }
        };
        
        function preImage(url, callback) {
            var img = new Image(); // 创建一个Image对象，实现图片的预下载
            img.src = url;

            if (img.complete) { // 如果图片已经存在于浏览器缓存，直接调用回调函数
                callback.call(img);
                return; // 直接返回，不用再处理onload事件
            }

            img.onload = function() { // 图片下载完毕时异步调用callback函数。
                callback.call(img); // 将回调函数的this替换为Image对象
            };
        }  

        // 鼠标移动
        var max_tip_x = -1;
        var max_tip_y = -1;
        var min_tip_x = canvasWidth + 1;
        var min_tip_y = canvasHeight + 1;
        var circle_move = 0; // 解决画圆闪烁问题
        var mousemove = function(e) {
            // e=e||window.event;
            e = e ? e : window.even;

            var o_x = e.pageX;
            var o_y = e.pageY;
            var x = (e.pageX - this.offsetLeft-192) * scalePencent;// canvasLeft;
            var y = (e.pageY - this.offsetLeft-139) * scalePencent;// canvasTop;
            //console.log(x + " " + y);

 	    //max_tip_x = x;
            //max_tip_y = y;
            //min_tip_x = x;
            //min_tip_y = y;
            if (graphType == 'line' || graphType == 'linetext') {
                // 直线
                if (canDraw) {
                    cxt_bak.beginPath();
                    clearContext();
                    cxt_bak.moveTo(startX, startY);
                    cxt_bak.lineTo(x, y);
                    cxt_bak.stroke();
                    
                    // 在x坐标最大的点tips
                    if(x > max_tip_x){
                        max_tip_x = x;
                        max_tip_y = y;
                    }
                      // 201603
                    //console.log(x +" "+ min_tip_x);
                    if(x < min_tip_x){
                        min_tip_x = x;
                        min_tip_y = y;
                    }
                }
            } else if (graphType == 'rule') {
                //var scaleDivWidth = $("#scaleDiv").width(); // 38px
                //var scaleUnit = accDiv(10,scaleDivWidth);// 一个像素是多少毫米 10.0 / scaleDivWidth;
                
                var scaleUnit = accDiv(parseFloat(imgSize), canvasWidth);// 一个像素是多少毫米 10.0 / scaleDivWidth;
                
                // 测距
                if (canDraw) {
                    clearContext();
                    cxt_bak.beginPath();
                    cxt_bak.moveTo(startX, startY);
                    cxt_bak.lineTo(x, y);
                    cxt_bak.stroke();

                    var distance = Math.sqrt((x - startX) * (x - startX) + (y - startY) * (y - startY));
                    if (distance > 0) {
                     
                        var mm = changeTwoDecimal(distance *　scaleUnit);
                        // console.log(mm);
                     
                        cxt_bak.fillText(mm + "mm", (x + startX) / 2 + 10, (y + startY) / 2 + 10);
                        cxt_bak.beginPath();

                        // 起点
                        var x1 = startX - 5 * (y - startY) / distance
                        var x2 = startX + 5 * (y - startY) / distance
                        var y1 = startY + 5 * (x - startX) / distance
                        var y2 = startY - 5 * (x - startX) / distance
                        // saveImageToAry();

                        cxt_bak.moveTo(x1, y1); // 起始位置
                        cxt_bak.lineTo(x2, y2);
                        // cxt.stroke();

                        // 终点
                        var x_e1 = x1 + (x - startX);
                        var x_e2 = x2 + (x - startX);
                        var y_e1 = y1 + (y - startY);
                        var y_e2 = y2 + (y - startY);
                        cxt_bak.moveTo(x_e1, y_e1); // 起始位置
                        cxt_bak.lineTo(x_e2, y_e2);
                        cxt_bak.stroke();
                    }
                }
            } else if (graphType == 'rect_rule') {
                // 矩形测量
                //var scaleDivWidth = $("#scaleDiv").width(); // 38px
                //var scaleUnit = accDiv(10,scaleDivWidth);// 一个像素是多少毫米 10.0 / scaleDivWidth;
                
                var scaleUnit = accDiv(parseFloat(imgSize), canvasWidth);// 一个像素是多少毫米 10.0 / scaleDivWidth;
                
                // 方块 4条直线搞定
                if (canDraw) {
                    clearContext();
                    cxt_bak.beginPath();
                    cxt_bak.moveTo(startX, startY);
                    cxt_bak.lineTo(x, startY);
                    cxt_bak.lineTo(x, y);
                    cxt_bak.lineTo(startX, y);
                    cxt_bak.lineTo(startX, startY);
                    cxt_bak.stroke();
                    
                    var x_distance = Math.sqrt((x - startX) * (x - startX));
                    if (x_distance > 0) {
                        var x_mm = changeTwoDecimal(x_distance *　scaleUnit);
                        
                        cxt_bak.fillText(x_mm + "mm", (x + startX) / 2 - 10, startY + 10);
                        cxt_bak.beginPath();
                    }
                    
                    var y_distance = Math.sqrt((y - startY) * (y - startY));
                    if (y_distance > 0) {
                        var y_mm = changeTwoDecimal(y_distance *　scaleUnit);
                        
                        cxt_bak.fillText(y_mm + "mm", startX + 2, (y + startY) / 2);
                        cxt_bak.beginPath();
                    }
                }
            } else if (graphType == 'pencil') {
                // 画笔
                if (canDraw) {
                    // cxt_bak.lineTo(e.clientX - canvasLeft ,e.clientY - canvasTop); //canvasTop
                    cxt.lineTo(x, y); // canvasTop
                    cxt.stroke();
                }
            } else if (graphType == 'text') {
                // 画笔
                if (canDraw) {
                    // cxt_bak.lineTo(e.clientX - canvasLeft ,e.clientY - canvasTop); //canvasTop
                    cxt_bak.lineTo(x, y); // canvasTop
                    cxt_bak.stroke();
                    
                    // 在x坐标最大的点tips
                    if(x > max_tip_x){
                        max_tip_x = x;
                        max_tip_y = y;
                    }
		    // 201603
                     if(x < min_tip_x){
                        min_tip_x = x;
                        min_tip_y = y;
                    }
                }
            } else if (graphType == 'circle' || graphType == 'circletext') {
                // 圆 未画得时候 出现一个小圆
                if (canDraw) {
                    circle_move = 1; // 解决画圆闪烁问题
                    clearContext();
                    cxt_bak.beginPath();
                    var radii = Math.sqrt((startX - x) * (startX - x) + (startY - y) * (startY - y));
                    cxt_bak.arc(startX, startY, radii, 0, Math.PI * 2, false);
                    cxt_bak.stroke();
                    
                    // 在x坐标最大的点tips
//                    if(x > max_tip_x){
                        max_tip_x = startX + radii;
                        max_tip_y = startY;
//                    }
       		// 201603
                        min_tip_x = startX - radii;
                        min_tip_y = startY;
                } else {
                    if (graphType == 'circle' && circle_move == 0){
                        clearContext();
                        cxt_bak.beginPath();
                        cxt_bak.arc(x, y, 5, 0, Math.PI * 2, false);
                        cxt_bak.stroke();
                    }
                }
            } else if (graphType == 'fillCircle') {
                // 实心圆
                if (canDraw) {
                    clearContext();
                    cxt_bak.beginPath();
                    var radii = Math.sqrt((startX - x) * (startX - x) + (startY - y) * (startY - y));
                    cxt_bak.arc(startX, startY, radii, 0, Math.PI * 2, false);
                    cxt_bak.fill();
                }
                /*
                 * else { cxt_bak.beginPath(); cxt_bak.arc(x,y,20,0,Math.PI * 2,false); cxt_bak.fill(); }
                 */
            } else if (graphType == 'square' || graphType == 'squaretext') {
                // 方块 4条直线搞定
                if (canDraw) {
                    clearContext();
                    cxt_bak.beginPath();
                    cxt_bak.moveTo(startX, startY);
                    cxt_bak.lineTo(x, startY);
                    cxt_bak.lineTo(x, y);
                    cxt_bak.lineTo(startX, y);
                    cxt_bak.lineTo(startX, startY);
                    cxt_bak.stroke();
                    
                 // 在x坐标最大的点tips
//                  if(x > max_tip_x){
                      max_tip_x = x;
                      max_tip_y = startY;
//                  }
                      
                      // 201603
                      min_tip_x = startX;
                      min_tip_y = y;

                }
            } else if (graphType == 'fillSquare') {
                // 实心方块
                if (canDraw) {
                    clearContext();
                    cxt_bak.beginPath();
                    cxt_bak.fillRect(startX, startY, x - startX, y - startY);
                }
            } else if (graphType == 'poly') {
                // 三角形
                if (canDraw) {
                    clearContext();
                    cxt_bak.beginPath();
                    cxt_bak.moveTo(startX, startY);
                    cxt_bak.lineTo(x, y);
                    cxt_bak.lineTo(startX - (x - startX), y);

                    cxt_bak.closePath();
                    cxt_bak.stroke();
                }
            } else if (graphType == 'handwriting') {
                // 涂鸦 未画得时候 出现一个小圆
                if (canDraw) {
                    cxt_bak.beginPath();
                    cxt_bak.strokeStyle = color;
                    cxt_bak.fillStyle = color;
                    cxt_bak.arc(x, y, size * 10, 0, Math.PI * 2, false);
                    cxt_bak.fill();
                    cxt_bak.stroke();
                    cxt_bak.restore();
                } else {
                    clearContext();
                    cxt_bak.beginPath();
                    cxt_bak.fillStyle = color;
                    cxt_bak.arc(x, y, size * 10, 0, Math.PI * 2, false);
                    cxt_bak.fill();
                    cxt_bak.stroke();
                }
            } else if (graphType == 'rubber') {
                // 橡皮擦 不管有没有在画都出现小方块 按下鼠标 开始清空区域
                cxt_bak.lineWidth = 1;
                clearContext();
                cxt_bak.beginPath();
                cxt_bak.strokeStyle = '#000000';
    // cxt_bak.moveTo(x - size * 1, y - size * 1);
    // cxt_bak.lineTo(x + size * 1, y - size * 1);
    // cxt_bak.lineTo(x + size * 1, y + size * 1);
    // cxt_bak.lineTo(x - size * 1, y + size * 1);
    // cxt_bak.lineTo(x - size * 1, y - size * 1);
                cxt_bak.moveTo(x - sizeTemp/2, y - sizeTemp/2);
                cxt_bak.lineTo(x + sizeTemp/2, y - sizeTemp/2);
                cxt_bak.lineTo(x + sizeTemp/2, y + sizeTemp/2);
                cxt_bak.lineTo(x - sizeTemp/2, y + sizeTemp/2);
                cxt_bak.lineTo(x - sizeTemp/2, y - sizeTemp/2);
                cxt_bak.stroke();
                if (canDraw) {
                    cxt.clearRect(x - sizeTemp/2, y - sizeTemp/2, sizeTemp, sizeTemp);
                }
            } else if (graphType == 'part_enlarge'){
                var partSize = 100;
                
                $("#partEnlargeDiv").show();
                $("#partEnlargeDiv").css({"top":o_y-(partSize)/scalePencent-18,"left":o_x+(partSize)/scalePencent-68});
                
                // 局部放大
                cxt_bak.lineWidth = 1;
                clearContext();
                cxt_bak.beginPath();
                
                //cxt_bak.strokeStyle = '#000000';
                cxt_bak.fillStyle = 'rgba(255,0,0,0.25)';
                cxt_bak.strokeStyle = 'rgba(255,0,0,0.25)';
                // console.log("880 " + cxt_bak_fillStyle + "  " + cxt_bak.fillStyle);
                
                cxt_bak.moveTo(x - partSize, y - partSize);
                cxt_bak.lineTo(x + partSize, y - partSize);
                cxt_bak.lineTo(x + partSize, y + partSize);
                cxt_bak.lineTo(x - partSize, y + partSize);
                cxt_bak.lineTo(x - partSize, y - partSize);
                cxt_bak.fill();
                cxt_bak.stroke();
                
                
                var imageData = cxt.getImageData(x - partSize, y - partSize, partSize * 2, partSize * 2);
                //console.log(imageData.data);
                //var pxData = imageData.data;
                
                cxt_enlarge.putImageData(imageData,0,0);
                //if (canDraw) {
                    //cxt.clearRect(x - partSize, y - partSize, partSize * 2, partSize * 2);
                //}
                
                //$(canvas_enlarge).width();
                canvas_enlarge.style.width = '400px';
                canvas_enlarge.style.height = '400px';
            }
        };

        // 鼠标离开区域以外 除了涂鸦 都清空
        var mouseout = function() {
            canDraw = false;
            if (graphType == 'circle' || graphType == 'rubber' || graphType == 'part_enlarge'){
                //console.log("out");
                clearContext();
            }
            // if(graphType != 'handwriting'){
            // clearContext();
            // }
        }

        //$(canvas_bak).unbind();
        $(canvas_bak).bind('mousedown', mousedown);
        $(canvas_bak).bind('mousemove', mousemove);
        $(canvas_bak).bind('mouseup', mouseup);
        $(canvas_bak).bind('mouseout', mouseout);
    }
}

// 清空层
var clearContext = function(type) {
    if (!type) {
        cxt_bak.clearRect(0, 0, canvasWidth, canvasHeight);
    } else {
        cxt.clearRect(0, 0, canvasWidth, canvasHeight);
        cxt_bak.clearRect(0, 0, canvasWidth, canvasHeight);
    }
}

// 圆角矩形(气泡对话) cxt_bak.tips(300,300,100,100,10).stroke();
CanvasRenderingContext2D.prototype.roundRect = function(x, y, w, h, r) {
    // if (w < 2 * r) r = w / 2;
    // if (h < 2 * r) r = h / 2;
    this.lineWidth = 1;
    this.fillStyle = 'rgba(255,0,0,0.25)';
    this.strokeStyle = 'rgba(255,0,0,0.25)';
    this.beginPath();
    
    // x, y, w, h, r
    this.moveTo(x + r, y);
    // this.arcTo(x+w, y, x+w, y+h, r);
    // this.arcTo(x+w, y+h, x, y+h, r);
    // this.arcTo(x, y+h, x, y, r);
    // this.arcTo(x, y, x+w, y, r);
    this.arcTo(x + w, y, x + w, y + h, r);
    this.arcTo(x + w, y + h, x+w/2, y + h,r);
    this.arcTo(x+w/2, y + h, x, y + h + 50, r*4);
    this.lineTo(x, y + h + 50, x+w/3, y + h);
    this.arcTo(x+w/3, y + h, x, y + h, r);
    this.arcTo(x, y + h, x, y, r);
    this.arcTo(x, y, x + w, y, r);
    // this.arcTo(x+r, y);
    this.fill();
    this.closePath();
    return this;
}

// 矩形(气泡对话) cxt_bak.tipsRect(10,10,100,100).stroke();
CanvasRenderingContext2D.prototype.tipsRect = function(x, y, w, h) {
    this.lineWidth = 1;
    this.fillStyle = 'rgba(255,0,0,0.25)';
    this.strokeStyle = 'rgba(255,0,0,0.25)';
    this.beginPath();
    this.moveTo(x, y);

    this.lineTo(x + w, y, x + w, y + h);
    this.lineTo(x + w, y + h, x+w/2, y + h);
    this.lineTo(x+w/2, y + h, x, y + h + 50);
    this.lineTo(x, y + h + 50, x+w/3, y + h);
    this.lineTo(x+w/3, y + h, x, y+h);
    this.lineTo(x, y+h, x, y);
    // this.arcTo(x+r, y);
    this.fill();
    this.closePath();
    return this;
}

// 201603
CanvasRenderingContext2D.prototype.tips = function(max_x, max_y, min_x, min_y, w, h, r, text, color) {
    // if (w < 2 * r) r = w / 2;
    // if (h < 2 * r) r = h / 2;
//debugger
	var th = 50; // tips.height
	var tw = w; // tips.width
	//var tips_weight = 50;
	
	//console.log(max_x);
	//console.log(max_y);
	//console.log(min_x);
	//console.log(min_y);
 
    // w=100;
    // h=30;
 
    this.lineWidth = 1;
    this.fillStyle = tips_bgcolor;//'rgba(255,0,0,0.25)';
    this.strokeStyle = tips_bgcolor;//'rgba(255,0,0,0.25)';
    this.beginPath();
    //canvasWidth
    //canvasHeight
    
    var x,y;
    if(max_x + w > canvasWidth || max_y - th < 0){
        x = min_x;
        y = min_y;
    } else {
        x = max_x;
        y = max_y;
    }
    
    if(x + tw < canvasWidth && y - th > 0){
    	// 右上
    this.moveTo(x, y);
    this.arcTo(x + w/3, y-20, x , y -20, 5);
    this.arcTo(x , y -20, x , y -20-h, 5);
    this.arcTo(x , y -20-h, x+w , y -20-h, 5);
    this.arcTo(x+w , y -20-h, x+w , y -20, 5);
    this.arcTo(x+w , y -20, x+w/2 , y -20, 5);
    this.arcTo(x+w/2 , y -20, x , y, 5);
    this.fill();
    this.closePath();
    
    this.fillStyle = color;
    this.fillText(text, x+3, y-20-10);
    } else if(x + tw < canvasWidth && y + th < canvasHeight){
        // 右下
        this.moveTo(x, y);
        this.arcTo(x + w/3, y + 20, x, y + 20, 5);
        this.arcTo(x, y + 20, x, y + 20 + h, 5);
        this.arcTo(x, y + 20 + h, x + w, y + 20 + h, 5);
        this.arcTo(x + w, y + 20 + h, x + w, y + 20, 5);
        this.arcTo(x + w, y + 20, x + w/2, y + 20, 5);
        this.arcTo(x + w/2, y + 20, x, y, 5);
        this.fill();
        this.closePath();
        
        this.fillStyle = color;
        this.fillText(text, x + 3, y + 20 + 15);
    } else if(x - tw > 0 && y + th < canvasHeight){
        // 左下
        this.moveTo(x, y);
        this.arcTo(x - w/3, y + 20, x, y + 20, 5);
        this.arcTo(x, y + 20, x, y + 20 + h, 5);
        this.arcTo(x, y + 20 + h, x - w, y + 20 + h, 5);
        this.arcTo(x - w, y + 20 + h, x - w, y + 20, 5);
        this.arcTo(x - w, y + 20, x - w/2, y + 20, 5);
        this.arcTo(x - w/2, y + 20, x, y, 5);
        this.fill();
        this.closePath();
        
        this.fillStyle = color;
        this.fillText(text, x - tw + 3, y + 20 + 15);
    } else if(x - tw > 0 && y - th > 0){
        // 左上
        this.moveTo(x, y);
        this.arcTo(x - w/3, y-20, x, y - 20, 5);
        this.arcTo(x, y - 20, x, y - 20 - h, 5);
        this.arcTo(x, y - 20 - h, x - w, y - 20 - h, 5);
        this.arcTo(x - w, y - 20 - h, x - w, y - 20, 5);
        this.arcTo(x - w, y - 20, x - w/2, y - 20, 5);
        this.arcTo(x - w/2, y -20, x, y, 5);
        this.fill();
        this.closePath();
        
        this.fillStyle = color;
        this.fillText(text, x - tw + 3, y - 20 - 10);
    } else {
        // 默认右上
        this.moveTo(x, y);
        this.arcTo(x + w/3, y-20, x, y - 20, 5);
        this.arcTo(x, y - 20, x, y - 20 - h, 5);
        this.arcTo(x, y - 20 - h, x + w, y - 20 - h, 5);
        this.arcTo(x + w, y - 20 - h, x + w, y - 20, 5);
        this.arcTo(x + w, y - 20, x + w/2, y - 20, 5);
        this.arcTo(x + w/2, y -20, x, y, 5);
        this.fill();
        this.closePath();
        
        this.fillStyle = color;
        this.fillText(text, x + 3, y - 20 - 10);
    }
//    this.moveTo(x, y);
//    this.arcTo(x + w/3, y-20, x, y - 20, 5);
//    this.arcTo(x, y - 20, x, y - 20 - h, 5);
//    this.arcTo(x, y - 20 - h, x + w, y - 20 - h, 5);
//    this.arcTo(x + w, y - 20 - h, x + w, y - 20, 5);
//    this.arcTo(x + w, y - 20, x + w/2, y - 20, 5);
//    this.arcTo(x + w/2, y -20, x, y, 5);
//    this.fill();
//    this.closePath();
//    
//    this.fillStyle = color;
//    this.fillText(text, x + 3, y - 20 - 10);
    // x, y, w, h, r
    // this.moveTo(x + r, y);
    // // this.arcTo(x+w, y, x+w, y+h, r);
    // // this.arcTo(x+w, y+h, x, y+h, r);
    // // this.arcTo(x, y+h, x, y, r);
    // // this.arcTo(x, y, x+w, y, r);
    // this.arcTo(x + w, y, x + w, y + h, r);
    // this.arcTo(x + w, y + h, x+w/2, y + h,r);
    // this.arcTo(x+w/2, y + h, x, y + h + 50, r*4);
    // this.lineTo(x, y + h + 50, x+w/3, y + h);
    // this.arcTo(x+w/3, y + h, x, y + h, r);
    // this.arcTo(x, y + h, x, y, r);
    // this.arcTo(x, y, x + w, y, r);
    // // this.arcTo(x+r, y);
    // this.fill();
    // this.closePath();
    return this;
}

draw_graph('pencil', 'actions', $("#brush")) // 初始化铅笔

// 两数相除num1/num2
function accDiv(num1, num2) {
    var t1, t2, r1, r2;
    try {
        t1 = num1.toString().split('.')[1].length;
    } catch(e) {
        t1 = 0;
    }
    try {
        t2 = num2.toString().split(".")[1].length;
    } catch(e) {
        t2 = 0;
    }
    r1 = Number(num1.toString().replace(".", ""));
    r2 = Number(num2.toString().replace(".", ""));
    return (r1 / r2) * Math.pow(10, t2 - t1);
}

// console.log(accDiv(scaleDivWidth,10));
// 精确到小数点后2位
function changeTwoDecimal(floatvar) {
    var f_x = parseFloat(floatvar);
    if (isNaN(f_x)) {
        alert('number error');
        return false;
    }
    var f_x = Math.round(floatvar * 100) / 100;
    return f_x;
}

$(document).ready(function() {
    
});