<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <base href="/day11/day3--chat/Public/">
    <title>BiuBiu在线聊天室</title>
    <link href="css/style_log.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="css/style1.css">
    <link rel="stylesheet" type="text/css" href="css/userpanel.css">
    <link rel="stylesheet" type="text/css" href="css/jquery.ui.all.css">
    <style>
        .login_boder{ background: url("images/login_m_bg.png") no-repeat; height:302px; overflow:hidden;}
        .rem_sub input.sub_button{ float:right; width:135px; height:32px; background:url("images/site_bg.png") no-repeat -153px -850px; border:none; color:#FFF; padding-bottom:2px; font-size:14px; font-weight:bold;}
    </style>
</head>
<body class="login1" mycollectionplug="bind" style="background-image: url("images/login_bgx.gif")">
<div class="login_m">
    <form action="<?php echo U('Index/login');?>" method="post">
    <div class="login_logo"><img src="images/logo1.png" width="196" height="46"></div>
    <div class="login_boder">
        <div class="login_padding" id="login_model">
            <h2>登录名</h2>
            <label>
                <input type="text" id="username" name="username" class="txt_input txt_input2" onfocus="if (value ==&#39;Your name&#39;){value =&#39;&#39;}" onblur="if (value ==&#39;&#39;){value=&#39;Your name&#39;}" value="Your name">
            </label>
            <h2>登录密码</h2>
            <label>
                <input type="password" name="password" id="userpwd" class="txt_input" onfocus="if (value ==&#39;******&#39;){value =&#39;&#39;}" onblur="if (value ==&#39;&#39;){value=&#39;******&#39;}" value="******">
            </label>
            <p class="forgot"><a id="iforget" href="javascript:void(0);">Forgot your password?</a></p>
        </div>
        <div class="rem_sub">
            <div class="rem_sub_l">
            </div>
            <label>
                <input type="submit" class="sub_button" name="button" id="button" value="登录" style="opacity: 0.9;">
            </label>
        </div>
    </div>
    </form>
</div>
</body>
</html>
<script src="layui/layui.js"></script>
<script src="js/jquery.min.js"></script>
<script>
    layui.use('layer', function(){ //独立版的layer无需执行这一句
        var str='<div style="padding: 50px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;">欢迎亲使用本聊天室<br>本人博客：<a href="http://blog.csdn.net/z1040657944" target="_blank">http://blog.csdn.net/z1040657944</a><br><br>BiuBiu聊天室采用layui的弹出层+workerman框架开发,有些功能不太完善，后期会添加功能。<br><br>如果你在体验中发现有些功能可以改进,希望能将建议发送至1040657944@qq.com备注BiuBiu<br><br>体验账号1：demo<br>密码123123；<br>体验账号2：demo2<br>密码：123123</div>';
        layer.open({
            type: 1
            ,title: 'BiuBiu聊天室公告:' //不显示标题栏
            ,closeBtn: false
            ,area: '300px;'
            ,shade: 0.8
            ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
            ,resize: false
            ,btn: ['知道了', '关闭']
            ,btnAlign: 'c'
            ,moveType: 1 //拖拽模式，0或者1
            ,content: str
            ,success: function(layero){

            }
        });
    })
</script>