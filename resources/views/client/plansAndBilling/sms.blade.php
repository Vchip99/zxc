@extends('client.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Purchase Sms  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-inr"></i> Plans & Billing </li>
      <li class="active"> Purchase Sms </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container admin_div">
    <div class="row ">
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
      <form action="{{url('clientPurchaseSms')}}" method="POST">
        {{ csrf_field() }}
        <div class="form-group row"><label> Please purchase sms in thousands. Price is 150 Rs/thousands sms.</label></div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label" for="category">No of Sms:</label>
          <div class="col-sm-3">
            <input type="number" id="sms_count" name="sms_count" value="1000" min="1000" step="1000" class="form-control" onchange="showTotal(this);" required>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Total</label>
          <div class="col-sm-3">
            <input type="text" name="total" class="form-control" id="total" value="150" readonly="true" required>
          </div>
        </div>
        <div class="form-group row">
          <div class="offset-sm-2 col-sm-3" title="Submit">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <script type="text/javascript">
    function showTotal(ele){
      var smsCount = $(ele).val();
      var price = 150;
      document.getElementById('total').value = parseInt(price) * parseInt(smsCount/1000);
    }
  </script>
@stop