@extends('clientuser.dashboard.dashboard')
@section('dashboard_header')
  <link href="{{asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
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
@section('module_title')
  <section class="content-header">
    <h1> Uploaded Transactions </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Payments</li>
      <li class="active"> Uploaded Transactions </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
<div class="content-wrapper v-container tab-content" >
  <div class="container">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div class="form-group row">
      <div>
        <a href="{{url('createUploadTransaction')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Upload Transaction">Upload Transaction</a>&nbsp;&nbsp;
      </div>
    </div>
    <div>
      <table>
        <thead class="thead-inverse">
          <tr>
            <th>#</th>
            <th>Photo</th>
            <th> Batch </th>
            <th>Comment</th>
          </tr>
        </thead>
        <tbody id="">
          @if(count($transactions) > 0)
            @foreach($transactions as $index => $transaction)
            <tr style="overflow: auto;">
              <td>{{$index + 1}}</td>
              <td><img class="img-box" src="{{$transaction->image}}" alt=""></td>
              <td>{{$transaction->batch->name}}</td>
              <td>{{$transaction->comment}}</td>
            </tr>
            @endforeach
          @else
            <tr><td colspan="4">No batches are created.</td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
@stop