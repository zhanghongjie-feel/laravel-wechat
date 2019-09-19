<html>
    <head>
        <title>菜单列表</title>
    </head>
<body>
    <center>
        <h2>创建菜单</h2>
        <form action="{{url('menu/create')}}" method="post">
            @csrf
            一级菜单：<input type="text" name="name1"><br><br>
            二级菜单：<input type="text" name="name2"><br><br>
            菜单类型 [click/view]
            <select name="type" id="">
                <option value="1">click</option>
                <option value="2">view</option>
            </select><br><br>
            事件值[key/url]
            <input type="text" name="event_value">
            <br><br>
            <input type="submit" value="提交">
        </form>
        <br><br><br>
        <h2>菜单列表</h2>
        {{--{{$info}}--}}
        <table border="1">
            <tr>
                <td>name1</td>
                <td>name2</td>
                <td>操作</td>
            </tr>
            @foreach($info as $k=>$v)
                <tr>
                    <td>{{$v->name1}}</td>
                    <td>{{$v->name2}}</td>
                    <td><a href="{{url('menu/del')}}?id={{$v->id}}">删除</a></td>
                </tr>
            @endforeach
        </table>
    </center>
</body>
</html>