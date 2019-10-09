@extends('layout.admin')

@section('title', 'openid绑定')
<html>
<body>
@section('content')


        <form action="{{url('admin/do_bangding')}}" method="post">
            @csrf
            <table border="1" class="table table-striped table table-hover">
                <div class="form-group" style="margin-top:60px;>
                    <label for="exampleInputEmail1"><h1>绑定管理员账号</h1></label>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1"><h3>name</h3></label>
                    <input type="password" class="form-control" name='name' placeholder="name">
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1"><h3>password</h3></label>
                    <input type="password" class="form-control" name="password" id="exampleInputPassword1" placeholder="Password">
                </div>
                <button type="submit" class="btn btn-default">绑定</button>
            </table>

            {{--<button>发送验证码</button>--}}

        </form>
@endsection

</body>
</html>