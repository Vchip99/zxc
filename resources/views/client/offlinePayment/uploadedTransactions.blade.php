@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Offline Payment  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-address-book"></i> Offline Payment </li>
      <li class="active"> Manage  Offline Payment </li>
    </ol>
  </section>
  <style type="text/css">
    .img-box{
      min-width: 100%;
      width: 300px;
      height: 150px;
    }
    @media screen and (max-width: 700px) {
      .img-box{
        width: 110px;
        height: 150px;
      }
    }
  </style>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container">
    <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Photo</th>
            <th> Batch </th>
            <th> User </th>
            <th>Comment</th>
            <th>Download</th>
          </tr>
        </thead>
        <tbody id="">
          @if(count($transactions) > 0)
            @foreach($transactions as $index => $transaction)
            <tr>
              <td>{{$index + 1}}</td>
              <td><a href="#image_{{$transaction->id}}" data-toggle="modal"><img class="img-box" src="{{asset($transaction->image)}}" alt=""></a></td>
              <td>{{$transaction->batch->name}}</td>
              <td>{{$transaction->user->name}}</td>
              <td>{{$transaction->comment}}</td>
              <td><a href="{{asset($transaction->image)}}" download class="btn btn-primary download" style="width: 50px;"><i class="fa fa-download" aria-hidden="true" title="Download"></i></a></td>
              <div id="image_{{$transaction->id}}" class="modal fade" role="dialog">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button class="close" data-dismiss="modal">×</button>
                      <h2  class="modal-title">Transaction</h2>
                    </div>
                    <div class="modal-body">
                      <div class="iframe-container">
                          <iframe src="{{asset($transaction->image)}}" frameborder="0"></iframe>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </tr>
            @endforeach
          @else
            <tr><td colspan="6">No offline payments.</td></tr>
          @endif
        </tbody>
      </table>
  </div>
@stop