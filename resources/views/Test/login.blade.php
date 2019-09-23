<html>
    <head>
        <title>登录</title>
    </head>
<body>
    <center>
        <form action="">
            <input id='wechat_btn' type="button" value="来！微信网页授权">
        </form>
    </center>
</body>
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script>
        $(function(){
            $('#wechat_btn').click(function(){
                window.location.href='{{url('/test/wechat_login')}}';
            })
        })
    </script>
</html>