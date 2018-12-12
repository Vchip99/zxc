@extends('admin.master')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Admin Receipt </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-inr"></i> Payments </li>
      <li class="active"> Admin Receipt </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <div class="container admin_div">
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
    @if(isset($receipt->id))
      <form action="{{url('admin/updateReceipt')}}" method="POST">
      {{ method_field('PUT') }}
      <input type="hidden" name="receipt_id" value="{{$receipt->id}}">
    @else
      <form action="{{url('admin/createReceipt')}}" method="POST">
    @endif
      {{ csrf_field() }}
      <div class="form-group row">
        <div align="center"><b>Receipt Details</b></div>
      </div>
      <div class="form-group row  @if ($errors->has('receipt_by')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="receipt_by">Receipt By:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" id="receipt_by" name="receipt_by" value="{{($receipt->receipt_by)?$receipt->receipt_by:null}}" placeholder="Receipt By" required>
          @if($errors->has('receipt_by')) <p class="help-block">{{ $errors->first('receipt_by') }}</p> @endif
        </div>
      </div>
      <div class="form-group row  @if ($errors->has('address')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="address">Address:</label>
        <div class="col-sm-3">
          <textarea class="form-control" id="address" name="address" required>{{($receipt->address)?$receipt->address:null}}</textarea>
          @if($errors->has('address')) <p class="help-block">{{ $errors->first('address') }}</p> @endif
        </div>
      </div>
      <div class="form-group row  @if ($errors->has('gstin')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="gstin">GSTIN:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" id="gstin" name="gstin" value="{{($receipt->gstin)?$receipt->gstin:null}}" placeholder="GSTIN" >
          @if($errors->has('gstin')) <p class="help-block">{{ $errors->first('gstin') }}</p> @endif
        </div>
      </div>
      <div class="form-group row  @if ($errors->has('cin')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="cin">CIN:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" id="cin" name="cin" value="{{($receipt->cin)?$receipt->cin:null}}" placeholder="CIN" >
          @if($errors->has('cin')) <p class="help-block">{{ $errors->first('cin') }}</p> @endif
        </div>
      </div>
      <div class="form-group row  @if ($errors->has('pan')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="pan">PAN:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" id="pan" name="pan" value="{{($receipt->pan)?$receipt->pan:null}}" placeholder="PAN" >
          @if($errors->has('pan')) <p class="help-block">{{ $errors->first('pan') }}</p> @endif
        </div>
      </div>
      <div class="form-group row">
        <label class="col-sm-2 col-form-label" for="is_gst_test_applied">Test receipt with GST:</label>
        <div class="col-sm-3">
          @if(isset($receipt->id))
            <input type="radio" name="is_gst_test_applied" value="1" @if(1 == $receipt->is_gst_test_applied) checked @endif> Yes
            <input type="radio" name="is_gst_test_applied" value="0" @if(0 == $receipt->is_gst_test_applied) checked @endif> No
          @else
            <input type="radio" name="is_gst_test_applied" value="1"> Yes
            <input type="radio" name="is_gst_test_applied" value="0" checked> No
          @endif
        </div>
      </div>
      <div class="form-group row">
        <label class="col-sm-2 col-form-label" for="is_gst_course_applied">Course receipt with GST:</label>
        <div class="col-sm-3">
          @if(isset($receipt->id))
            <input type="radio" name="is_gst_course_applied" value="1" @if(1 == $receipt->is_gst_course_applied) checked @endif> Yes
            <input type="radio" name="is_gst_course_applied" value="0" @if(0 == $receipt->is_gst_course_applied) checked @endif> No
          @else
            <input type="radio" name="is_gst_course_applied" value="1"> Yes
            <input type="radio" name="is_gst_course_applied" value="0" checked> No
          @endif
        </div>
      </div>
      <div class="form-group row">
        <label class="col-sm-2 col-form-label" for="is_gst_vkit_applied">Vkit receipt with GST:</label>
        <div class="col-sm-3">
          @if(isset($receipt->id))
            <input type="radio" name="is_gst_vkit_applied" value="1" @if(1 == $receipt->is_gst_vkit_applied) checked @endif> Yes
            <input type="radio" name="is_gst_vkit_applied" value="0" @if(0 == $receipt->is_gst_vkit_applied) checked @endif> No
          @else
            <input type="radio" name="is_gst_vkit_applied" value="1"> Yes
            <input type="radio" name="is_gst_vkit_applied" value="0" checked> No
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
        <div class="offset-sm-2 col-sm-3" title="Submit">
          <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
        </div>
      </div>
    </form>
  </div>
@stop