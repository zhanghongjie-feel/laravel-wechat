<html>
    <body>
        <center>
            {{--<img src="{{asset('/storage/goods/H6h8OWQVmMgbWM0mExpDj3ieUCWWNZXIsFQ4vACB.jpeg')}}" alt="">--}}
            <form action="{{url('wechat/do_upload')}}" method="post" enctype="multipart/form-data">
            {{--<form action="https://api.weixin.qq.com/cgi-bin/media/upload?access_token={{$token}}&type=image" method="post" enctype="multipart/form-data">--}}
            @csrf
                <input type="file" name="image" id="">
                <input type="submit" value="提交">

            </form>
        </center>
    </body>

</html>