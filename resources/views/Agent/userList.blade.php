<html>
<head>
    <title>用户列表</title>
</head>
<body>
    <center>
        <table border="1">
            <tr>
                <td>id</td>
                <td>name</td>
                <td>推广码</td>
                <td>码</td>
                <td>操作</td>
            </tr>
            @foreach($info as $k=>$v)
                <tr>
                    <td>{{$info[$k]->id}}</td>
                    <td>{{$info[$k]->name}}</td>
                    <td>{{$info[$k]->id}}</td>
                    <td><img src="{{asset($v->qrcode_url)}}"  height=150 alt=""></td>
                    <td><a href="{{url('agent/create_qrcode')}}?uid={{$v->id}}">生成专属二维码</a></td>
                </tr>
            @endforeach
        </table>
    </center>
</body>
</html>