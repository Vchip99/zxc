<!DOCTYPE html>
<html lang="en">
  <head>
    @yield('title')
    <meta name="description" content="Basic html form ">
    <meta charset="UTF-8">
    <meta name="keywords" content="html,htmlform,form">
    <meta name="author" content="Vchip Technology">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/star-rating.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/star-rating.css') }}" />
    <link href="{{ asset('css/nav_footer.css?ver=1.0')}}" rel="stylesheet"/>
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
      .fa {
        font-size: medium !important;
      }
      .rating-container .filled-stars{
        color: #e7711b;
        border-color: #e7711b;
      }
      .rating-xs {
          font-size: 0em;
      }
      .img-circle {
        border-radius: 50%;
        width: 40px;
        height: 40px;
      }
      .navbar-nav > li > a {
        color: #fff;
        font-size: 14px;
        font-family: 'Noto Sans', sans-serif;
        text-transform: uppercase;
        letter-spacing: 1px;
      }
      .navbar-nav > li > a:hover{
        color: black !important;
      }
      .navbar-toggle .icon-bar {
        background: white !important;
      }
      .footer a,.footer h2{
        color: white !important;
      }
    </style>
    <script type="text/javascript">
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
    </script>
    @yield('header-css')
    @yield('header-js')
  </head>
  <body class="is-preload">
    @yield('content')
    @yield('footer')
  </body>
</html>