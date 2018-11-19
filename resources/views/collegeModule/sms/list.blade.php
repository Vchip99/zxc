@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Purchase Sms  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-inr"></i> Purchase </li>
      <li class="active"> Purchase Sms </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container">
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
    <div class="form-group row">
      <div id="addTopicDiv">
        <a id="addTopic" href="{{url('college/'.Session::get('college_user_url').'/createCollegePurchaseSms')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Purchase Sms">Purchase Sms</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="" id="purchaseSms">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Purchased By </th>
          <th>Note</th>
          <th>Price </th>
          <th>Date </th>
        </tr>
      </thead>
      <tbody>
        @if(count($collegePayments) > 0)
          @foreach($collegePayments as $index => $collegePayment)
          <tr style="overflow: auto;">
            <td>{{$index + $collegePayments->firstItem()}}</td>
            <td>{{$collegePayment->user_name}}</td>
            <td>{{$collegePayment->note}}</td>
            <td>Rs. {{$collegePayment->price}}</td>
            <td>{{$collegePayment->created_at}}</td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="10">No Payments.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $collegePayments->links() }}
    </div>
  </div>
  </div>
@stop