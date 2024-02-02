<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

        <!-- Icons reference -->
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">

        <!-- CSS files -->
        <link rel="stylesheet" type="text/css" href="{{ asset('standards/css_standards.css') }}">
 
        @yield('styles')

        <!-- Scripts -->
        <!-- <script src="https://kit.fontawesome.com/29a438e58f.js" crossorigin="anonymous"></script> -->

        <!-- Tab icon -->
        <link rel="icon" href="{{ asset('auth/images/PUPLogo.png') }}">

        <!-- Page title -->
        <title>@yield('title')</title>
    </head>
<body>
    @yield('body')
</body>
</html>
