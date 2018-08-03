@extends('client.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Settings </h1>
  </section>
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
    @media screen and (max-width: 320px) {
      .content,.col-sm-12,.v-container, .container, .col-md-7, .list-group .list-group-item,.col-xs-7{
        padding-left: 0px !important;
        padding-right: 0px !important;
      }
    }

    @media screen and (min-width: 350px) and (max-width: 415px){
      .content,.col-sm-12,.v-container, .container, .col-md-7, .list-group .list-group-item,.col-xs-7{
        padding-left: 0px !important;
        padding-right: 0px !important;
      }
    }
  </style>
@stop
@section('dashboard_content')
	<div class="content-wrapper v-container tab-content" >
    <div id="profile" class="tab-pane active">
      <div class="container">
        <div class="row">
          <div class="col-md-offset-2">
            <div class="panel panel-default">
              <ul class="list-group">
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Allow Login To Non-Verified Email-Id</b></div>
                        <div class="col-xs-7 pull-left">
                          @if(1 == Auth::guard('client')->user()->allow_non_verified_email)
                            <input type="checkbox" id="allow_non_verified_email" checked onClick="toggleNonVerifiedEmail();">
                          @else
                            <input type="checkbox" id="allow_non_verified_email" onClick="toggleNonVerifiedEmail();">
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
       </div>
    </div>
  </div>
  @include('footer.clientLiveChat')
  <script type="text/javascript">
    var currentToken = $('meta[name="csrf-token"]').attr('content');
    function toggleNonVerifiedEmail(){
      $.ajax({
          method:'POST',
          url: "{{url('toggleNonVerifiedEmailStatus')}}",
          data:{_token:currentToken}
      }).done(function( status ) {
        var currentStatus = $('#allow_non_verified_email').prop('checked');
        if(true == currentStatus && 1 == status){
          $('#allow_non_verified_email').prop('checked', true);
        } else {
          $('#allow_non_verified_email').prop('checked', false);
        }
      });
    }
  </script>
@stop