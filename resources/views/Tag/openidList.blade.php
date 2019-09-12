<html>
<head>
    <title>
        标签下用户列表
    </title>
    <body>
        <center>
            <h1>标签下用户列表</h1>
            <table border="1">
                <tr>
                    <td>id</td>
                   <td>openid</td>
                </tr>
                @foreach($openid as $k=>$v)
                <tr>
                    <td>{{$k}}</td>
                    <td>{{$v}}</td>
                </tr>
                @endforeach
            </table>
        </center>
</body>
</head>
</html>