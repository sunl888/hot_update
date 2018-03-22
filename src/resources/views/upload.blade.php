<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form action="{{url('upload')}}" method="post" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type="file" name="{{config('update.upload_form_key')}}">
    <input type="submit">
</form>
</body>
</html>