<link href="{{ asset('css/bootstrap.min.css?ver=1.0')}} " rel="stylesheet">
<link href="{{ asset('css/font-awesome/css/font-awesome.min.css?ver=1.0')}}" rel="stylesheet"/>
<link rel="stylesheet" href="{{ asset('css/main.css?ver=1.0')}}">
<link rel="stylesheet" href="{{ asset('css/style.css?ver=1.0')}}">
<link rel="stylesheet" href="{{ asset('css/jquery.numpad.css?ver=1.0') }}">
<link rel="stylesheet" href="{{ asset('css/jquery-ui-custom.css?ver=1.0') }}"/>
<link rel="stylesheet" href="{{ asset('css/ui.jqgrid.css?ver=1.0') }}"/>
<link href="{{asset('css/jquery-confirm.min.css?ver=1.0')}}" rel="stylesheet"/>

<script src="{{ asset('js/jquery.min.js?ver=1.0')}}"></script>
<script src="{{ asset('js/bootstrap.min.js?ver=1.0')}}" type="text/javascript"></script>
<script src="{{ asset('js/jquery.jqGrid.min.js?ver=1.0') }}"></script>
<script src="{{ asset('js/jquery.numpad.js?ver=1.0') }}"></script>
<script src="{{ asset('js/jquery-confirm.min.js?ver=1.0')}}"></script>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>