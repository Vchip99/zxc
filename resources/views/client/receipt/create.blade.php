@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Receipt Details </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-inr"></i> Plans And Billing </li>
      <li class="active"> Receipt Details </li>
    </ol>
  </section>
  <style type="text/css">
    .timepicker-picker .btn-primary{
      width: 50px !important;
    }
  </style>
@stop
@section('dashboard_content')
  <div class="container admin_div">
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
  @if(isset($receipt->id))
    <form action="{{url('updateReceipt')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="receipt_id" value="{{$receipt->id}}">
  @else
   <form action="{{url('createReceipt')}}" method="POST">
  @endif
    {{ csrf_field() }}
    <div class="form-group row">
      <div align="center"><b>Offline Receipt Details</b></div>
    </div>
    <div class="form-group row  @if ($errors->has('offline_receipt_by')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="offline_receipt_by">Receipt By:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="offline_receipt_by" name="offline_receipt_by" value="{{($receipt->offline_receipt_by)?$receipt->offline_receipt_by:null}}" placeholder="Offline Receipt By" required>
        @if($errors->has('offline_receipt_by')) <p class="help-block">{{ $errors->first('offline_receipt_by') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('offline_address')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="offline_address">Address:</label>
      <div class="col-sm-3">
        <textarea class="form-control" id="offline_address" name="offline_address" required>{{($receipt->offline_address)?$receipt->offline_address:null}}</textarea>
        @if($errors->has('offline_address')) <p class="help-block">{{ $errors->first('offline_address') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('offline_gstin')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="offline_gstin">GSTIN:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="offline_gstin" name="offline_gstin" value="{{($receipt->offline_gstin)?$receipt->offline_gstin:null}}" placeholder="GSTIN" >
        @if($errors->has('offline_gstin')) <p class="help-block">{{ $errors->first('offline_gstin') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('offline_cin')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="offline_cin">CIN:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="offline_cin" name="offline_cin" value="{{($receipt->offline_cin)?$receipt->offline_cin:null}}" placeholder="CIN" >
        @if($errors->has('offline_cin')) <p class="help-block">{{ $errors->first('offline_cin') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('offline_pan')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="offline_pan">PAN:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="offline_pan" name="offline_pan" value="{{($receipt->offline_pan)?$receipt->offline_pan:null}}" placeholder="PAN" >
        @if($errors->has('offline_pan')) <p class="help-block">{{ $errors->first('offline_pan') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="is_offline_gst_applied">Receipt with GST:</label>
      <div class="col-sm-3">
        @if(isset($receipt->id))
          <input type="radio" name="is_offline_gst_applied" value="1" @if(1 == $receipt->is_offline_gst_applied) checked @endif> Yes
          <input type="radio" name="is_offline_gst_applied" value="0" @if(0 == $receipt->is_offline_gst_applied) checked @endif> No
        @else
          <input type="radio" name="is_offline_gst_applied" value="1"> Yes
          <input type="radio" name="is_offline_gst_applied" value="0" checked> No
        @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('hsn_sac')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="hsn_sac">HSN/SAC:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="hsn_sac" name="hsn_sac" value="{{($receipt->hsn_sac)?$receipt->hsn_sac:null}}" placeholder="HSN/SAC">
        @if($errors->has('hsn_sac')) <p class="help-block">{{ $errors->first('hsn_sac') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <div align="center"><b>Online Receipt Details</b></div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="is_same_details">Same As Offline Receipt Details:</label>
      <div class="col-sm-3">
        @if(isset($receipt->id))
          <input type="radio" name="is_same_details" value="1" @if(1 == $receipt->is_same_details) checked @endif onClick="toggleDetails(this.value)"> Yes
          <input type="radio" name="is_same_details" value="0" @if(0 == $receipt->is_same_details) checked @endif onClick="toggleDetails(this.value)"> No
        @else
          <input type="radio" name="is_same_details" value="1" checked onClick="toggleDetails(this.value)"> Yes
          <input type="radio" name="is_same_details" value="0" onClick="toggleDetails(this.value)"> No
        @endif
      </div>
    </div>
    @if(isset($receipt->id) && 0 == $receipt->is_same_details)
      <div class="" id="onlineRecord">
    @else
      <div class="hide" id="onlineRecord">
    @endif
      <div class="form-group row  @if ($errors->has('online_receipt_by')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="online_receipt_by">Receipt By:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" id="online_receipt_by" name="online_receipt_by" value="{{($receipt->online_receipt_by)?$receipt->online_receipt_by:null}}" placeholder="Online Receipt By" >
          @if($errors->has('online_receipt_by')) <p class="help-block">{{ $errors->first('online_receipt_by') }}</p> @endif
        </div>
      </div>
      <div class="form-group row  @if ($errors->has('online_address')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="online_address">Address:</label>
        <div class="col-sm-3">
          <textarea class="form-control" id="online_address" name="online_address"  >{{($receipt->online_address)?$receipt->online_address:null}}</textarea>
          @if($errors->has('online_address')) <p class="help-block">{{ $errors->first('online_address') }}</p> @endif
        </div>
      </div>
      <div class="form-group row  @if ($errors->has('online_gstin')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="online_gstin">GSTIN:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" id="online_gstin" name="online_gstin" value="{{($receipt->online_gstin)?$receipt->online_gstin:null}}" placeholder="GSTIN" >
          @if($errors->has('online_gstin')) <p class="help-block">{{ $errors->first('online_gstin') }}</p> @endif
        </div>
      </div>
      <div class="form-group row  @if ($errors->has('online_cin')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="online_cin">CIN:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" id="online_cin" name="online_cin" value="{{($receipt->online_cin)?$receipt->online_cin:null}}" placeholder="CIN" >
          @if($errors->has('online_cin')) <p class="help-block">{{ $errors->first('online_cin') }}</p> @endif
        </div>
      </div>
      <div class="form-group row  @if ($errors->has('online_pan')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="online_pan">PAN:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" id="online_pan" name="online_pan" value="{{($receipt->online_pan)?$receipt->online_pan:null}}" placeholder="PAN" >
          @if($errors->has('online_pan')) <p class="help-block">{{ $errors->first('online_pan') }}</p> @endif
        </div>
      </div>
      <div class="form-group row">
        <label class="col-sm-2 col-form-label" for="is_online_gst_applied">Receipt with GST:</label>
        <div class="col-sm-3">
          @if(isset($receipt->id))
            <input type="radio" name="is_online_gst_applied" value="1" @if(1 == $receipt->is_online_gst_applied) checked @endif> Yes
            <input type="radio" name="is_online_gst_applied" value="0" @if(0 == $receipt->is_online_gst_applied) checked @endif> No
          @else
            <input type="radio" name="is_online_gst_applied" value="1"> Yes
            <input type="radio" name="is_online_gst_applied" value="0" checked> No
          @endif
        </div>
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
      </div>
    </div>
    </form>
  </div>
<script type="text/javascript">
  function toggleDetails(val){
    if(1 == val){
      $('#onlineRecord').addClass('hide');
      $('#online_receipt_by').prop('required',false);
      $('#online_address').prop('required',false);
    } else {
      $('#onlineRecord').removeClass('hide');
      $('#online_receipt_by').prop('required',true);
      $('#online_address').prop('required',true);
    }
  }
</script>
@stop