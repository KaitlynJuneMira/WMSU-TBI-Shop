<!-- Exporting files -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        .page-break {
            page-break-after: always;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>  
    <h6 class="text-center">{{$title}}</h6>
    <table class="table table-striped">
        <thead style="font-size:13px;margin:0px 0px 0px 0px;padding:0px 0px 0px 0px;">
            <tr>
                <th scope="col">#</th>
                @foreach($header as $key =>$value)
                    <th >{{$value}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody style="font-size:12px;margin:0px 0px 0px 0px;padding:0px 0px 0px 0px;">
            @foreach($content as $key => $value)
                <tr>
                <td>{{$key+1}}</td>
                @foreach($value as $column_key => $column_value)
                    <td>{{$column_value}}</td>
                @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>