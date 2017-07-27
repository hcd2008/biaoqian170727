var toggle = ['buguize', 'zhixian', 'juxing', 'yuanxing', 'danxian', 'juxingceliang', 'tuodong', 'wenzi', 'cankaoxian', 'fangdajing', 'quse'];

function reStatus() {
    if (cur == 'tuodong') {
        board.removeEventListener('mousedown', beginDrag);
        document.removeEventListener('mouseup', endDrag);
        board.removeEventListener('mousemove', draging);
    }

    if (cur == 'fangdajing' || cur == 'quse') {
        ctxcur.clearRect(0, 0, w, h);
    }

    for (var i = 0; i < toggle.length; i++) {
        document.getElementById(toggle[i]).style.color = 'black';
    }
}

var trigger = ['fangda', 'suoxiao', 'huanyuan', 'shunshizhen', 'nishizhen', 'fanhui', 'chexiao', 'cankaoxian_hide'];

for (var i = 0; i < trigger.length; i++) {
    var el = document.getElementById(trigger[i]);
    el.onmousedown = function () {
        this.style.color = 'red';
    }

    el.onmouseup = function () {
        this.style.color = 'black';
    }
}

//放大镜
var fangdajing = document.getElementById('fangdajing');
fangdajing.onclick = function () {
    reStatus();
    this.style.color = 'red';
    cur = 'fangdajing';
}

//取色器
var quse = document.getElementById('quse');
quse.onclick = function () {
    reStatus();
    this.style.color = 'red';
    cur = 'quse';
}

//不规则标注
var buguize = document.getElementById('buguize');
buguize.onclick = function () {
    reStatus();
    this.style.color = 'red';
    canvascur.style.cursor = 'crosshair';
    cur = 'buguize';
}

//直线标注
var zhixian = document.getElementById('zhixian');
zhixian.onclick = function () {
    reStatus();
    this.style.color = 'red';
    canvascur.style.cursor = 'crosshair';
    cur = 'zhixian';
}

//矩形标注
var juxing = document.getElementById('juxing');
juxing.onclick = function () {
    reStatus();
    this.style.color = 'red';
    canvascur.style.cursor = 'crosshair';
    cur = 'juxing';
}

//圆形标注
var yuanxing = document.getElementById('yuanxing');
yuanxing.onclick = function () {
    reStatus();
    this.style.color = 'red';
    canvascur.style.cursor = 'crosshair';
    cur = 'yuanxing';
}

//文字
var wenzi = document.getElementById('wenzi');
wenzi.onclick = function () {
    reStatus();
    this.style.color = 'red';
    canvascur.style.cursor = 'text';
    cur = 'wenzi';
}

//单线测量
var danxian = document.getElementById('danxian');
danxian.onclick = function () {
    reStatus();
    this.style.color = 'red';
    canvascur.style.cursor = 'crosshair';
    cur = 'danxian';
}

//矩形测量
var juxingceliang = document.getElementById('juxingceliang');
juxingceliang.onclick = function () {
    reStatus();
    this.style.color = 'red';
    canvascur.style.cursor = 'crosshair';
    cur = 'juxingceliang';
}

var drag = false;
var dragX = 0;
var dragY = 0;
//拖动
var tuodong = document.getElementById('tuodong');
tuodong.onclick = function () {
    reStatus();
    this.style.color = 'red';
    cur = 'tuodong';

    board.addEventListener('mousedown', beginDrag);
    document.addEventListener('mouseup', endDrag);
    board.addEventListener('mousemove', draging);
}

function beginDrag(e) {
    if (cur == 'tuodong') {
        drag = true;

        dragX = e.clientX - this.offsetLeft;
        dragY = e.clientY - this.offsetTop;
    }
}

function draging(e) {
    if (cur == 'tuodong' && drag) {
        var curLeft = e.clientX - dragX;
        var curTop = e.clientY - dragY - 100;

        board.style.left = curLeft + 'px';
        board.style.top = curTop + 'px';
    }
}

function endDrag(e) {
    if (cur == 'tuodong') {
        drag = false;
    }
}

//放大
var fangda = document.getElementById('fangda');
fangda.onclick = function () {
    suofang('fangda');
    fixPostion();
}

//缩小
var suoxiao = document.getElementById('suoxiao');
suoxiao.onclick = function () {
    suofang('suoxiao');
    fixPostion();
}

//还原
var huanyuan = document.getElementById('huanyuan');
huanyuan.onclick = function () {
    suofang('huanyuan');
    fixPostion();
}

var rotate = 0;

function fixPostion() {
    var fixLeft = 0;
    var fixTop = 0;
    var hs = (rotate / rotateStep) % 2;
    var absScale = Math.abs(scale);

    if (hs == 0) {
        //横
        fixLeft -= w * (1 - absScale) / 2;
        fixTop -= h * (1 - absScale) / 2;
    } else {
        //竖
        fixLeft -= (w - h * scale) / 2;
        fixTop -= (h - w * scale) / 2;
    }

    board.style.left = fixLeft + 'px';
    board.style.top = fixTop + 'px';
}

//顺时针
var shunshizhen = document.getElementById('shunshizhen');
shunshizhen.onclick = function () {
    rotate += rotateStep;

    board.style.transform = 'scale(' + scale + ') rotate(' + rotate + 'deg)';
    fixPostion();
}

//逆时针
var nishizhen = document.getElementById('nishizhen');
nishizhen.onclick = function () {
    rotate -= rotateStep;

    board.style.transform = 'scale(' + scale + ') rotate(' + rotate + 'deg)';
    fixPostion();
}

//粗细
document.getElementById('cuxi').onchange = function () {
    cx = this.value;
    ctxfon.lineWidth = cx;
    ctxcur.lineWidth = cx;
}

//形状
document.getElementById('xingzhuang').onchange = function () {
    if (this.value) {
        reStatus();
        cur = this.value;
        canvascur.style.cursor = 'crosshair';
    }
}

//线条颜色
document.getElementById('xiantiaose').onchange = function () {
    xiantiaose = this.value;
}

//标注背景色
document.getElementById('biaozhuse').onchange = function () {
    var rgb = hexToRgb(this.value);
    if (rgb) {
        biaozhuse = 'rgba(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ', ' + alpha + ')';
    } else {
        biaozhuse = this.value;
    }
}

//文字颜色
document.getElementById('wenzise').onchange = function () {
    wenzise = this.value;
}

//字号
document.getElementById('zihao').onchange = function () {
    zh = parseInt(this.value);
    ctxfon.font = zh + 'px serif';
}

//返回
var fanhui = document.getElementById('fanhui');
fanhui.onclick = function () {
    fanhui();
}

//撤销
var chexiao = document.getElementById('chexiao');
chexiao.onclick = function () {
    chexiao();
}

//标尺
var ruleShow = false;
document.getElementById('biaochi').onclick = function () {
    if (ruleShow) {
        this.style.color = 'black';
        // ctxrule.clearRect(0, 0, w, h);

        // if (ruleLineShow) {
        //     ruleLineReDraw();
        // }

        canvasrulex.style.display = 'none';
        canvasruley.style.display = 'none';
    } else {
        this.style.color = 'red';
        // rule();

        canvasrulex.style.display = 'block';
        canvasruley.style.display = 'block';
    }

    ruleShow = !ruleShow;
}
//参考线
var ruleLineShow = false;
var cankaoxian = document.getElementById('cankaoxian');
cankaoxian.onclick = function () {
    reStatus();
    this.style.color = 'red';
    cur = 'cankaoxian';

    if (!ruleLineShow) {
        ruleLineShow = true;
        ruleLineReDraw();
    }
}

//隐藏参考线
document.getElementById('cankaoxian_hide').onclick = function () {
    if (ruleLineShow) {
        ctxrule.clearRect(0, 0, w, h);

        if (ruleShow) {
            rule();
        }

        ruleLineShow = false;
        if (cur == 'cankaoxian') {
            cur = '';
            cankaoxian.style.color = 'black';
        }
    }
}

//保存到本地
document.getElementById('xiazai').onclick = function () {
    xiazai();
}

window.onkeypress = function (e) {
    if (e.key == '=') {
        fangda.click();
    } else if (e.key == '-') {
        suoxiao.click();
    } else if (e.key == '[') {
        fanhui();
    } else if (e.key == ']') {
        chexiao();
    }
}

window.onscroll = function (e) {

}