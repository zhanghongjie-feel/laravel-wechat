<!doctype html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <center>
        <table border="1">
            <tr>
                <td>id</td>
                <td>用户昵称</td>
                <td>用户openid</td>
                <td>是否关注</td>
                <td>城市</td>
                <td>国家</td>
                <td>头像</td>
                <td>操作</td>
            </tr>
            @foreach($info as $k=>$v)
                <tr>
                    <td>{{$k}}</td>
                    <td>{{$v['nickname']}}</td>
                    <td>{{$v['openid']}}</td>
                    <td>{{$v['city']}}</td>
                    <td>{{$v['subscribe']}}</td>
                    <td>{{$v['country']}}</td>
                    <td><img src="{{asset($v['headimgurl'])}}" alt=""></td>
                    <td>{{date('Y-m-d H:i:s',$v['subscribe_time'])}}</td>
                    <td class="a">
                        <a href="{{url('wechat/get_detailed_info')}}?id={{$k}}">详情</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </center>

</body>
</html>