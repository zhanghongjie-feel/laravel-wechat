<html>
    <head>
        <title>素材管理</title>
    </head>
<body>
            <button id="image">image</button>&nbsp;&nbsp;    <button id="voice">voice</button>&nbsp;&nbsp;
    <center>
        <h1>素材管理</h1>
        <a href="{{url('wechat/upload_wechat')}}">上传</a><br>

        <table border="1">
            <tr>
                <td>id</td>
                <td>media_id</td>
                <td>type</td>
                <td>path</td>
                <td>add_time</td>
                <td>操作</td>
            </tr>
            @foreach($info as $v)
            <tr>
                <td>{{$v->id}}</td>
                <td>{{$v->media_id}}</td>
                <td>@if($v->type==1)image @elseif($v->type==2)voice @elseif($v->type==3)video @elseif($v->type==4)thumb @endif</td>
                <td>{{$v->path}}</td>
                <td>{{date('Y-m-d,H:i',$v->add_time)}}</td>
                <td>
                    <a href="{{url('wechat/del_source')}}">删除</a> @if($v->is_download==1) | <a href="{{url('download_source')}}?id={{$v->id}}">下载资源</a> @endif
                </td>
            </tr>
            @endforeach
        </table>
        <button id="pre" data="{{$pre_page}}">上一页</button>&nbsp;&nbsp;&nbsp;&nbsp;
        <button id="next" data="{{$next_page}}">下一页</button>
    </center>
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script>
        $(function(){
            $('#image').click(function(){
                window.location.href='{{url('wechat/source')}}?page=1&source_type=image';
            });
            $('#voice').click(function(){
                window.location.href='{{url('wechat/source')}}?page=1&source_type=voice';
            });
            $('#pre').click(function(){
                var pre_page=$(this).attr('data');
                window.location.href='{{url('wechat/source')}}?page='+pre_page+'&source_type={{$source_type}}';
            });
            $('#next').click(function(){
                var next_page=$(this).attr('data');
                window.location.href='{{url('wechat/source')}}?page='+next_page+'&source_type={{$source_type}}';
            });
        });
    </script>
</body>
</html>