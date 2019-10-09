<!DOCTYPE html>
<html>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <base href="/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title> - 登录</title>
    <meta name="keywords" content="">
    <meta name="description" content="">

    <link rel="shortcut icon" href="favicon.ico"> <link href="css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="css/font-awesome.css?v=4.4.0" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css?v=4.1.0" rel="stylesheet">
    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;ie.html" />
    <![endif]-->
    <script>if(window.top !== window.self){ window.top.location = window.location;}</script>
</head>

<body class="gray-bg">

<div class="middle-box text-center loginscreen  animated fadeInDown">
    <div>
        <div>

            <h1 class="logo-name">h</h1>

        </div>
        <h3>欢迎使用 hAdmin</h3>
        <img src="{{asset('内网通截图20191007103954.png')}}" alt="">

        <form class="m-t" role="form">
            <div class="form-group">
                <input type="text" id='name' class="form-control" placeholder="用户名" required="">
            </div>
            <div class="form-group">
                <input type="password" id='password' class="form-control" placeholder="密码" required="">
            </div>
            <div class="form-group">
                <input type="code" id="code" class="form-control" placeholder="验证码" required="">
                <button id="code_get" type="submit" class="btn btn-primary block">获取验证码</button>
            </div>

            <br>
            <input type="button" id='a' class="btn btn-primary block full-width m-b" value="login">

            <img src="http://qr.liantu.com/api.php?text=http://wechat.distantplace.vip/admin/openid"/>

            
            <p class="text-muted text-center"> <a href="login.html#"><small>忘记密码了？</small></a> | <a href="register.html">注册一个新账号</a>
            </p>
        </form>
    </div>
</div>


</body>

</html>
<!-- 全局js -->
<script src="{{asset('js/jquery.min.js?v=2.1.4')}}"></script>
<script src="{{asset('js/bootstrap.min.js?v=3.3.6')}}"></script>

<script type="text/javascript">
    $(function(){
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });
    });

    $('#code_get').click(function(){
        var name=$('#name').val();
        var password=$('#password').val();
        // alert(11)
//        var tel=$('#tel').val();
        // alert(cart)
        $.ajax({
            url: "{{url('admin/do_code')}}" ,
            type: 'POST',
            data: {name:name,password:password},
            dataType: 'json',
            success: function(data){
                alert(data.content);
            }
        });
        return false;
    });

    $('#a').click(function(){
        alert(123);die;
        var code=$('#code').val();
        $.ajax({
            url: "{{url('admin/send_code')}}" ,
            type: 'POST',
            data: {code:code},
            dataType: 'json',
            success: function(data){
                alert(data.content);
            }
        });
        return false;
    })
</script>
