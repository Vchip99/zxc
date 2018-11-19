@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Holiday  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Academic </li>
      <li class="active"> Manage Holiday </li>
    </ol>
  </section>
  <style type="text/css">
    .btn-primary{
      width: 50px;
    }
    .glyphicon {
      font-family: 'Glyphicons Halflings' !important;
    }
  </style>
@stop
@section('dashboard_content')
  <div class="container admin_div">
    <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
    <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  @if(isset($collegeHoliday->id))
    <form action="{{url('college/'.Session::get('college_user_url').'/updateCollegeHoliday')}}" method="POST" id="submitForm">
      {{method_field('PUT')}}
      <input type="hidden" id="holiday_id" name="holiday_id" value="{{$collegeHoliday->id}}">
  @else
      <form action="{{url('college/'.Session::get('college_user_url').'/createCollegeHoliday')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row">
      <label for="" class="col-sm-2 col-form-label">Date:</label>
      <div class="col-sm-3">
        <input type="text"  class="form-control" name="date" id="date" @if(isset($collegeHoliday->id)) value="{{$collegeHoliday->date}}" @endif placeholder="Date" required>
      </div>
      <script type="text/javascript">
          $(function () {
              $('#date').datetimepicker({
                format: 'YYYY-MM-DD'
              });
          });
      </script>
    </div>
    <div class="form-group row @if ($errors->has('message')) has-error @endif">
      <label for="message" class="col-sm-2 col-form-label">Message:</label>
      <div class="col-sm-3">
        @if(isset($collegeHoliday->id))
          <textarea class="form-control" id="message" name="message" required cols="5" rows="10">{{$collegeHoliday->note}}</textarea>
        @else
          <textarea class="form-control" id="message" name="message" required cols="5" rows="5"></textarea>
        @endif
        @if($errors->has('message')) <p class="help-block">{{ $errors->first('message') }}</p> @endif
      </div>
    </div>
    <span class="hide" id="ttError" style="color: white;">Holiday exist for selected date.Please select another date.</span>
    <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          @if(isset($collegeHoliday->id) && $collegeHoliday->created_by == Auth::user()->id)
            <button type="button" class="btn btn-primary" style="width: 90px !important;"  onclick="searchHoliday();">Submit</button>
          @elseif(empty($collegeHoliday->id))
            <button type="button" class="btn btn-primary" style="width: 90px !important;"  onclick="searchHoliday();">Submit</button>
          @else
            <a href="{{url('college/'.Session::get('college_user_url').'/manageCollegeHoliday')}}" class="btn btn-primary" style="width: 90px !important;">Back</a>
          @endif
        </div>
      </div>
    </div>
</form>
<script type="text/javascript">
  function searchHoliday(){
    var date = document.getElementById('date').value;
    var message = document.getElementById('message').value;
    if(document.getElementById('holiday_id')){
      var holidayId = document.getElementById('holiday_id').value;
    } else {
      var holidayId = 0;
    }
    if(date && message){
      $.ajax({
        method:'POST',
        url: "{{url('isCollegeHolidayExist')}}",
        data:{date:date,holiday_id:holidayId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('ttError').classList.remove('hide');
          document.getElementById('ttError').classList.add('has-error');
        } else {
          document.getElementById('ttError').classList.add('hide');
          document.getElementById('ttError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    } else if(!date){
      alert('please select date.');
    } else if(!message){
      alert('please enter message.');
    }
  }
</script>
@stop