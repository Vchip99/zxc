@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Batch Student </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-address-book"></i> Batch </li>
      <li class="active"> Batch Student </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container ">
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
  @if(count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
  @endif
   <form action="{{url('associateBatchStudents')}}" method="POST">
    {{ csrf_field() }}
      <div class="row" >
          <div class="form-group">
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
              <div class="col-md-3">
                  <div style="margin-bottom: 10px">
                      <input type="text" name="student" id="student" class="form-control"  placeholder="search student" onkeyup="searchStudent(this.value);">
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
                  @if(count($students) > 0)
                    @foreach($students as $index => $student)
                      <tr class="student" id="div_student_{{$student->id}}" >
                        <td> {{ $index + 1 }} </td>
                        <td>{{$student->name}}</td>
                        <td>
                            <input type="checkbox" name="students[]" id="student_{{$student->id}}" value="{{$student->id}}">
                        </td>
                      </tr>
                    @endforeach
                  @endif
                  </tbody>
              </table>
            </div>
          </div>
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
  function toggleAll(ele){
    if(true == $(ele).prop('checked')){
      $('input[id^=student_]').prop('checked', 'checked');
    } else {
      $('input[id^=student_]').prop('checked', '');
    }
  }
  function selectBatchStudent(ele){
    var batchId = parseInt($(ele).val());
    var currentToken = $('meta[name="csrf-token"]').attr('content');
    $('input[type=checkbox]').prop('checked', '');
    if(batchId){
      $.ajax({
          method:'POST',
          url: "{{url('getBatchStudentsIdsbyBatchId')}}",
          data:{_token:currentToken,batch_id:batchId}
      }).done(function( batch ) {
          if(batch){
            var students = batch.student_ids.split(',');
            $.each(students, function(idx, id) {
                $('#student_'+id).prop('checked', 'checked');
            });
          }
      });
    }
  }
  function searchStudent(student){
    var currentToken = $('meta[name="csrf-token"]').attr('content');
    if(student.length > 0){
      $.ajax({
          method:'POST',
          url: "{{url('searchClientStudent')}}",
          data:{_token:currentToken,student:student}
      }).done(function( students ) {
          $('tr.student').addClass('hide');
          if(students.length > 0){
              $.each(students, function(idx,obj){
                $('#div_student_'+obj.id).removeClass('hide');
              })
          }
      });
    } else if( 0 == student.length) {
      $('tr.student').removeClass('hide');
    }
  }
</script>
@stop