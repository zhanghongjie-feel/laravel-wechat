<!doctype html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<script src="/js/jquery.min.js"></script>
<script type="text/javascript">
    $(function(){
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });
    });

    $('#aa').click(function(){
        // alert(11)
        var cart=$('#cart_num').val();
        // alert(cart)
        $.ajax({
            url: "{{url('/parking/do_entry')}}" ,
            type: 'POST',
            data: {cart:cart},
            dataType: 'json',
            success: function(data){
                alert(data.content);
            }
        });
        return false;
    });
</script>
</body>
</html>