@extends('client.dashboard')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Bank Details  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-inr"></i> Plans & Billing </li>
      <li class="active"> Bank Details </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container admin_div">
    <form action="{{url('updateBankDetails')}}" method="POST">
  @if(isset($bankDetail->id))
      <input type="hidden" name="bank_detail_id" value="{{$bankDetail->id}}">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('account_holder_name')) has-error @endif">
    <label for="account_holder_name" class="col-sm-2 col-form-label">Account Holder Name:</label>
    <div class="col-sm-3">
        <input type="text" class="form-control" name="account_holder_name" value="{{$bankDetail->account_holder_name}}" required="true">
      @if($errors->has('account_holder_name')) <p class="help-block">{{ $errors->first('account_holder_name') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('account_number')) has-error @endif">
    <label for="name" class="col-sm-2 col-form-label">Account Number:</label>
    <div class="col-sm-3">
        <input type="text" class="form-control" name="account_number" value="{{$bankDetail->account_number}}" required="true">
      @if($errors->has('account_number')) <p class="help-block">{{ $errors->first('account_number') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('ifsc_code')) has-error @endif">
    <label for="name" class="col-sm-2 col-form-label">IFSC Code:</label>
    <div class="col-sm-3">
        <input type="text" class="form-control" name="ifsc_code" value="{{$bankDetail->ifsc_code}}" required="true">
      @if($errors->has('ifsc_code')) <p class="help-block">{{ $errors->first('ifsc_code') }}</p> @endif
    </div>
  </div>
  <div class="form-group row">
      <div class="offset-sm-2 col-sm-3">
        <button type="submit" class="btn btn-primary" title="Submit">Submit</button>
      </div>
    </div>
  </div>
</form>
@stop