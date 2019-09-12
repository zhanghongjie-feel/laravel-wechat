<html>
    <head>
        <title>标签管理</title>
    </head>
<body>
    <center>
        <h1>公众号标签管理</h1><br><br><br>
        <a href="{{url('add_tag')}}">添加标签</a><br><br>
        <table border="1">
            <tr>
                <td>tag_id</td>
                <td>tag_name</td>
                <td>标签下粉丝数</td>
                <td>操作</td>
            </tr>

                @foreach($info['tags'] as $v)
                <tr>
                    <td>{{$v['id']}}</td>
                    <td>{{$v['name']}}</td>
                    <td>{{$v['count']}}</td>
                    <td>
                        <a href="{{url('delete_tag')}}?id={{$v['id']}}">删除</a> | <a href="{{url('update_tag')}}?id={{$v['id']}}&name={{$v['name']}}">修改</a> |
                        <a href="{{url('wechat/tag_openid_list')}}?tagid={{$v['id']}}">粉丝列表</a> |
                        <a href="{{url('wechat/get_user_list')}}?tagid={{$v['id']}}">给标签加他个粉丝</a> |
                        <a href="{{url('wechat/push_tag_message')}}?tagid={{$v['id']}}">推送消息</a>
                    </td>
                </tr>
                @endforeach

        </table>

    </center>
</body>
</html>