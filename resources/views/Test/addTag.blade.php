<html>
<head>
    <title>添加标签</title>
</head>
<body>
<center>
    <form action="{{url('test/do_add_tag')}}" method="post">
        @csrf
        标签名称:<input type="text" name="tag_name"><br><br><br>
        <input type="submit" value="created">
    </form>
</center>
</body>
</html>