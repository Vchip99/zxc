@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
  <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
  <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Attendance </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-address-book"></i> Batch </li>
      <li class="active"> Manage Attendance </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container ">
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
   <form action="{{url('markAttendance')}}" method="POST">
    {{ csrf_field() }}
      <div class="row" >
          <div class="form-group">
              <div class="col-md-3">
                  <div style="margin-bottom: 10px">
                    <input type="text"  class="form-control" name="attendance_date" id="attendance_date" >
                  </div>
              </div>
              <div class="col-md-3">
                  <div style="margin-bottom: 10px">
                      <select class="form-control" name="batch" id="batch" onChange="selectBatchStudent(this);">
                          <option value="">Select Batch</option>
                          @if(count($batches) > 0)
                              @foreach($batches as $batch)
                                  <option value="{{$batch->id}}">{{$batch->name}}</option>
                              @endforeach
                          @endif
                      </select>
                  </div>
              </div>
          </div>
      </div>
      <div class="row" >
        <div class="form-group">
          <div class="col-md-12">
              <div style="margin-bottom: 10px">
                <input type="radio" name="mark_attendance" value="1" checked> Mark Attendance As Present &nbsp;
                <input type="radio" name="mark_attendance" value="0"> Mark Attendance As Absent
              </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12" id="all-result">
          <div class="panel panel-info">
            <div class="panel-heading text-center">
              <span class="">Students</span>
              <span class="pull-right">Toggle All - <input type="checkbox" onClick="toggleAll(this);"></span>
            </div>
            <div class="panel-body">
              <table  class="" id="">
                <thead>
                  <tr>
                    <th>Sr. No.</th>
                    <th>Name</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody id="client_batch_users" class="">
                </tbody>
              </table>
            </div>
          </div>
          <input type="hidden" name="all_users" id="all_users">
          <div class="form-group">
            <div class="col-md-12">
              <button type="submit" class="btn btn-primary" style="float: right;width: 90px !important;">Submit</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
<script type="text/javascript">
  $(function () {
      var currentDate = "{{ date('Y-m-d')}}";
      $('#attendance_date').datetimepicker({
        defaultDate: currentDate,
        format: 'YYYY-MM-DD'
      }).on('dp.change', function (e) {
        document.getElementById('client_batch_users').innerHTML = '';
        select = document.getElementById('batch').selectedIndex = '';
    });
  });

  function toggleAll(ele){
    if(true == $(ele).prop('checked')){
      $('input[id^=student_]').prop('checked', 'checked');
    } else {
      $('input[id^=student_]').prop('checked', '');
    }
  }

  function selectBatchStudent(ele){
    var batchId = parseInt($(ele).val());
    var date = document.getElementById('attendance_date').value;
    var currentToken = $('meta[name="csrf-token"]').attr('content');
    if(batchId){
      $.ajax({
          method:'POST',
          url: "{{url('getBatchStudentAttendancebyBatchId')}}",
          data:{_token:currentToken,batch_id:batchId, attendance_date:date}
      }).done(function( result ) {
          var users = document.getElementById('client_batch_users');
          users.innerHTML = '';
          var allUsers = document.getElementById('all_users');
          allUsers.value = '';
          if(result['batchUsers']){
            $.each(result['batchUsers'], function(idx, obj) {
              if(result['batchAttendance'].indexOf(String(obj.id)) > -1){
                users.innerHTML +='<tr class="student" id="div_student_'+obj.id+'" ><td>'+(idx + 1)+'</td><td>'+obj.name+'</td><td><input type="checkbox" name="students[]" id="student_'+obj.id+'" value="'+obj.id+'" checked="checked"></td></tr>';
              } else {
                users.innerHTML +='<tr class="student" id="div_student_'+obj.id+'" ><td>'+(idx + 1)+'</td><td>'+obj.name+'</td><td><input type="checkbox" name="students[]" id="student_'+obj.id+'" value="'+obj.id+'"></td></tr>';
              }
              if(0 == idx){
                allUsers.value = obj.id;
              } else {
                allUsers.value += ','+ obj.id;
              }
            });
          }
      });
    }
  }
</script>
@stop