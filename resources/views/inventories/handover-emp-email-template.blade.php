<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <style>
    p {
        margin-bottom: 0px !important;
        font-size: 0.9rem;
        line-height: 1.4rem;
    }
    </style>
</head>

<body>
    <p>
        Dear Sir,<br /><br />
        Asset code FRC-CHD-{{$un_id}} is assigned to Employee code: {{$emp_id}} and Employee Name: {{$emp_name}}

        We need to verify<br />

        Please click on the following link to approved / declined your email address:
        
        <a href="{{ url('api/approved-asset/'.Crypt::encrypt($invoice_id))}}" target="_blank"
                class="btn btn-primary verify">Accepted</a>


        <!-- <a href="{{ url('api/declined-asset/'.Crypt::encrypt($invoice_id))}}" target="_blank" -->
            class="btn btn-primary verify">Declined</a>
        <br /><br />

        Auto Email from Eternity
    </p>
</body>

</html>