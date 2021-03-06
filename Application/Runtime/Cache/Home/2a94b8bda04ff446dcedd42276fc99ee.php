<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>BiuBiu聊天室</title>
<base href="/day11/day3--chat/Public/">
<link rel="shortcut icon" href="favicon.png">
<link rel="icon" href="favicon.png" type="image/x-icon">
<link type="text/css" rel="stylesheet" href="css/style.css">
<link rel="stylesheet" type="text/css" href="layui/css/layui.css">
<script type="text/javascript" src="js/jquery.min.js"></script>
<script src="layui/layui.js" charset="utf-8"></script>
</head>
<body>
<div class="chatbox">
  <div class="chat_top fn-clear">
    <div class="logo"><img src="images/logo.png" width="190" height="60"  alt=""/></div>
    <div class="uinfo fn-clear">
      <div class="uface"><img src="images/hetu.jpg" width="40" height="40"  alt=""/></div>
      <div class="uname"><span id="user_name" uid="<?php echo ($uid); ?>"><?php echo ($name); ?></span>
        <i class="fontico down"></i>
        <ul class="managerbox">
          <li><a href="#"><i class="fontico lock"></i>修改密码</a></li>
          <li><a href="<?php echo U('Index/autologin');?>"><i class="fontico logout"></i>退出登录</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="chat_message fn-clear">
    <div class="chat_left">
      <div class="message_box" id="message_box">
      </div>
      <div class="write_box">
        <textarea class="layui-textarea" id="LAY_demo1" style="display: none" placeholder="说点啥吧..."></textarea>
        <input type="hidden" name="fromname" id="fromname" value="<?php echo ($name); ?>" />
        <input type="hidden" name="to_uid" id="to_uid" value="0">
        <div class="facebox fn-clear">
          <div class="expression"></div>
          <div class="chat_type" id="chat_type">群聊</div>
          <button name="" class="sub_but">提 交</button>
        </div>
      </div>
    </div>
    <div class="chat_right">
      <ul class="user_list" title="双击用户私聊">
        <li class="fn-clear" style="pointer-events:none;"><em>当前在线好友<i class="layui-icon">&#xe613;</i></em><span class="layui-badge" style="width: 20px; height: 20px" id="online_num"></span></li>
        <!--//所有用户的列表-->
        <li class="fn-clear selected" data-id="0"><em>所有用户</em></li>
        <?php if(is_array($mo_user)): foreach($mo_user as $key=>$vo): if(($vo['state'] == 1)): ?><li class="fn-clear userlist" data-id="<?php echo ($vo["uid"]); ?>" data-name="<?php echo ($vo["name"]); ?>"><span><img src="images/53f442834079a.jpg" width="30" height="30"  alt=""/></span><em><?php echo ($vo["name"]); ?></em><small class="online" title="在线"></small><i class="layui-icon" id="friend" style="font-size: 25px; color: #1E9FFF;">&#xe608;</i></li>
          <?php else: ?>
            <li class="fn-clear userlist" data-id="<?php echo ($vo["uid"]); ?>" data-name="<?php echo ($vo["name"]); ?>"><span><img src="images/53f442834079a.jpg" width="30" height="30"  alt=""/></span><em><?php echo ($vo["name"]); ?></em><small class="offline" title="离线"></small><i class="layui-icon" id="friend" style="font-size: 25px; color: #1E9FFF;">&#xe608;</i></li><?php endif; endforeach; endif; ?>
        <!--我的好友列表-->
        <li class="fn-clear" style="pointer-events:none;background-color: #c7ddef"><em>我的好友</em></li>
        <?php if(is_array($message)): foreach($message as $key=>$vo): if(($vo['state'] == 1)): ?><li class="fn-clear userlist" data-id="<?php echo ($vo["uid"]); ?>" data-name="<?php echo ($vo["name"]); ?>"><span><img src="images/53f442834079a.jpg" width="30" height="30"  alt=""/></span><em><?php echo ($vo["name"]); ?></em><small class="online" title="在线"></small></li>
            <?php else: ?>
            <li class="fn-clear userlist" data-id="<?php echo ($vo["uid"]); ?>" data-name="<?php echo ($vo["name"]); ?>"><span><img src="images/53f442834079a.jpg" width="30" height="30"  alt=""/></span><em><?php echo ($vo["name"]); ?></em><small class="offline" title="离线"></small></li><?php endif; endforeach; endif; ?>
      </ul>
    </div>
  </div>
</div>
<script type="text/javascript">
var ws;
$(document).ready(function() {
    //连接websocket
    ws = new WebSocket("ws://" + document.domain + ":7272");
    // 当socket连接打开时，输入用户名
    ws.onopen = onopen;
    // 当有消息时根据消息类型显示不同信息
    ws.onmessage = onmessage;
    ws.onclose = function () {
        console.log("连接关闭，定时重连");
    };
    ws.onerror = function () {
        console.log("出现错误");
    }
});
$('#message_box').scrollTop($("#message_box")[0].scrollHeight + 20);
//下拉列表
$('.uname').hover(
    function(){
        $('.managerbox').stop(true, true).slideDown(100);
    },
    function(){
        $('.managerbox').stop(true, true).slideUp(100);
    }
);
var to_uid   = 0; // 默认为0,表示发送给所有用户
var to_uname = '所有用户';
//双击用户名发送私聊
$(document).on("dblclick",".user_list > li",function(){
    var fromname = $('#fromname').val();//获取当前用户的名称
    to_uname = $(this).find('em').text();//获取接收人的用户名
    to_uid   = $(this).attr('data-id');//获取接收人的client_id
    if(to_uname == fromname){
		alert('您不能和自己聊天!');
		return false;
	}
    if(to_uname == "所有用户"){
		$("#toname").val('');
		$('#chat_type').text('群聊');
	}else{
		    $("#toname").val(to_uid);
			$('#chat_type').text('您正和 ' + to_uname + ' 聊天');
	}
    $(this).addClass('selected').siblings().removeClass('selected');
    $('#message').focus().attr("placeholder", "您对"+to_uname+"说：");
});
//发送消息
$('.sub_but').click(function(event){
    sendMessage(event,to_uid,to_uname);
});
//向服务端发送消息
function sendMessage(event,to_uid,to_uname){
    //to_uid="接收人的client_id"; //to_uname="接收人的姓名";
    var fromname = $('#fromname').val();//获取当前用户的名称
    var msg = $(document.getElementById('LAY_layedit_1').contentWindow.document.body).html();//获取将要发送的信息
    var mydate = new Date();
    var timeStr = mydate.toLocaleString( ); //获取日期与时间
    var message = {"type":"chat","content":""+msg+"","to_uid":""+to_uid+"","time":""+timeStr+"","to_uname":""+to_uname+"","fromname":""+fromname+""};
    //将JSON.stringify() 方法用于将 JavaScript 值转换为 JSON 字符串。
    console.log(message);
    var new_message = JSON.stringify(message);
    //发送信息到服务端
    ws.send(new_message,true);
    //发送完毕清空文本域
    $(document.getElementById('LAY_layedit_1').contentWindow.document.body).html("");

}
layui.use('layedit', function(){
    var layedit = layui.layedit
        ,$ = layui.jquery;
    //构建一个默认的编辑器
    $('.site-demo-layedit').on('click', function(){
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });
    layedit.set({
        uploadImage: {
            url: "<?php echo U('Index/upload');?>" //接口url
            ,type: 'post'
        }
    });
    //自定义工具栏
    var index =   layedit.build('LAY_demo1', {
        tool: ['face','image']
        ,height: 100
    })
});
/*按下按钮或键盘按键*/
$("#message").keydown(function(event){
    var e = window.event || event;
    var k = e.keyCode || e.which || e.charCode;
    //按下ctrl+enter发送消息
    if((event.ctrlKey && (k == 13 || k == 10) )){
        sendMessage(event,to_uid,to_uname);
    }
});
//发送登录信息
function onopen() {
    console.log("链接握手成功");
    //获取登录的用户名，将该用户上线的消息发送给该房间的所有人
    var user_name = $("#user_name").text();
    var message = '{"type":"login","name":"'+user_name+'"}';
    ws.send(message);
}
//接受服务端发送的消息
function onmessage(e) {
    console.log(e.data);
    var data = eval('('+e.data+')');
    //接收服务端发来的登录信息
    if(data.type == 'login'){
        var name = data.name;//登录聊天室的名字
        var num = data.onlineNum;//获取房间的人数
        var group_client_id = data.group_client_id;//登录聊天室的cliend_id在线的信息
        $("#message_box").append("<center><p id='welcome'>欢迎"+name+"加入聊天室</p></center>");
        $("#online_num").text(num);//更新当前在线的人数
        refreshUserlist(group_client_id);//调用刷新好友列表的方法
    }
    //接收服务端发来的聊天信息
    if(data.type == "chat"){
        console.log(data.content);
        var to_uname = data.to_uname;//接收人的用户名
        var  fromname =data.fromname;//发送人的姓名
        if(to_uname == "所有用户"){
            to_uname ="";
        }
        var msg = data.content;//内容
        var time = data.time;//发送的时间
        if(to_uname != ''){
	    msg = fromname+'对 ' + to_uname + ' 说： ' + msg;
	   }
          var htmlData =   '<div class="msg_item fn-clear">'
                         + '   <div class="uface"><img src="images/hetu.jpg" width="40" height="40"  alt=""/></div>'
                         + '   <div class="item_right">'
                         + '     <div class="msg own">' + msg + '</div>'
                         + '     <div class="name_time">' + fromname + ' · '+time+'</div>'
                         + '   </div>'
                         + '</div>';
        $("#message_box").append(htmlData);
        $('#message_box').scrollTop($("#message_box")[0].scrollHeight + 20);
    }
    //接收服务端发来的断开服务端操作的信息
    if(data.type == 'singout'){
        var name = data.name;//当前退出聊天室的名字
        var num = data.num;//当前在线的人数
        var group_client_id = data.group_client_id;//登录聊天室的cliend_id在线的信息
        $("#message_box").append("<center><p id='welcome'>"+name+"退出聊天室</p></center>");
        $("#online_num").text(num);//更新当前在线的人数
        refreshUserlist(group_client_id);//调用刷新好友列表的方法
    }
    //判定参数
    if(data.type == "buid"){
        var cliend = data.cliend;
        $.post("<?php echo U('Index/buid');?>", {cliend: ""+cliend+""});
    }
    //接收服务端发来的加好友申请
    if(data.type == "Apply"){
        var info = data.info;//接收来之加好友的信息
        var to_uid = data.from_uid;//来自哪个好友的id
        var from_uid =data.to_uid;//本人的id
        layer.open({
            title: '好友请求'
            ,content: info
            ,btn: ['同意请求','拒绝']
            ,btn1:function(index,layero){
              //同意后将好友信息入库并返回好友添加成功的信息通知好友
                $.post("<?php echo U('Index/applefrined');?>",{"from_uid":from_uid,"to_uid":+to_uid},
                    function(data){
                        var data = $.parseJSON(data);//将json串转化成json对象
                        if(data.code == "1"){
                            var from_uid = data.to_uid;//即将添加好友的值
                            var to_uid = data.from_uid;//返回给该好友的id
                            //添加好友成功
                            var message = '{"from_uid":"'+from_uid+'","to_uid":"'+to_uid+'","type":"YesApply"}';
                            ws.send(message);
                            alert("添加好友成功后,请按ctrl+F5刷新用户列表");
                        }
                    });
                layer.close(index);
            }
            ,btn2:function(index,layero){
               //拒绝好友申请后，返回给好友的拒绝信息
                var message = '{"from_uid":"'+from_uid+'","to_uid":"'+to_uid+'","type":"NoApply"}';
                ws.send(message);
                layer.close(index);
            }
        });
    }
    //来自好友的同意申请
    if(data.type == "YesApply"){
        var from_uid = data.from_uid;//来之好友的id
        var to_uid = data.to_uid;//发送给好友的id
        layer.open({
            title: '消息通知'
            , content: "对方已同意您的好友请求"
            , btn: ['确定']
            ,btn1:function(index,layero){
                $.post("<?php echo U('Index/friends');?>",{"from_uid":from_uid,"to_uid":+to_uid},function(data){
                    var data = $.parseJSON(data);//将json串转化成json对象
                    if(data.code == "1"){
                        //刷新好友列表
                        alert("添加好友成功后,请按ctrl+F5刷新用户列表");
                    }
                });
                layer.close(index);
            }
        })
    }
    //来自对方好友的拒绝申请
    if(data.type == "NoApply"){
        layer.open({
            title:'消息通知'
            ,content:"对方拒绝了您的好友申请"
            ,btn:['确定']
            ,btn1:function(index,layero){
                layer.close(index);//关闭该弹框
            }
        })
    }
};
//添加好友
$(document).on("click","#friend",function(){
    var id = $(this).parent().attr("data-id");//好友的id
    var name = $(this).parent().text();//申请的好友
    var to_uid =$("#user_name").attr("uid");//获取当前用户的id
    //获取当前好友的状态
    var friend_state = $(this).prev().attr("title");
    if(id == to_uid){
        alert("自己不能添加自己为好友奥");
        return;
    }
    //离线好友暂不支持加好友
    if(friend_state == '离线'){
        alert("抱歉,该好友暂时离线无法添加对方为好友");
        return;
    }
    //发送id信息申请添加好友请求
    var str= '<div class="layim-add-box"><div class="layim-add-img">' +
        ' <p>'+name+'</p></div><div class="layim-add-remark"><textarea user_id='+id+' to_uid='+to_uid+' id="LAY_layimRemark" placeholder="验证信息" class="layui-textarea"></textarea>' +
        '</div></div>';
    layer.open({
        title:'申请好友'
        ,content:str  //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
        ,btn: ['发送申请','关闭']
        ,btn1:function(index,layero){
            var info = $("#LAY_layimRemark").val();//获取加好友的信息
            var user_id =$("#LAY_layimRemark").attr("user_id");//获取该好友的uid
            var to_uid = $("#LAY_layimRemark").attr("to_uid");
            var message = '{"type":"Apply","to_uid":"'+user_id+'","info":"'+info+'","from_uid":"'+to_uid+'"}';
            console.log(message);
            //将好友的申请信息发送给好友
            ws.send(message);
            layer.close(index);
        }
   });
});
//刷新好友列表
function refreshUserlist(group_client_id)
{
    var str = "";
    for(key in group_client_id){
        str +=','+group_client_id[key]['name'];
    }
    var s_str = str.substring(1);//把前面的,号去掉
    //定义一个新的数组
    var online_array = new Array();
    online_array = s_str.split(",");
    //获取好友列表的好友
    $(".userlist").each(function(){
        var inar = jQuery.inArray($(this).attr("data-name"),online_array);
         if(inar != -1){
             //如果当前用户不在线添加不在线的样式
              $(this).children().eq(2).attr('class','online');
             $(this).children().eq(2).attr('title','在线');
         }else{
             $(this).children().eq(2).attr('class','offline');
             $(this).children().eq(2).attr('title','离线');
         }
    })
}
</script>
</body>
</html>