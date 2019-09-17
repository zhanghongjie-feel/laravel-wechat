<html>
<head>
    <title>登录</title>
</head>
<body>
<center>
    <h1>登录</h1>
    用户名：<input type="text"><br>
    密码：<input type="password" name="" id=""><br>
    第三方登录 <button id="wechat_btn">微信授权登录</button>
</center>
<script src="{{asset('js/jquery.min.js')}}"></script>

<script>
    $(function(){
        $('#wechat_btn').click(function(){
            window.location.href='{{url('/exam/wechat_login')}}';
        })
    })
</script>
</body>
</html>