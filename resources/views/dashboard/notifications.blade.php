@extends('dashboard.dashboard')
@section('dashboard_header')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
  @media only screen and (max-width: 760px), (max-device-width: 1024px) and (min-device-width: 768px){
  td {
      padding-left: 50% !important;
  }
}
  </style>
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Notifications</h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-star"></i> Notifications</li>
      <li class="active">My Notifications </li>
    </ol>
  </section>
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
@stop
@section('dashboard_content')
  <div class="container ">
  <div>
    <table class="table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Message</th>
        </tr>
      </thead>
      <tbody id="mobile_user_notifications">
        @if(count($notifications) > 0)
          @foreach($notifications as $index => $notification)
          <tr style="overflow: auto;">
            <td scope="row">{{$index + 1}}</td>
            <td>{!! $notification->message !!}
            </td>
          </tr>
          @endforeach
        @else
            <tr><td colspan="3">No Notifications.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{$notifications->links()}}
    </div>
  </div>
  </div>
@stop