@extends('clientuser.dashboard.dashboard')
@section('dashboard_header')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
    @media only screen and (max-width: 760px), (max-device-width: 1024px) and (min-device-width: 768px){
      td {
          padding-left: 50% !important;
      }
    }
    #my-message .panel-body {
      height: 800px;
      overflow: scroll;
    }
  </style>
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Event  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-envelope"></i> Message </li>
      <li class="active"> My Event </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container">
    <div class="row">
      <div class="col-lg-12" id="my-message">
        <div class="panel panel-info">
          <div class="panel-heading text-center">
            My Events
          </div>
          <div class="panel-body">
            @if(count($events) > 0)
              @foreach($events as $message)
                @if(!empty($message->photo))
                  <div class="form-group row">
                    <img class="img-responsive" src="{{url($message->photo)}}" alt="message_img" style="max-height: 500px;width: 100%;float: center;">
                  </div>
                  <div class="form-group row"><b>Event From {{date('Y-m-d h:i:s a', strtotime($message->start_date))}} To {{date('Y-m-d h:i:s a', strtotime($message->end_date))}} </b> @ {{$message->message}}
                  </div>
                @else
                  <div class="form-group row"><b>Event From {{date('Y-m-d h:i:s a', strtotime($message->start_date))}} To {{date('Y-m-d h:i:s a', strtotime($message->end_date))}} </b> @ {{$message->message}}
                  </div>
                @endif
              @endforeach
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@stop