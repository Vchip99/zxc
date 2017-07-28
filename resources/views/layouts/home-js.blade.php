<!-- JavaScript -->
<script src="{{asset('js/jquery.min.js?ver=1.0')}}"></script>
<script src="{{asset('js/bootstrap.min.js?ver=1.0')}}"></script>
<script src="{{asset('js/slideanim.js?ver=1.0')}}"></script>
<script src="{{asset('js/scrolling-nav.js?ver=1.0')}}"></script>
<script src="{{asset('js/jquery-confirm.min.js?ver=1.0')}}"></script>
<script src="{{asset('js/bootstrap-multiselect.js?ver=1.0')}}"></script>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>