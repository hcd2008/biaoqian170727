{__NOLAYOUT__}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>跳转提示</title>
    <style type="text/css">
        *{ padding: 0; margin: 0; }
        *{font-size:12px;color:#2B61BA;}

        body{font-family: Verdana, Arial, Helvetica, sans-serif;background:#F0F2F7;margin:0;}
        a:link,a:visited,a:active,.jump {color:#ABBBD6;text-decoration:none;}
        #href{color: #2B61BA;}
        .system-message{  background-color: #ffffff;width:400px;margin:auto;margin-top:10%;}
        .system-message-header{font-weight:bold;padding: 10px 5px;}
        .system-message-content{padding: 5px 5px;line-height:200%;word-break:break-all;border:#7998B7 1px solid;border-top:none;}
        .ml{color:#FFFFFF;  background-color: #1e73b3;padding-left:10px;}
    </style>
</head>
<body>
    <div class="system-message">
        <div class="system-message-header ml">提示信息</div>
        <div class="system-message-content">
        <?php switch ($code) {?>
            <?php case 1:?>
            <p>:) <?php echo(strip_tags($msg));?></p>
            <?php break;?>
            <?php case 0:?>
            <p>:( <?php echo(strip_tags($msg));?></p>
            <?php break;?>
        <?php } ?>
            <p class="jump">页面自动 <a id="href" href="<?php echo($url);?>">跳转</a> 等待时间： <b id="wait"><?php echo($wait);?></b></p>
        </div>
         
    </div>
    <script type="text/javascript">
        var layer_close = <?php echo isset($data['layer_close'])?$data['layer_close']:0; ?>;
        (function(){
            var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
            var interval = setInterval(function(){
                var time = --wait.innerHTML;
                if(time <= 0) {
                    if(layer_close){
                        try{
                            parent.layer.closeAll();
                            return false;
                        }catch(e){}
                        
                    }
                    location.href = href;
                    clearInterval(interval);
                };
            }, 1000);
        })();
    </script>
</body>
</html>
