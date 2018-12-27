<!DOCTYPE html>
<html lang="en">
  <head>
    @yield('title')
    <meta name="description" content="Basic html form ">
    <meta charset="UTF-8">
    <meta name="keywords" content="html,htmlform,form">
    <meta name="author" content="Vchip Technology">

    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/font-awesome/css/font-awesome.min.css') }}">
    <style type="text/css">
      /* For Horizontal Scrolling */
      .scrolling-wrapper {
        overflow-x: scroll;
        overflow-y: hidden;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }
      .scrolling-wrapper .card {
        display: inline-block;
      }
      /* end For Horizontal Scrolling */
      .navbar-nav {
        margin-top: 0px !important;
      }
    </style>
    @yield('header-css')
    @yield('header-js')
  </head>
  <body class="is-preload">
    @yield('content')
    @yield('footer')
  </body>
</html>