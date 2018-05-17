<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Online Education">
    <meta name="keywords" content="GATE, Test series, Online education">
	<meta name="author" content="https://vchipdesign.com">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>

    <title>Online Education</title>
    <!-- Bootstrap -->
    <link href="{{ asset('css/css/bootstrap.min.css')}} " rel="stylesheet">
    <link href="{{ asset('css/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{ asset('css/css/login.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/css/main.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/css/style.css')}}">
    <link rel="stylesheet" href="{{ asset('/css/css/jquery-ui-custom.css') }}"/>
    <link rel="stylesheet" href="{{ asset('/css/css/ui.jqgrid.css') }}"/>
    <link rel="stylesheet" href="{{ asset('/css/css/jquery.numpad.css') }}">

    <script src="{{asset('/js/js/jquery.min.js')}}"></script>
    <script src="{{ asset('/js/js/bootstrap.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('/js/js/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('/js/js/jquery-ui-custom.min.js') }}"></script>
    <script src="{{ asset('/js/js/grid.locale-en.js') }}"></script>
    <script src="{{ asset('/js/js/jquery.jqGrid.min.js') }}"></script>
    <script src="{{ asset('/js/js/jquery.numpad.js') }}"></script>


</head>
<body>
	@yield('content')
</body>
</html>