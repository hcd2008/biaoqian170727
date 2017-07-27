var canvasfon = document.getElementById('canvasfon');
var ctxfon = canvasfon.getContext('2d');
var canvascur = document.getElementById('canvascur');
var ctxcur = canvascur.getContext('2d');
var canvasrule = document.getElementById('canvasrule');
var ctxrule = canvasrule.getContext('2d');
var board = document.getElementsByClassName('board')[0];
var w = 0;
var h = 0;
var rotate = 0;
var rotateStep = 90;
var scale = 1;
var scaleStep = 0.25;
var cur = '';
var draw = false;
var beginX = 0;
var beginY = 0;
var record = {
    index: 0,
    cache: []
}
var alpha = 0.5;
var xiantiaose = '#000000';
var biaozhuse = 'rgba(12, 8, 247, ' + alpha + ')'; //标注背景色
var wenzise = '#000000'; //标注文字颜色
var guiji = []; //不规则标注，记录运动轨迹
var resolution = 0; //图片分辨率
resolution=$("#chicun").val();
var ckx = []; //缓存参考线
var zh = 20;//字号
var cx = 1;
var ruleH = false;
var ruleV = false;

var img = new Image();
img.onload = function () {
    w = img.width > 5000 ? 5000 : img.width;
    h = img.width > 5000 ? parseInt(img.height / img.width * 5000) : img.height;
    board.style.width = w + 'px';
    board.style.height = h + 'px';
    canvasfon.width = w;
    canvasfon.height = h;
    canvascur.width = w;
    canvascur.height = h;
    canvasrule.width = w;
    canvasrule.height = h;

    ctxfon.drawImage(img, 0, 0, w, h);
    record.cache.push(canvasfon.toDataURL());

    ctxfon.font = '20px serif';
    ctxrule.font = '10px serif';
    ctxrule.strokeStyle = '#0cdbf9'

    EXIF.getData(img, function () {
        var tags = EXIF.getAllTags(img);
        resolution = parseInt(tags.XResolution || tags.YResolution) / 2.54;
    });
}
img.src = document.getElementById('myimg').value;
// img.src = "/file/upload/two.jpg";

canvascur.onmousedown = function (e) {
    if (cur) {
        beginX = e.offsetX;
        beginY = e.offsetY;

        switch (cur) {
            case 'buguize':
            case 'zhixian':
            case 'juxing':
            case 'yuanxing':
            case 'danxian':
            case 'juxingceliang':
            case 'xz0':
            case 'xz1':
            case 'xz2':
            case 'xz3':
            case 'xz4':
            case 'xz5':
            case 'xz6':
            case 'xz7':
                draw = true;
                break;
            case 'cankaoxian':
                if (e.offsetX < 20) {
                    ruleV = true;
                    ruleH = false;
                    draw = true;
                    canvascur.style.cursor = 'col-resize';

                    ctxcur.clearRect(0, 0, w, h);
                    ruleLine(ctxcur, beginX, beginY);
                } else if (e.offsetY < 20) {
                    ruleH = true;
                    ruleV = false;
                    draw = true;
                    canvascur.style.cursor = 'row-resize';

                    ctxcur.clearRect(0, 0, w, h);
                    ruleLine(ctxcur, beginX, beginY);
                } else {
                    var hitX = [e.offsetX - 3, e.offsetX + 3];
                    var hitY = [e.offsetY - 3, e.offsetY + 3];

                    for (var i = 0; i < ckx.length; i++) {
                        if (ckx[i].direction == 'h') {
                            if (ckx[i].y > hitY[0] && ckx[i].y < hitY[1]) {
                                ruleH = true;
                                ruleV = false;
                                draw = true;
                                canvascur.style.cursor = 'row-resize';

                                ctxrule.clearRect(0, ckx[i].y - 1, w, 2);
                                ckx.splice(i, 1);
                                break;
                            }
                        } else {
                            if (ckx[i].x > hitX[0] && ckx[i].x < hitX[1]) {
                                ruleV = true;
                                ruleH = false;
                                draw = true;
                                canvascur.style.cursor = 'col-resize';

                                ctxrule.clearRect(ckx[i].x - 1, 0, 2, h);
                                ckx.splice(i, 1);
                                break;
                            }
                        }
                    }
                }
                break;
        }
    }
}

canvascur.onmousemove = function (e) {
    if (draw) {
        switch (cur) {
            case 'buguize':
            case 'xz0':
                guiji.push({
                    x1: beginX,
                    y1: beginY,
                    x2: e.offsetX,
                    y2: e.offsetY
                });
                buguize(ctxcur, beginX, beginY, e.offsetX, e.offsetY);
                beginX = e.offsetX;
                beginY = e.offsetY;
                break;
            case 'zhixian':
            case 'xz1':
                ctxcur.clearRect(0, 0, w, h);
                zhixian(ctxcur, beginX, beginY, e.offsetX, e.offsetY);
                break;
            case 'juxing':
            case 'xz2':
                ctxcur.clearRect(0, 0, w, h);
                juxing(ctxcur, beginX, beginY, e.offsetX, e.offsetY);
                break;
            case 'yuanxing':
            case 'xz3':
                ctxcur.clearRect(0, 0, w, h);
                var mX = e.offsetX - beginX;
                var mY = e.offsetY - beginY;
                yuanxing(ctxcur, beginX, beginY, Math.sqrt(mX * mX + mY * mY));
                break;
            case 'danxian':
                ctxcur.clearRect(0, 0, w, h);
                zhixian(ctxcur, beginX, beginY, e.offsetX, e.offsetY);
                break;
            case 'juxingceliang':
                ctxcur.clearRect(0, 0, w, h);
                juxing(ctxcur, beginX, beginY, e.offsetX, e.offsetY);
                break;
            case 'cankaoxian':
                ctxcur.clearRect(0, 0, w, h);
                ruleLine(ctxcur, e.offsetX, e.offsetY);
                break;
            case 'xz4':
                //三角形
                ctxcur.clearRect(0, 0, w, h);
                sanjiao(ctxcur, beginX, beginY, e.offsetX, e.offsetY);
                break;
            case 'xz5':
                //实心矩形
                ctxcur.clearRect(0, 0, w, h);
                juxing(ctxcur, beginX, beginY, e.offsetX, e.offsetY, true);
                break;
            case 'xz6':
                ctxcur.clearRect(0, 0, w, h);
                var mX = e.offsetX - beginX;
                var mY = e.offsetY - beginY;
                yuanxing(ctxcur, beginX, beginY, Math.sqrt(mX * mX + mY * mY), true);
                //实心圆形
                break;
            case 'xz7':
                //实心三角形
                ctxcur.clearRect(0, 0, w, h);
                sanjiao(ctxcur, beginX, beginY, e.offsetX, e.offsetY, true);
                break;
        }
    } else if (cur == 'fangdajing') {
        ctxcur.clearRect(0, 0, w, h);
        zoom(e.offsetX, e.offsetY);
    } else if (cur == 'quse') {
        ctxcur.clearRect(0, 0, w, h);
        pick(e.offsetX, e.offsetY);
    }
}

canvascur.onmouseup = function (e) {
    if (draw) {
        canvascur.style.cursor = 'default';
        ctxcur.clearRect(0, 0, w, h);
        draw = false;

        switch (cur) {
            case 'buguize':
                if (guiji.length > 0) {
                    var content = tipContent();
                    if (content) {
                        buguizeReDraw();
                        tip(ctxfon, content, guiji[guiji.length - 1].x2, guiji[guiji.length - 1].y2);
                        snap();
                    }
                    guiji = [];
                }
                break;
            case 'zhixian':
                if (beginX != e.offsetX || beginY != e.offsetY) {
                    var content = tipContent();
                    if (content) {
                        zhixian(ctxfon, beginX, beginY, e.offsetX, e.offsetY);
                        tip(ctxfon, content, e.offsetX, e.offsetY);
                        snap();
                    }
                }
                break;
            case 'juxing':
                if (beginX != e.offsetX && beginY != e.offsetY) {
                    var content = tipContent();
                    if (content) {
                        juxing(ctxfon, beginX, beginY, e.offsetX, e.offsetY);
                        tip(ctxfon, content, e.offsetX, e.offsetY);
                        snap();
                    }
                }
                break;
            case 'yuanxing':
                if (beginX != e.offsetX || beginY != e.offsetY) {
                    var content = tipContent();
                    if (content) {
                        var mX = e.offsetX - beginX;
                        var mY = e.offsetY - beginY;
                        yuanxing(ctxfon, beginX, beginY, Math.sqrt(mX * mX + mY * mY));
                        tip(ctxfon, content, e.offsetX, e.offsetY);
                        snap();
                    }
                }
                break;
            case 'danxian':
                if (beginX != e.offsetX || beginY != e.offsetY) {
                    danxian(ctxfon, beginX, beginY, e.offsetX, e.offsetY);
                    snap();
                }
                break;
            case 'juxingceliang':
                if (beginX != e.offsetX && beginY != e.offsetY) {
                    juxingceliang(ctxfon, beginX, beginY, e.offsetX, e.offsetY);
                    snap();
                }
                break;
            case 'cankaoxian':
                if (ruleV == true && e.offsetX < 20) {
                    ctxcur.clearRect(0, 0, w, h);
                } else if (ruleH == true && e.offsetY < 20) {
                    ctxcur.clearRect(0, 0, w, h);
                } else {
                    ruleLine(ctxrule, e.offsetX, e.offsetY);
                    ckx.push({
                        direction: ruleH ? 'h' : 'v',
                        x: ruleH ? 0 : e.offsetX,
                        y: ruleH ? e.offsetY : 0
                    });
                }

                ruleH = false;
                ruleV = false;
                break;
            case 'xz0':
                buguizeReDraw();
                snap();
                guiji = [];
                break;
            case 'xz1':
                zhixian(ctxfon, beginX, beginY, e.offsetX, e.offsetY);
                snap();
                break;
            case 'xz2':
                juxing(ctxfon, beginX, beginY, e.offsetX, e.offsetY);
                snap();
                break;
            case 'xz3':
                var mX = e.offsetX - beginX;
                var mY = e.offsetY - beginY;
                yuanxing(ctxfon, beginX, beginY, Math.sqrt(mX * mX + mY * mY));
                snap();
                break;
            case 'xz4':
                sanjiao(ctxfon, beginX, beginY, e.offsetX, e.offsetY);
                snap();
                break;
            case 'xz5':
                juxing(ctxfon, beginX, beginY, e.offsetX, e.offsetY, true);
                snap();
                break;
            case 'xz6':
                var mX = e.offsetX - beginX;
                var mY = e.offsetY - beginY;
                yuanxing(ctxfon, beginX, beginY, Math.sqrt(mX * mX + mY * mY), true);
                snap();
                break;
            case 'xz7':
                sanjiao(ctxfon, beginX, beginY, e.offsetX, e.offsetY, true);
                snap();
                break;
        }
    } else if (cur == 'wenzi') {
        canvascur.style.cursor = 'default';
        var content = tipContent();
        if (content) {
            wenzi(ctxfon, content, e.offsetX, e.offsetY);
            snap();
        }
    }
}

canvascur.onclick = function () {
    if (cur == 'quse' && qs) {
        document.getElementById('clipBtn').click();
        alert('已复制到剪切板');
    }
}

new Clipboard('#clipBtn', {
    text: function (trigger) {
        return qs;
    }
});

//快照
function snap() {
    record.index++;
    record.cache = record.cache.slice(0, record.index);
    record.cache.push(canvasfon.toDataURL());
    beginX = 0;
    beginY = 0;
    $("#resinfo").show();
}

//缩放
function suofang(type) {
    if ('huanyuan' == type) {
        scale = 1;
    } else if ('fangda' == type) {
        scale += scaleStep;
    } else {
        if (scale > 0.25) {
            scale -= scaleStep;
        }
    }

    board.style.transform = 'scale(' + scale + ') rotate(' + rotate + 'deg)';
}

//放大镜
var zoom = function (x, y) {
    var minW = 250;
    var minH = 150;

    var dx = 0;
    var dy = 0;

    if ((x + minW) <= w) {
        //右
        if ((y - minH) >= 0) {
            //上
            dx = x;
            dy = y - minH;
        } else {
            //下
            dx = x;
            dy = y;
        }
    } else {
        //左
        if ((y - minH) >= 0) {
            //上
            dx = x - minW;
            dy = y - minH;
        } else {
            //下
            dx = x - minW;
            dy = y;
        }
    }

    ctxcur.drawImage(canvasfon,
        Math.abs(x - 5),
        Math.abs(y - 5),
        50, 50,
        dx, dy,
        minW, minH);

    juxing(ctxcur, dx, dy, dx + minW, dy + minH);
};

//取色器
var qs = '';
function pick(x, y) {
    var pixel = ctxfon.getImageData(x, y, 1, 1);
    var data = pixel.data;
    qs = 'rgba(' + data[0] + ',' + data[1] +
        ',' + data[2] + ',' + data[3] + ')';

    ctxcur.fillStyle = qs;
    ctxcur.beginPath();
    ctxcur.arc(x, y, 30, 0, Math.PI * 2);
    ctxcur.stroke();
    ctxcur.fill();

}

//不规则标注
function buguize(ctx, x1, y1, x2, y2) {
    ctx.strokeStyle = xiantiaose;
    ctx.beginPath();
    ctx.moveTo(x1, y1);
    ctx.lineTo(x2, y2);
    ctx.stroke();
}

function buguizeReDraw() {
    ctxfon.strokeStyle = xiantiaose;
    ctxfon.beginPath();
    for (var i = 0; i < guiji.length; i++) {
        var gj = guiji[i];
        ctxfon.moveTo(gj.x1, gj.y1);
        ctxfon.lineTo(gj.x2, gj.y2);
    }

    ctxfon.stroke();
}

//直线标注
function zhixian(ctx, x1, y1, x2, y2) {
    ctx.strokeStyle = xiantiaose;
    ctx.beginPath();
    ctx.moveTo(x1, y1);
    ctx.lineTo(x2, y2);
    ctx.stroke();
}

//三角形
function sanjiao(ctx, x1, y1, x2, y2, fill) {
    ctx.beginPath();
    ctx.moveTo(x1, y1);
    ctx.lineTo(x2, y2);
    ctx.lineTo(x1 - (x2 - x1), y2);
    ctx.closePath();
    if (fill) {
        ctx.fillStyle = xiantiaose;
        ctx.fill();
    } else {
        ctx.strokeStyle = xiantiaose;
        ctx.stroke();
    }
}

//矩形
function juxing(ctx, x1, y1, x2, y2, fill) {
    ctx.beginPath();
    ctx.moveTo(x1, y1);
    ctx.lineTo(x2, y1);
    ctx.lineTo(x2, y2);
    ctx.lineTo(x1, y2);
    ctx.closePath();
    if (fill) {
        ctx.fillStyle = xiantiaose;
        ctx.fill();
    } else {
        ctx.strokeStyle = xiantiaose;
        ctx.stroke();
    }
}

//圆形标注
function yuanxing(ctx, x, y, radius, fill) {
    ctx.beginPath();
    ctx.arc(x, y, radius, 0, Math.PI * 2);
    if (fill) {
        ctx.fillStyle = xiantiaose;
        ctx.fill();
    } else {
        ctx.strokeStyle = xiantiaose;
        ctx.stroke();
    }
}

//文字
function wenzi(ctx, text, x, y) {
    ctx.beginPath();
    ctx.fillStyle = wenzise;
    ctx.fillText(text, x, y);
}

//单线测量
function danxian(ctx, x1, y1, x2, y2) {
    var cl = celiang(Math.sqrt(Math.abs((x1 - x2) * (x1 - x2)) + Math.abs((y1 - y2) * (y1 - y2))));
    zhixian(ctx, x1, y1, x2, y2);
    ctx.fillStyle = wenzise;
    ctx.fillText(cl, x2, y2);
}

//矩形测量
function juxingceliang(ctx, x1, y1, x2, y2) {
    var clW = celiang(Math.abs(x1 - x2));
    var clH = celiang(Math.abs(y1 - y2));
    juxing(ctx, x1, y1, x2, y2);
    ctx.fillStyle = wenzise;
    ctx.fillText(clW, x1 + (x2 - x1) / 2, y1);
    ctx.fillText(clH, x1, y1 + (y2 - y1) / 2);
}

function celiang(px) {
    if (resolution > 0) {
        return ((px / resolution) * 10).toFixed(2) + 'mm';
    } else {
        return px.toFixed(2) + 'px';
    }
}

//返回
function fanhui() {
    if (record.index > 0) {
        record.index--;
        redraw();
    }
}
//撤销
function chexiao() {
    if (record.index < (record.cache.length - 1)) {
        record.index++;
        redraw();
    }
}

function redraw() {
    var reImg = new Image();
    reImg.onload = function () {
        ctxfon.drawImage(reImg, 0, 0);
    }
    reImg.src = record.cache[record.index];
}

function tipContent() {
    var content = window.prompt('请输入文字', '');
    return content;
}

function tip(ctx, text, x, y) {
    var pop = 24;
    var textMetrics = ctx.measureText(text);
    var minW = Math.ceil(textMetrics.width);
    if (minW < 72) {
        minW = 72;
    }
    var minH = (zh + 10) + pop;

    ctx.beginPath();
    ctx.moveTo(x, y);
    if ((x + minW) <= w) {
        //右
        if ((y - minH) >= 0) {
            //上
            ctx.lineTo(x + pop, y - pop);
            ctx.lineTo(x, y - pop);
            ctx.lineTo(x, y - minH);
            ctx.lineTo(x + minW, y - minH);
            ctx.lineTo(x + minW, y - pop);
            ctx.lineTo(x + pop + 10, y - pop);
            ctx.closePath();
            ctx.fillStyle = biaozhuse;
            ctx.fill();
            ctx.fillStyle = wenzise;
            ctx.fillText(text, x, y - pop - 10);
        } else {
            //下
            ctx.lineTo(x + pop, y + pop);
            ctx.lineTo(x, y + pop);
            ctx.lineTo(x, y + minH);
            ctx.lineTo(x + minW, y + minH);
            ctx.lineTo(x + minW, y + pop);
            ctx.lineTo(x + pop + 10, y + pop);
            ctx.closePath();
            ctx.fillStyle = biaozhuse;
            ctx.fill();
            ctx.fillStyle = wenzise;
            ctx.fillText(text, x, y + minH + 10);
        }
    } else {
        //左
        if ((y - minH) >= 0) {
            //上
            ctx.lineTo(x - pop, y - pop);
            ctx.lineTo(x, y - pop);
            ctx.lineTo(x, y - minH);
            ctx.lineTo(x - minW, y - minH);
            ctx.lineTo(x - minW, y - pop);
            ctx.lineTo(x - pop - 10, y - pop);
            ctx.closePath();
            ctx.fillStyle = biaozhuse;
            ctx.fill();
            ctx.fillStyle = wenzise;
            ctx.fillText(text, x - minW, y - pop - 10);
        } else {
            //下
            ctx.lineTo(x - pop, y + pop);
            ctx.lineTo(x, y + pop);
            ctx.lineTo(x, y + minH);
            ctx.lineTo(x - minW, y + minH);
            ctx.lineTo(x - minW, y + pop);
            ctx.lineTo(x - pop - 10, y + pop);
            ctx.closePath();
            ctx.fillStyle = biaozhuse;
            ctx.fill();
            ctx.fillStyle = wenzise;
            ctx.fillText(text, x - minW, y + minH + 10);
        }
    }
}

//标尺
function rule() {
    if (resolution > 0) {
        var pxMM = Math.floor(resolution / 10);
        var ruleXNum = Math.floor(w / pxMM);

        ctxrule.strokeStyle = 'black';
        ctxrule.beginPath();
        ctxrule.fillText(0, 5, 10);
        ctxrule.moveTo(0, 20);
        ctxrule.lineTo(w, 20);
        var mt = 0;
        for (var i = 1; i <= ruleXNum; i++) {
            if (i % 5 == 0) {
                mt = 10;
                ctxrule.fillText(i / 10, (i * pxMM - (i % 10 == 0 ? 5 : 10)), 10);
            } else {
                mt = 15;
            }

            ctxrule.moveTo(i * pxMM, mt);
            ctxrule.lineTo(i * pxMM, 20);
        }

        var ruleYNum = Math.floor(h / pxMM);

        ctxrule.moveTo(20, 0);
        ctxrule.lineTo(20, h);
        for (var i = 1; i <= ruleYNum; i++) {
            if (i % 5 == 0) {
                mt = 10;
                ctxrule.fillText(i / 10, 0, (i * pxMM + 5));
            } else {
                mt = 15;
            }

            ctxrule.moveTo(mt, i * pxMM);
            ctxrule.lineTo(20, i * pxMM);
        }

        ctxrule.stroke();
    } else {

    }
}

//参考线
function ruleLine(ctx, x, y) {
    ctx.beginPath();
    if (ruleH) {
        ctx.moveTo(0, y);
        ctx.lineTo(w, y);
    } else if (ruleV) {
        ctx.moveTo(x, 0);
        ctx.lineTo(x, h);
    }
    ctx.strokeStyle = '#0cdbf9';
    if (cx > 1) {
        ctx.lineWidth = 1;
    }
    ctx.stroke();
    if (cx > 1) {
        ctx.lineWidth = cx;
    }
}

function ruleLineReDraw() {
    ctxrule.beginPath();
    for (var i = 0; i < ckx.length; i++) {
        if (ckx[i].direction == 'h') {
            ctxrule.moveTo(0, ckx[i].y);
            ctxrule.lineTo(w, ckx[i].y);
        } else {
            ctxrule.moveTo(ckx[i].x, 0);
            ctxrule.lineTo(ckx[i].x, h);
        }
    }
    ctxrule.strokeStyle = '#0cdbf9';
    if (cx > 1) {
        ctxrule.lineWidth = 1;
    }
    ctxrule.stroke();
    if (cx > 1) {
        ctxrule.lineWidth = cx;
    }
}

//保存到本地
function xiazai() {
    canvasfon.toBlob(function (blob) {
        var url = URL.createObjectURL(blob);
        var a = document.createElement("a");
        document.body.appendChild(a);
        a.style.display = 'none';
        a.download = Date.now() + '.png';
        a.href = window.URL.createObjectURL(blob);
        a.click();
        URL.revokeObjectURL(url);
    });
}

function hexToRgb(hex) {
    if (hex.substr(0, 1) == '#') {
        hex = hex.substr(1);
    }

    if (hex.length == 3) {
        return convertHexToRgb(hex + hex.charAt(2) + hex.charAt(1) + hex.charAt(0));
    } else if (hex.length == 6) {
        return convertHexToRgb(hex);
    } else {
        return null;
    }
}

function convertHexToRgb(hex) {
    let r = (parseInt(hex.substring(0, 2), 16)).toFixed(0);
    let g = (parseInt(hex.substring(2, 4), 16)).toFixed(0);
    let b = (parseInt(hex.substring(4, 6), 16)).toFixed(0);

    if (!isNaN(r) && !isNaN(g) && !isNaN(b)) {
        return {
            "r": r,
            "g": g,
            "b": b,
        };
    } else {
        return null;
    }
}