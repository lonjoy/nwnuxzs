<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>教务查询绑定</title>
    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.css">
    <?php
    $openid=$_GET['id'];
    ?>
    <script>
        var base_url = 'http://www.tsdesign.hk/wechat/bind.php';
        //var base_url='http://lnanddj.xicp.net/weixin/cookie.php';//修改
        var openid= '<?php echo $openid;?>';
    </script>
</head>
<body>
<div data-role="page">
    <div class="content">
        <div data-role="content">
            <div data-role="fieldcontain">
                <fieldset data-role="controlgroup">
                    <label for="jwid">您的学号</label>
                    <input id="jwid" placeholder="" value="" type="text" />
                </fieldset>
            </div>
            <div data-role="fieldcontain">
                <fieldset data-role="controlgroup">
                    <label for="jwpwd">身份证号码</label>
                    <input id="person_id" placeholder="" value="" type="text" />
                </fieldset>
            </div>
            <a data-role="button" data-transition="fade" id="bind-btn" data-theme="c">没错，就是点我</a>
            <center><label > Designed By 红领巾小分队<br>©2014 版权所有</label></center>

        </div>
    </div>
</div>
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>
<script>
    $('#bind-btn').on('click',function(){
        $.mobile.showPageLoadingMsg();
        $.ajax({
            url:base_url,
            type:'POST',
            data:'xuehao='+$('#jwid').val()+'&mima='+$('#person_id').val()+'&user='+openid,
            dataType:'json',
            success:function(e){
                $.mobile.hidePageLoadingMsg();
                if(e.msg=='success'){
                    $('.content').html('<div style="margin-top:80px;"><center>绑定成功！ 关闭本页面并回复对应指令获取消息<br>指令：成绩 课表 考试  等级考试 取消绑定</center></div>');
                }else{
                    alert(e.data);
                }
            },
            error:function(e){
                $.mobile.hidePageLoadingMsg();
                alert('绑定失败,请检查网络');
            }
        });
    });
</script>
<script type="text/javascript">
    function onBridgeReady(){
        WeixinJSBridge.call('hideOptionMenu');
    }

    if (typeof WeixinJSBridge == "undefined"){
        if( document.addEventListener ){
            document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
        }else if (document.attachEvent){
            document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
            document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
        }
    }else{
        onBridgeReady();
    }
</script>
</body>
</html>