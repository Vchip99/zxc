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
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container admin_div">
    <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
    <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  @if(isset($payment->id))
    <form action="{{url('updateOfflinePayment')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="payment_id" value="{{$payment->id}}">
  @else
   <form action="{{url('createOfflinePayment')}}" method="POST">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('batch')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="batch">Batch Name:</label>
      <div class="col-sm-3">
        @if(isset($payment->id))
          @if(count($batches) > 0)
            @foreach($batches as $batch)
              @if($batch->id == $payment->client_batch_id)
                <input type="text" class="form-control" name="batch_text" id="batch" value="{{$batch->name}}" readonly>
                <input type="hidden" name="batch" value="{{$batch->id}}">
              @endif
            @endforeach
          @endif
        @else
          <select class="form-control" name="batch" id="batch" required onClick="selectUser(this);">
            <option value="">Select Batch</option>
            @if(count($batches) > 0)
              @foreach($batches as $batch)
                <option value="{{$batch->id}}">{{$batch->name}}</option>
              @endforeach
            @endif
          </select>
        @endif
        @if($errors->has('batch')) <p class="help-block">{{ $errors->first('batch') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('user')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="user">User Name:</label>
      <div class="col-sm-3">
        @if(isset($payment->id))
          @if(count($users) > 0)
            @foreach($users as $user)
              @if($user->id == $payment->clientuser_id)
                <input type="text" class="form-control" name="user_text" id="user" value="{{$user->name}}" readonly>
                <input type="hidden" name="user" value="{{$user->id}}">
              @endif
            @endforeach
          @endif
        @else
          <select class="form-control" name="user" id="user" required onClick="selectUserTotal(this);">
            <option value="">Select User</option>
          </select>
        @endif
        @if($errors->has('user')) <p class="help-block">{{ $errors->first('user') }}</p> @endif
      </div>
    </div>
    <span id="note" class="hide" style="color: blue;"></span>
    <div class="form-group row  @if ($errors->has('amount')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="amount">Amount:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="amount" name="amount" value="{{($payment)?$payment->amount:null}}" placeholder="Amount" required="true">
        @if($errors->has('amount')) <p class="help-block">{{ $errors->first('amount') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="comment">Comment:</label>
      <div class="col-sm-3">
        <textarea class="form-control" id="comment" name="comment" placeholder="Comment">{{($payment)?$payment->comment:null}}</textarea>
      </div>
    </div>
    <div class="form-group row">
      <label for="date_to_active" class="col-sm-2 col-form-label">Due Date:</label>
      <div class="col-sm-3">
        <input type="text"  class="form-control" name="due_date" id="due_date" @if(isset($payment->id)) value="{{$payment->due_date}}" @endif placeholder="Due Date">
      </div>
      <script type="text/javascript">
          $(function () {
              $('#due_date').datetimepicker({
                format: 'YYYY-MM-DD'
              });
          });
      </script>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
      </div>
    </div>
    </form>
  </div>
  <script type="text/javascript">
    $('#batch').focus();

    function selectUser(ele){
      var batch = $(ele).val();
      if( batch > 0 ){
        $.ajax({
            method: "POST",
            url: "{{url('getBatchUsersByBatchId')}}",
            data: {batch_id:batch}
        })
        .done(function( users ) {
          select = document.getElementById('user');
          select.innerHTML = '';
          var opt = document.createElement('option');
          opt.value = '';
          opt.innerHTML = 'Select user';
          select.appendChild(opt);
          if(users.length > 0){
            $.each(users, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              select.appendChild(opt);
            });
          }
        });
      }
    }
    function selectUserTotal(ele){
      var user = $(ele).val();
      var batch = document.getElementById('batch').value;
      if( user > 0 && batch > 0 ){
        $.ajax({
            method: "POST",
            url: "{{url('getTotalPaidByBatchIdByUserId')}}",
            data: {batch_id:batch,user_id:user}
        })
        .done(function( result ) {
          console.log(result);
          if(result['paid'] && result['total']){
            document.getElementById('note').innerHTML = 'Total Paid-'+result['total']+':'+result['paid'];
            $('#note').removeClass('hide');
          } else {
            $('#note').addClass('hide');
          }
        });
      }
    }
  </script>
@stop