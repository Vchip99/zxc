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
    <h1> Admin Messages: Unread - {{Auth::user()->adminNotificationCount($selectedYear,$selectedMonth)}} </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-star"></i> Notifications</li>
      <li class="active">Admin Messages </li>
    </ol>
  </section>
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
@stop
@php
  $paginationVars = [];
  if($selectedYear > 0){
    $paginationVars['year'] = $selectedYear;
  }
  if($selectedMonth > 0){
    $paginationVars['month'] = $selectedMonth;
  }
@endphp
@section('dashboard_content')
  <div class="container ">
    <div class="row">
      <form action="{{url('college/'.Session::get('college_user_url').'/adminMessages')}}" method="GET" id="search">
        <div class="col-sm-2 mrgn_10_btm">
          <select id="year" class="form-control" name="year" required>
            <option value="">Select Year</option>
            @foreach($years as $year)
              @if($selectedYear == $year)
                <option value="{{$year}}" selected>{{$year}}</option>
              @else
                <option value="{{$year}}">{{$year}}</option>
              @endif
            @endforeach
          </select>
        </div>
        <div class="col-sm-2 mrgn_10_btm">
          <select id="month" class="form-control" name="month" onChange="search();" required>
            <option value="">Select Month</option>
            @foreach($months as $index => $month)
              @if($selectedMonth == $index)
                <option value="{{$index}}" selected>{{$month}}</option>
              @else
                <option value="{{$index}}">{{$month}}</option>
              @endif
            @endforeach
          </select>
        </div>
      </form>
    </div>
    <div class="row">
      <div>
        <table class="table ">
          <thead class="thead-inverse">
            <tr>
              <th>#</th>
              <th>Message</th>
              <th>Already Seen</th>
            </tr>
          </thead>
          <tbody id="mobile_admin_messages">
            @if(count($notifications) > 0)
              @foreach($notifications as $index => $notification)
              <tr>
                <td scope="row">{{$index + 1}}</td>
                <td>{!! $notification->message !!}
                </td>
                <td>@if(in_array($notification->id, $readNotificationIds)) <i class="fa fa-envelope-open"></i> @else <i class="fa fa-envelope"></i> @endif</td>
              </tr>
              @endforeach
            @else
                <tr><td colspan="3">No Messages.</td></tr>
            @endif
          </tbody>
        </table>
        <div style="float: right;">
          @if($selectedYear > 0 && $selectedMonth > 0)
            {{$notifications->appends($paginationVars)->links()}}
          @else
            {{$notifications->links()}}
          @endif
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function search(){
      document.getElementById('search').submit();
    }
  </script>
@stop