<html>
    <head>
        <title>粉丝列表</title>
    </head>
<body>
   <center>
       {{$openid}}
       <form action="{{url('exam/do_liuyan')}}" method="post">
           @csrf
           <input type="submit" value="提交openid">
           <input type="hidden" name="content" value="{{$openid}}">
           <br><br>

           <table border="1">
               <tr>
                   <td>id</td>
                   <td>openid</td>
                   <td>粉丝</td>
               </tr>
               {{--@foreach($info as $k=>$v)--}}
                   {{--<tr>--}}
                       {{--<td><input type="checkbox" name="openid_list[]" id="" value="{{$v->openid}}"></td>--}}
                       {{--<td>{{$v->id}}</td>--}}
                       {{--<td>{{$v->openid}}</td>--}}
                       {{--<td><img src="{{asset($v->headimgurl)}}" alt="">--}}
                           {{--{{$v->nickname}}--}}
                       {{--</td>--}}
                   {{--</tr>--}}
               {{--@endforeach--}}
           </table>
       </form>
   </center>
</body>
</html>