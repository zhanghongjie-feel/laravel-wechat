<html>
<head>
    <title>修改标签</title>
</head>
<body>
<center>


    <form action="{{url('do_update_tag')}}" method="post">
        @csrf
        <input type="hidden" name="id" value="{{$req['id']}}">
        tag <input type="text" name="tag_name" value="{{$req['name']}}">
        <input type="submit" value="update">
    </form>
</center>
</body>
</html>