<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, shrink-to-fit=no, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <script src="{{asset('js/jquery.min.js?ver=1.0')}}"></script>
    <script src="{{asset('js/bootstrap.min.js?ver=1.0')}}"></script>
    <script src="{{asset('vendor/jsvalidation/js/jsvalidation.js?ver=1.0')}}"></script>
    <!-- Bootstrap Core CSS -->
    <link href="{{asset('css/bootstrap.min.css?ver=1.0')}}" rel="stylesheet">
    <link href="{{asset('css/simple-sidebar.css?ver=1.0')}}" rel="stylesheet">
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <link href="{{asset('css/font-awesome/css/font-awesome.min.css?ver=1.0')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('css/bootstrap.min.css?ver=1.0')}}" rel="stylesheet">
    <link href="{{asset('css/nav_footer.css?ver=1.0')}}" rel="stylesheet"/>
</head>
<body>
<!-- <div id="wrapper"> -->
    <div class="row affix-row">
        @include('admin.menu')
        <div class="col-sm-9 col-md-10 affix-content">
            <div>
                 <table border="0" width="100%" cellspacing="0" cellpadding="0" background="{{asset('images/blackbar.jpg')}}">
                      <tr>
                        <td width="60%" align="center">
                          <b style="color: White; height: 45px;">Online Quiz</b>
                      </td>
                        <td >
                          <img border="0" src="{{ asset('images/topright.jpg')}}" width="100%" height="68" align="right">
                        </td>
                      </tr>
                </table>
            </div>
            <div class="container">
                <div>
                  @yield('content')
                </div>
            </div>
        </div>
    </div>
<!-- </div> -->
<style type="text/css">
/* make sidebar nav vertical */
@media (min-width: 768px){
  .affix-content .container {
    width: 700px;
  }

  html,body{
    background-color: #f8f8f8;
    height: 100%;
    overflow: hidden;
  }
    .affix-content .container .page-header{
    margin-top: 0;
  }
  .sidebar-nav{
        position:fixed;
        width:100%;
  }
  .affix-sidebar{
    padding-right:0;
    font-size:small;
    padding-left: 0;
  }
  .affix-row, .affix-container, .affix-content{
    height: 100%;
    margin-left: 0;
    margin-right: 0;
  }
  .affix-content{
    background-color:white;
  }
  .sidebar-nav .navbar .navbar-collapse {
    padding: 0;
    max-height: none;
  }
  .sidebar-nav .navbar{
    border-radius:0;
    margin-bottom:0;
    border:0;
  }
  .sidebar-nav .navbar ul {
    float: none;
    display: block;
  }
  .sidebar-nav .navbar li {
    float: none;
    display: block;
  }
  .sidebar-nav .navbar li a {
    padding-top: 12px;
    padding-bottom: 12px;
  }
}

@media (min-width: 769px){
  .affix-content .container {
    width: 600px;
  }
    .affix-content .container .page-header{
    margin-top: 0;
  }
}

@media (min-width: 992px){
  .affix-content .container {
  width: 900px;
  }
    .affix-content .container .page-header{
    margin-top: 0;
  }
}

@media (min-width: 1220px){
  .affix-row{
    overflow: hidden;
  }

  .affix-content{
    overflow: auto;
  }

  .affix-content .container {
    width: 1000px;
  }

  .affix-content .container .page-header{
    margin-top: 0;
  }
  .affix-content{
    padding-right: 30px;
    padding-left: 30px;
  }
  .affix-title{
    border-bottom: 1px solid #ecf0f1;
    padding-bottom:10px;
  }
  .navbar-nav {
    margin: 0;
  }
  .navbar-collapse{
    padding: 0;
  }
  .sidebar-nav .navbar li a:hover {
    background-color: #428bca;
    color: white;
  }
  .sidebar-nav .navbar li a > .caret {
    margin-top: 8px;
  }
}

</style>




</body>

</html>
