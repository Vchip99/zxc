@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Paid Sms For Client </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-inr"></i> Payments </li>
      <li class="active"> Paid Sms For Client </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <div class="container admin_div" >
    @if(count($errors) > 0)
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
    @endif
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div class="form-group row"><label> Please purchase sms in thousands. Price is 150 Rs/thousands sms.</label></div>
    <form action="{{url('admin/clientPurchaseSms')}}" method="POST" id="submitForm">
      {{ csrf_field() }}
      <div class="form-group row">
        <label class="col-sm-2 col-form-label" for="">Client:</label>
        <div class="col-sm-3">
          <select class="form-control" id="client" name="client">
            <option value="0"> Select Client </option>
            @if(count($clients) > 0)
              @foreach($clients as $client)
                <option value="{{$client->id}}">{{$client->name}}</option>
              @endforeach
            @endif
          </select>
        </div>
      </div>
      <div class="form-group row">
        <label class="col-sm-2 col-form-label" for="category">No of Sms:</label>
        <div class="col-sm-3">
          <input type="number" id="sms_count" name="sms_count" min="0" value="" class="form-control" required>
        </div>
      </div>
      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Total</label>
        <div class="col-sm-3">
          <input type="number" name="total" class="form-control" min="0" id="total" value="" required>
        </div>
      </div>
      <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </div>
    </form>
  </div>
@stop