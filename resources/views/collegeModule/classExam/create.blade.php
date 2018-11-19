@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Class Exam </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Academic </li>
      <li class="active"> Manage Class Exam </li>
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
  @if(isset($collegeClassExam->id))
    <form action="{{url('college/'.Session::get('college_user_url').'/updateCollegeClassExam')}}" method="POST" id="submitForm">
      {{method_field('PUT')}}
      <input type="hidden" id="exam_id" name="exam_id" value="{{$collegeClassExam->id}}">
  @else
      <form action="{{url('college/'.Session::get('college_user_url').'/createCollegeClassExam')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('subject')) has-error @endif">
    <label class="col-sm-2 col-form-label">Subject:</label>
    <div class="col-sm-3">
    	@if(isset($collegeClassExam->id))
	    	@if(count($subjects) > 0)
	            @foreach($subjects as $subject)
	            	@if($collegeClassExam->college_subject_id == $subject->id)
	            		<input type="text" class="form-control" name="subject_text" value="{{$subject->name}}" readonly>
	            		<input type="hidden" id="subject" name="subject" value="{{$subject->id}}">
	              	@endif
	            @endforeach
	        @endif
        @else
	  		<select class="form-control" id="subject" name="subject" required title="Subject" onChange="selectDepartemnts(this);">
	          <option value="">Select Subject</option>
	          @if(count($subjects) > 0)
	            @foreach($subjects as $subject)
	                <option value="{{$subject->id}}">{{$subject->name}}</option>
	            @endforeach
	          @endif
	        </select>
	        @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
       	@endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('department')) has-error @endif">
    <label class="col-sm-2 col-form-label">Departments:</label>
    <div class="col-sm-3">
    	@if(isset($collegeClassExam->id))
    		@if(count($collegeDepts) > 0)
            @php
              $examDepts = explode(',',$collegeClassExam->college_dept_ids);
            @endphp
	            @foreach($collegeDepts as $collegeDept)
	              	@if(in_array($collegeDept->id,$examDepts))
	              		<input type="text" class="form-control" name="department_text" value="{{$collegeDept->name}}" readonly>
                    <input type="hidden" id="department" name="department[]" value="{{$collegeDept->id}}">
                  @endif
	            @endforeach
	        @endif
        @else
      	<select class="form-control" id="department" name="department[]" required title="Department" multiple>
        	<option value="">Select Departments</option>
	        @if(count($collegeDepts) > 0)
	            @foreach($collegeDepts as $collegeDept)
                	<option value="{{$collegeDept->id}}">{{$collegeDept->name}}</option>
	            @endforeach
	        @endif
      	</select>
      		@if($errors->has('department')) <p class="help-block">{{ $errors->first('department') }}</p> @endif
	    @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('year')) has-error @endif">
    <label class="col-sm-2 col-form-label">Years:</label>
    <div class="col-sm-3">
      @if(isset($collegeClassExam->id))
        @if(count($years) > 0)
          @php
            $examYears = explode(',',$collegeClassExam->years);
          @endphp
          @foreach($years as $year)
              @if(1 == $year && in_array($year,$examYears))
                  <input type="text" class="form-control" name="year_text" value="First" readonly>
                  <input type="hidden" id="year" name="year[]" value="{{$year}}">
              @endif
              @if(2 == $year && in_array($year,$examYears))
                  <input type="text" class="form-control" name="year_text" value="Second" readonly>
                  <input type="hidden" id="year" name="year[]" value="{{$year}}">
              @endif
              @if(3 == $year && in_array($year,$examYears))
                  <input type="text" class="form-control" name="year_text" value="Third" readonly>
                  <input type="hidden" id="year" name="year[]" value="{{$year}}">
              @endif
              @if(4 == $year && in_array($year,$examYears))
                  <input type="text" class="form-control" name="year_text" value="Fourth" readonly>
                  <input type="hidden" id="year" name="year[]" value="{{$year}}">
              @endif
          @endforeach
        @endif
      @else
          <select class="form-control" id="year" name="year[]" required title="Year" multiple>
            <option value="">Select Years</option>
            @if(count($years) > 0)
                @foreach($years as $year)
                  @if(1 == $year)
                    <option value="1" @if(1 == $collegeClassExam->year) selected @endif> First</option>
                  @endif
                  @if(2 == $year)
                    <option value="2" @if(2 == $collegeClassExam->year) selected @endif> Second</option>
                  @endif
                  @if(3 == $year)
                    <option value="3" @if(3 == $collegeClassExam->year) selected @endif> Third</option>
                  @endif
                  @if(4 == $year)
                    <option value="4" @if(4 == $collegeClassExam->year) selected @endif> Fourth</option>
                  @endif
                @endforeach
            @endif
          </select>
          @if($errors->has('year')) <p class="help-block">{{ $errors->first('year') }}</p> @endif
        @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('topic')) has-error @endif">
    <label for="topic" class="col-sm-2 col-form-label">Topic:</label>
    <div class="col-sm-3">
      @if(isset($collegeClassExam->id))
        <input type="text" class="form-control" id="topic" name="topic" value="{{$collegeClassExam->topic}}" required="true">
      @else
        <input type="text" class="form-control" id="topic" name="topic" value="" required="true">
      @endif
      @if($errors->has('topic')) <p class="help-block">{{ $errors->first('topic') }}</p> @endif
    </div>
  </div>
      <div class="form-group row">
      <label for="" class="col-sm-2 col-form-label">Date:</label>
      <div class="col-sm-3">
        <input type="text"  class="form-control" name="date" id="date" @if(isset($collegeClassExam->id)) value="{{$collegeClassExam->date}}" @endif placeholder="Date" required>
      </div>
      <script type="text/javascript">
          $(function () {
              $('#date').datetimepicker({
                format: 'YYYY-MM-DD'
              });
          });
      </script>
    </div>
    <div class="form-group row">
      <label for="" class="col-sm-2 col-form-label">From Time:</label>
      <div class="col-sm-3">
        <input type="text"  class="form-control" name="from_time" id="from_time" @if(isset($collegeClassExam->id)) value="{{$collegeClassExam->from_time}}" @endif placeholder="From Time" required>
      </div>
      <script type="text/javascript">
          $(function () {
              $('#from_time').datetimepicker({
                format: 'LT'
              });
          });
      </script>
    </div>
    <div class="form-group row">
      <label for="" class="col-sm-2 col-form-label">To Time:</label>
      <div class="col-sm-3">
        <input type="text"  class="form-control" name="to_time" id="to_time" @if(isset($collegeClassExam->id)) value="{{$collegeClassExam->to_time}}" @endif placeholder="To Time" required>
      </div>
      <script type="text/javascript">
          $(function () {
              $('#to_time').datetimepicker({
                format: 'LT'
              });
          });
      </script>
    </div>
  <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
      </div>
    </div>
  </div>
</form>
<script type="text/javascript">
  function selectDepartemnts(ele){
    var subject = $(ele).val();
    if(subject){
      $.ajax({
        method: "POST",
        url: "{{url('getCollegeDepartmentsBySubjectId')}}",
        data: {subject_id:subject}
      })
      .done(function( msg ) {
        selectDept = document.getElementById('department');
        selectDept.innerHTML = '';
        var optDept = document.createElement('option');
        optDept.value = '';
        optDept.innerHTML = 'Select Departments';
        selectDept.appendChild(optDept);
        if( 0 < msg['collegeDepts'].length){
          $.each(msg['collegeDepts'], function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              selectDept.appendChild(opt);
          });
        }

        selectYears = document.getElementById('year');
        selectYears.innerHTML = '';
        var optYears = document.createElement('option');
        optYears.value = '';
        optYears.innerHTML = 'Select Years';
        selectYears.appendChild(optYears);
        if( 0 < msg['years'].length){
          $.each(msg['years'], function(idx, year) {
              var opt = document.createElement('option');
              opt.value = year;
              if(1 == year){
                opt.innerHTML = 'First';
              } else if(2 == year){
                opt.innerHTML = 'Second';
              } else if(3 == year){
                opt.innerHTML = 'Third';
              } else if(4 == year){
                opt.innerHTML = 'Fourth';
              }
              selectYears.appendChild(opt);
          });
        }
      });
    }
  }
</script>
@stop