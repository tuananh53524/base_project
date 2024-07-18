<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
    <meta name="googlebot" content="noindex,nofollow,noarchive"/>
    <title>@yield('title')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="{{ asset('libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    @stack('stylesheet')

    <script>
        var settings = {
            baseUrl: '{{ url('/') }}',
            cdnUrl: '{{ url('/storage') }}'
        }
    </script>

</head>

<body id="page-top">

<div id="wrapper">
    <div id="content-wrapper">

        <div class="container-fluid">
            @yield('content')
        </div>
    </div>
</div>

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Bootstrap core JavaScript-->
<script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
<script src="{{ asset('libs/jquery/jquery-3.7.1.min.js') }}"></script>

@stack('scripts')

<script src="{{ asset('js/admin.js?v=' . config('versions.js')) }}"></script>

</body>

</html>
