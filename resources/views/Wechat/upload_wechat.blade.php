<html>
    <body>
    <center>

        {{--<img src="{{asset('/storage/goods/H6h8OWQVmMgbWM0mExpDj3ieUCWWNZXIsFQ4vACB.jpeg')}}" alt="">--}}
        <form action="{{url('wechat/do_upload_wechat_image')}}" method="post" enctype="multipart/form-data">
            {{--<form action="https://api.weixin.qq.com/cgi-bin/media/upload?access_token={{$token}}&type=image" method="post" enctype="multipart/form-data">--}}
            @csrf
            <h3>image</h3>
            <input type="file" name="file_name" id="">
            <input type="submit" value="提交">

        </form>
        <form action="{{url('wechat/do_upload_wechat_video')}}" method="post" enctype="multipart/form-data">
            {{--<form action="https://api.weixin.qq.com/cgi-bin/media/upload?access_token={{$token}}&type=image" method="post" enctype="multipart/form-data">--}}
            @csrf
            <h3>video</h3>
            <input type="file" name="file_name" id="">
            <input type="submit" value="提交">

        </form>
        <form action="{{url('wechat/do_upload_wechat_voice')}}" method="post" enctype="multipart/form-data">
            {{--<form action="https://api.weixin.qq.com/cgi-bin/media/upload?access_token={{$token}}&type=image" method="post" enctype="multipart/form-data">--}}
            @csrf
            <h3>voice</h3>
            <input type="file" name="file_name" id="">
            <input type="submit" value="提交">

        </form>
    </center>
    </body>
</html>