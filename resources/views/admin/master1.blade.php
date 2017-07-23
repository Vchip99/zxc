
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, shrink-to-fit=no, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <script src="{{asset('js/js/jquery.min.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    <!-- Bootstrap Core CSS -->
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/simple-sidebar.css')}}" rel="stylesheet">
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
</head>

<body>

    <div id="wrapper">
    @include('admin.menu')
        <!-- Page Content -->
        <!-- <div id="page-content-wrapper"> -->
            <!-- <div class="container-fluid"> -->
          <div class="row">
              <div class="col-lg-12">
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
          </div>
          <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    @yield('content')
                </div>
            </div>
          </div>
            <!-- </div> -->
            <!-- </div> -->
        <!-- </div> -->
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->




</body>

</html>
