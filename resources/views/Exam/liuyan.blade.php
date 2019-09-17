<html>
    <head>
        <title>
            留言
        </title>
    </head>
<body>
   <center>
       <h3>留言内容</h3>
       <form action="{{url('exam/openid_list')}}" method="post">
           @csrf
           <textarea name="content" id="" cols="30" rows="10"></textarea><br>
           <input type="submit" value="提交">
       </form>
   </center>
</body>
</html>