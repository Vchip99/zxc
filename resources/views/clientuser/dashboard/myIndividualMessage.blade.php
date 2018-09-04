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
    <h1> My Individual Message  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-envelope"></i> Message </li>
      <li class="active"> My Individual Message </li>
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
            Individual Messages
          </div>
          <div class="panel-body">
            @if(count($myMessages) > 0)
              @foreach($myMessages as $message)
                <div class="form-group row divStyle">
                  <div class=""><b>{{$message['date']}}-[{{$message['batch']}}]</b> @ {{$message['message']}}</div>
                </div>
              @endforeach
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@stop