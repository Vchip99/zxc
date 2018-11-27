@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Individual Messages </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-envelope"></i> Event/Message </li>
      <li class="active"> Individual Messages </li>
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
   <form action="{{url('college/'.Session::get('college_user_url').'/createIndividualMessage')}}" method="POST">
    {{ csrf_field() }}
      <div class="row">
        <div class="form-group">
            <div class="col-md-3">
                <div style="margin-bottom: 10px">
                    <select class="form-control" name="department" id="department" @if(is_object($individualMessage)) disabled @endif>
                        <option value="">Select Department</option>
                        @if(count($departments) > 0)
                            @foreach($departments as $department)
                              @if(is_object($individualMessage) && $individualMessage->college_dept_id == $department->id)
                                <option value="{{$department->id}}" selected>{{$department->name}}</option>
                              @else
                                <option value="{{$department->id}}">{{$department->name}}</option>
                              @endif
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div style="margin-bottom: 10px">
                    <select class="form-control" name="year" id="year" onChange="selectCollegeStudent(this);" @if(is_object($individualMessage)) disabled @endif>
                        <option value="">Select Year</option>
                        @if(count($years) > 0)
                            @foreach($years as $year)
                              @if(is_object($individualMessage) && $individualMessage->college_dept_id == $year)
                                <option value="{{$year}}" selected>{{$year}}</option>
                              @else
                                <option value="{{$year}}">{{$year}}</option>
                              @endif
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            @if(is_object($individualMessage))
              <a class="btn btn-primary" href="{{url('college/'.Session::get('college_user_url').'/manageIndividualMessage')}}" style="float: right; width: 50px;">Back</a>
            @endif
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12" id="all-result">
          <div class="panel panel-info">
            <div class="panel-heading text-center">
              <span class="">Students</span>
            </div>
            <div class="panel-body">
              <table  class="" id="">
                <thead>
                  <tr>
                    <th>Sr. No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                  </tr>
                </thead>
                <tbody id="client_individual_messages" >
                  @if(count($students) > 0)
                    @php
                      $index = 1;
                    @endphp
                    @foreach($students as $student)
                    <tr>
                      <td>{{ $index++}}</td>
                      <td>{{$student->name}}</td>
                      <td>{{$student->email}}</td>
                      <td style="width: 100%;">
                        @if(isset($messages[$student->id]))
                          <input type="text" name="{{$student->id}}" id="student_{{$student->id}}" value="{{$messages[$student->id]}}" style="width: 100%;" readonly>
                        @else
                          <input type="text" name="{{$student->id}}" id="student_{{$student->id}}" value="" style="width: 100%;" readonly>
                        @endif
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
              @if(!is_object($individualMessage))
                <button type="submit" class="btn btn-primary" style="float: right;width: 90px !important;">Submit</button>
              @endif
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
<script type="text/javascript">

  function selectCollegeStudent(ele){
    var yearId = parseInt($(ele).val());
    var departmentId = parseInt($('#department').val());
    var currentToken = $('meta[name="csrf-token"]').attr('content');
    if(departmentId && yearId){
      $.ajax({
          method:'POST',
          url: "{{url('getCollegeStudentsByDeptIdByYear')}}",
          data:{_token:currentToken,department:departmentId,year:yearId}
      }).done(function( result ) {
          var users = document.getElementById('client_individual_messages');
          users.innerHTML = '';
          if(result.length){
            $.each(result, function(idx, obj) {
              users.innerHTML +='<tr class="student" id="div_student_'+obj.id+'" ><td>'+(idx + 1)+'</td><td>'+obj.name+'</td><td>'+obj.email+'</td><td style="width: 100%;"><input type="text" name="'+obj.id+'" id="student_'+obj.id+'" value="" style="width: 100%;"></td></tr>';
            });
          } else {
            users.innerHTML = '<tr class="student"><td colspan="4">No Users!</td></tr>';
          }
      });
    }
  }
</script>
@stop