<html>
    <body>
    <center>
        {{--<img src="{{asset('/storage/goods/H6h8OWQVmMgbWM0mExpDj3ieUCWWNZXIsFQ4vACB.jpeg')}}" alt="">--}}
        <form action="{{url('wechat/do_upload_wechat')}}" method="post" enctype="multipart/form-data">
            {{--<form action="https://api.weixin.qq.com/cgi-bin/media/upload?access_token={{$token}}&type=image" method="post" enctype="multipart/form-data">--}}
            @csrf

            <select name="type" id="">
                <option value="1">image</option>
                <option value="2">voice</option>
                <option value="3">video</option>
                <option value="4">thumb</option>
            </select>
            <input type="file" name="file_name" id="">
            <input type="submit" value="提交">

        </form>
    </center>
    </body>
</html>