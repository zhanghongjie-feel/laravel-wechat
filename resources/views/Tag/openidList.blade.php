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
                    <td>粉丝
                    </td>
                   <td>openid</td>
                </tr>
                @foreach($info as $k=>$v)
                <tr>
                    <td>{{$info[$k]->id}}</td>
                    <td>{{$info[$k]->nickname}}
                        <img src="{{asset($info[$k]->headimgurl)}}" alt="">
                    </td>
                    <td>{{$info[$k]->openid}}</td>
                </tr>
                @endforeach
            </table>
        </center>
</body>
</head>
</html>