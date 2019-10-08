<html>
    <head>
        <title>绑定管理员</title>
    </head>
<body>
    <center>
        <h1>绑定管理员账号</h1><br><br>
        <form action="{{url('admin/do_bangding')}}" method="post">
        @csrf
                用户名 <input type="text" name="name"><br><br>
                密码 <input type="text" name="password"><br><br>
                {{--<button>发送验证码</button>--}}
            <br><br>
                <input type="submit" value="绑定管理员账号">

        </form>

    </center>
</body>
</html>