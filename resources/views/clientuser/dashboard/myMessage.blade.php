@extends('clientuser.dashboard.dashboard')
@section('dashboard_header')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
    @media only screen and (max-width: 760px), (max-device-width: 1024px) and (min-device-width: 768px){
      td {
          padding-left: 50% !important;
      }
    }
    .divStyle{
      border-bottom:  2px solid white;
      padding: 5px 5px;
    }
    .mainDiv{
      border: 2px solid white;
    }
  </style>
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Message  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-envelope"></i> Message </li>
      <li class="active"> My Message </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container">
    <div class="mainDiv">
      @if(count($messages) > 0)
        @foreach($messages as $message)
          @if(!empty($message->photo))
            <div class="form-group row divStyle">
              <div class="col-md-3">
                <img class="img-responsive" src="{{$message->photo}}" alt="">
              </div>
              <div class="col-md-9"><b>{{$message->updated_at}}</b> @ {{$message->message}}</div>
            </div>
          @else
            <div class="form-group row divStyle">
              <div class=""><b>{{$message->updated_at}}</b> @ {{$message->message}}</div>
            </div>
          @endif
        @endforeach
      @endif
    </div>
  </div>
@stop