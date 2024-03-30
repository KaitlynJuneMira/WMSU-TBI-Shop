<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Activation - WMSU Technology Business Incubator</title>
</head>
<body style="font-family: Arial, sans-serif;">

    <table>
        <tr>
            <td><strong>Dear {{ $name }},</strong></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>We are delighted to welcome you to WMSU Technology Business Incubator Shop. To activate your account, please click on the link below:</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><a href="{{ url('/user/confirm/'.$code) }}">Confirm Your Account</a></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><em>Thanks & Regards,</em></td>
        </tr>
        <tr>
            <td><strong>WMSU Technology Business Incubator</strong></td>
        </tr>
    </table>

</body>
</html>
