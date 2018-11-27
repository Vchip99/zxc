@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Batch Payments  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-address-book"></i> Offline Payment </li>
      <li class="active"> Batch Payments </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div>
      <table class="" id="">
        <thead class="thead-inverse">
          <tr>
            <th>#</th>
            <th>Batch</th>
            <th>Total Amount</th>
          </tr>
        </thead>
        <tbody id="">
          @if(count($batchPayments) > 0)
            @php
              $index = 1;
              $totalBatchAmount = 0;
            @endphp
            @foreach($batchPayments as $batchId => $batchPayment)
              <tr style="overflow: auto;">
                <td>{{ $index++}}</td>
                <td><a href="#batchModal_{{$batchId}}" data-toggle="modal">{{$batchPayment['batch']}}</a></td>
                <td>{{$batchPayment['amount']}}</td>
                @php
                  $totalBatchAmount += $batchPayment['amount'];
                @endphp
              </tr>
            @endforeach
            <tr style="overflow: auto;">
              <td></td>
              <td> Total</td>
              <td>{{$totalBatchAmount}}</td>
            </tr>
          @else
            <tr><td colspan="3">No Batch payments</td></tr>
          @endif
        </tbody>
      </table>
      @if(count($batchPayments) > 0)
        @foreach($batchPayments as $batchId => $batchPayment)
          @php
            $batchIndex = 1;
            $batchAmount = 0;
          @endphp
          <div class="modal" id="batchModal_{{$batchId}}" role="dialog" style="display: none;">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">×</button>
                  <h4 class="modal-title">Payment Details of {{$batchPayment['batch']}}</h4>
                  <table id="batchPayment_{{$batchId}}">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Email-Id/User-Id</th>
                        <th>Phone</th>
                        <th>Total Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if(count($batchUsers[$batchId]) > 0)
                        @foreach($batchUsers[$batchId] as $userId => $batchUser)
                          <tr style="overflow: auto;">
                            <td>{{$batchIndex++}}</td>
                            <td><a href="#batchUser_{{$batchId}}_{{$userId}}" data-toggle="modal">{{$batchUser['user']}}</a></td>
                            <td>{{$batchUser['email']}}</td>
                            <td>{{$batchUser['phone']}}</td>
                            <td>{{$batchUser['amount']}}</td>
                            @php
                              $batchAmount += $batchUser['amount'];
                            @endphp
                          </tr>
                        @endforeach
                        <tr style="overflow: auto;">
                          <td colspan="3"></td>
                          <td>Total</td>
                          <td>{{$batchAmount}}</td>
                        </tr>
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      @endif
      @if(count($batchPayments) > 0)
        @foreach($batchPayments as $batchId => $batchPayment)
          @if(count($batchUsers[$batchId]) > 0)
            @foreach($batchUsers[$batchId] as $userId => $batchUser)
              <div class="modal" id="batchUser_{{$batchId}}_{{$userId}}" role="dialog" style="display: none;">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">×</button>
                      <h4 class="modal-title">Payment Details of {{$batchUser['user']}}</h4>
                      <table id="userPayment_{{$batchId}}_{{$userId}}">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th>Comment</th>
                            <th>Amount</th>
                          </tr>
                        </thead>
                        <tbody>
                          @if(count($usersPayment[$batchId][$userId]) > 0)
                            @php
                              $userTotal = 0;
                            @endphp
                            @foreach($usersPayment[$batchId][$userId] as $index => $payment)
                              <tr style="overflow: auto;">
                                <td>{{$index + 1}}</td>
                                <td>{{$payment['date']}}</td>
                                <td>{{$payment['due_date']}}</td>
                                <td>{{$payment['comment']}}</td>
                                <td>{{$payment['amount']}}</td>
                                @php
                                  $userTotal += $payment['amount'];
                                @endphp
                              </tr>
                            @endforeach
                              <tr style="overflow: auto;">
                                <td colspan="3"></td>
                                <td>Total</td>
                                <td>{{$userTotal}}</td>
                              </tr>
                          @endif
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          @endif
        @endforeach
      @endif
    </div>
  </div>

@stop