<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Registration Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif;">

    <table>
        <tr>
            <td><strong>Dear {{ $name }},</strong></td>
        </tr>
        <tr>
            <td>&nbsp;<br></td>
        </tr>
        <tr>
            <td>Please click on the link below to confirm your seller account:</td>
        </tr>
        <tr>
            <td><a href="{{ url('vendor/confirm/'.$code) }}">{{ url('vendor/confirm/'.$code) }}</a></td>
        </tr>
        <tr>
            <td>&nbsp;<br></td>
        </tr>
        <tr>
            <td><em>Thanks & Regards,</em></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><strong>WMSU Technology Business Incubator</strong></td>
        </tr>
    </table>

</body>
</html>
