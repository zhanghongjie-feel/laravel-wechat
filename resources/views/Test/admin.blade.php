<html>
    <head>
        <title>管理课程</title>
    </head>
    <body>
        <center>
            <h1>课程管理</h1>
            <form action="{{url('course/create')}}">
                第一节课：
                @if(!empty($info->one))
                    <select name="one" id="" value="">
                        <option value="">{{$info->one}}</option>
                        <option value="php">php</option>
                        <option value="java">java</option>
                        <option value="C++">C++</option>
                    </select><br><br>
                @elseif(empty($info->one))
                    <select name="one" id="">
                        <option value="">--请选择--</option>
                        <option value="php">php</option>
                        <option value="java">java</option>
                        <option value="C++">C++</option>
                    </select><br><br>
                @endif
                @if(!empty($info->two))
                第二节课：
                    <select name="two" id="">
                        <option value="">{{$info->two}}</option>
                        <option value="语文">语文</option>
                        <option value="国学">国学</option>
                    </select><br><br>
                @elseif(empty($info->two))
                    <select name="two" id="">
                        <option value="">--请选择--</option>
                        <option value="语文">语文</option>
                        <option value="国学">国学</option>
                    </select><br><br>
                @endif
                @if(!empty($info->three))
                第三节课：
                <select name="three" id="">
                    <option value="">{{$info->three}}</option>
                    <option value="数学">数学</option>
                    <option value="相对论">相对论</option>
                </select><br><br>
                @elseif(empty($info->three))
                    第三节课：
                    <select name="three" id="">
                        <option value="">--请选择--</option>
                        <option value="数学">数学</option>
                        <option value="相对论">相对论</option>
                    </select><br><br>
                @endif
                @if(!empty($info->four))
                第四节课：
                <select name="four" id="">
                    <option value="">{{$info->four}}</option>
                    <option value="英语">英语</option>
                    <option value="德语">德语</option>
                </select><br><br>
                <input type="submit" value="提交">
                @elseif(empty($info->four))
                    第四节课：
                    <select name="four" id="">
                        <option value="">--请选择--</option>
                        <option value="英语">英语</option>
                        <option value="德语">德语</option>
                    </select><br><br>
                    <input type="submit" value="提交">
                @endif
            </form>
        </center>
    </body>
</html>