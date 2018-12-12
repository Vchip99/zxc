@extends('dashboard.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> My Payments </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-inr"></i> Payments</li>
      <li class="active">My Payments </li>
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
	<div class="content-wrapper v-container tab-content" >
    <div class="">
      <div class="container">
        <div class="row">
          <div class="col-lg-12" id="all-result">
            <div class="panel panel-info">
              <div class="panel-heading text-center">
               Payments
              </div>
              <div class="panel-body">
                <table  class="" id="dataTables-example">
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Name</th>
                      <th>Type</th>
                      <th>Date</th>
                      <th>Payment</th>
                      <th>Receipt</th>
                    </tr>
                  </thead>
                  <tbody>
                    @if(count($results) > 0)
                      @foreach($results as $index => $result)
                        <tr class="" style="overflow: auto;">
                          <td>{{$index + 1}}</td>
                          <td>{{ $result['name'] }}</td>
                          <td>{{ $result['type'] }}</td>
                          <td>{{ $result['updated_at'] }}</td>
                          <td>Rs. {{ $result['price'] }}</td>
                          <td>
                            @if('Paper' == $result['type'])
                              <a href="{{url('college/'.Session::get('college_user_url').'/receipt/paper')}}/{{$result['id']}}" target="_blank">Receipt</a>
                            @elseif('Course' == $result['type'])
                              <a href="{{url('college/'.Session::get('college_user_url').'/receipt/course')}}/{{$result['id']}}" target="_blank">Receipt</a>
                            @else
                              <a href="{{url('college/'.Session::get('college_user_url').'/receipt/vkit')}}/{{$result['id']}}" target="_blank">Receipt</a>
                            @endif
                          </td>
                        </tr>
                      @endforeach
                      <tr>
                        <td colspan="3"></td>
                        <td>Total</td>
                        <td colspan="2">Rs. {{$total}}</td>
                      </tr>
                    @elseif(0 == count($results))
                      <tr class="">
                        <td colspan="6">No Payment.</td>
                      </tr>
                    @endif
                  </tbody>
                </table>
               </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop