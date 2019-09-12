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
        <form action="{{url('wechat/add_tag_openid')}}" method="post">
            @csrf
            <input type="submit" value="提交">
            <br><br>
            <input type="hidden" value="{{$tagid}}" name="tagid">
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
                        <td><input type="checkbox" name="openid_list[]" id="" value="{{$v->openid}}"></td>
                        <td>{{$info[$k]->id}}</td>
                        <td>{{$info[$k]->nickname}}</td>
                        <td>{{$info[$k]->openid}}</td>
                        <td>{{$info[$k]->city}}</td>
                        <td>{{$info[$k]->subscribe}}</td>
                        <td>{{$info[$k]->country}}</td>
                        <td><img src="{{asset($info[$k]->headimgurl)}}" alt=""></td>
                        <td>{{date('Y-m-d H:i:s',$info[$k]->subscribe_time)}}</td>
                        {{--<td class="a">--}}
                        {{--<a href="{{url('wechat/get_detailed_info')}}?id={{$k}}">详情</a>--}}
                        {{--</td>--}}
                    </tr>
                @endforeach
            </table>
        </form>

    </center>

</body>
</html>