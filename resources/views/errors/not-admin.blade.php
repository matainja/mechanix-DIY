<!DOCTYPE html>
<html>
<head>
    <title>Access Denied</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background:#f8f9fa;
            display:flex;
            align-items:center;
            justify-content:center;
            height:100vh;
        }

        .denied-box{
            background:#fff;
            padding:50px;
            border-radius:16px;
            box-shadow:0 5px 25px rgba(0,0,0,0.08);
            text-align:center;
            max-width:500px;
        }

        .denied-icon{
            font-size:70px;
            color:#dc3545;
            margin-bottom:20px;
        }
    </style>
</head>
<body>

    <div class="denied-box">

        <div class="denied-icon">
            🚫
        </div>

        <h2 class="mb-3">
            You Are Not An Active Admin
        </h2>

        <p class="text-muted mb-4">
            You do not have permission to access this admin panel.
        </p>

        <a href="{{ url('/') }}" class="btn btn-danger">
            Back To Home
        </a>

    </div>

</body>
</html>