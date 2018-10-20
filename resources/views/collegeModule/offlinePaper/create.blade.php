@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Offline Paper  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Academic </li>
      <li class="active"> Manage Offline Paper </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container admin_div">
  @if(isset($paper->id))
    <form action="{{url('college/'.Session::get('college_user_url').'/updateCollegeOfflinePaper')}}" method="POST" id="submitForm">
      {{method_field('PUT')}}
      <input type="hidden" id="paper_id" name="paper_id" value="{{$paper->id}}">
  @else
      <form action="{{url('college/'.Session::get('college_user_url').'/createCollegeOfflinePaper')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('subject')) has-error @endif">
    <label class="col-sm-2 col-form-label">Subject:</label>
    <div class="col-sm-3">
    	@if(isset($paper->id))
	    	@if(count($subjects) > 0)
	            @foreach($subjects as $subject)
	            	@if($paper->college_subject_id == $subject->id)
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
    <label class="col-sm-2 col-form-label">Department:</label>
    <div class="col-sm-3">
    	@if(isset($paper->id))
    		@if(count($collegeDepts) > 0)
	            @foreach($collegeDepts as $collegeDept)
	              	@if($paper->college_dept_id == $collegeDept->id)
	              		<input type="text" class="form-control" name="department_text" value="{{$collegeDept->name}}" readonly>
	            		<input type="hidden" id="department" name="department" value="{{$collegeDept->id}}">
	              	@endif
	            @endforeach
	        @endif
        @else
      	<select class="form-control" id="department" name="department" required title="Department">
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
    <label class="col-sm-2 col-form-label">Year:</label>
    <div class="col-sm-3">
    	@if(isset($paper->id))
    		@if(count($years) > 0)
	      		@foreach($years as $year)
		            @if(1 == $year)
		            	@if(1 == $paper->year)
			            	<input type="text" class="form-control" name="year_text" value="First" readonly>
		            		<input type="hidden" id="year" name="year" value="{{$paper->year}}">
		            	@endif
		            @endif
		            @if(2 == $year)
		            	@if(2 == $paper->year)
			            	<input type="text" class="form-control" name="year_text" value="Second" readonly>
		            		<input type="hidden" id="year" name="year" value="{{$paper->year}}">
		            	@endif
		            @endif
		            @if(3 == $year)
		            	@if(3 == $paper->year)
			            	<input type="text" class="form-control" name="year_text" value="Third" readonly>
		            		<input type="hidden" id="year" name="year" value="{{$paper->year}}">
		            	@endif
		            @endif
		            @if(4 == $year)
		            	@if(4 == $paper->year)
			            	<input type="text" class="form-control" name="year_text" value="Fourth" readonly>
		            		<input type="hidden" id="year" name="year" value="{{$paper->year}}">
		            	@endif
		            @endif
	      		@endforeach
	    	@endif
    	@else
	      	<select class="form-control" id="year" name="year" required title="Year">
	        	<option value="">Select Year</option>
	        	@if(count($years) > 0)
	          		@foreach($years as $year)
			            @if(1 == $year)
			              <option value="1" @if(1 == $paper->year) selected @endif> First</option>
			            @endif
			            @if(2 == $year)
			              <option value="2" @if(2 == $paper->year) selected @endif> Second</option>
			            @endif
			            @if(3 == $year)
			              <option value="3" @if(3 == $paper->year) selected @endif> Third</option>
			            @endif
			            @if(4 == $year)
			              <option value="4" @if(4 == $paper->year) selected @endif> Fourth</option>
			            @endif
	          		@endforeach
	        	@endif
	      	</select>
	      	@if($errors->has('year')) <p class="help-block">{{ $errors->first('year') }}</p> @endif
      	@endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('paper')) has-error @endif">
    <label for="paper" class="col-sm-2 col-form-label">Paper Name:</label>
    <div class="col-sm-3">
      @if(isset($paper->id))
        <input type="text" class="form-control" id="paper" name="paper" value="{{$paper->name}}" required="true">
      @else
        <input type="text" class="form-control" id="paper" name="paper" value="" required="true">
      @endif
      @if($errors->has('paper')) <p class="help-block">{{ $errors->first('paper') }}</p> @endif
      <span class="hide" id="nameError" style="color: white;">Given name is already exist for above criteria.Please enter another name.</span>
    </div>
  </div>
  <div class="form-group row @if ($errors->has('marks')) has-error @endif">
    <label for="marks" class="col-sm-2 col-form-label">Marks:</label>
    <div class="col-sm-3">
      @if(isset($paper->id))
        <input type="text" class="form-control" id="marks" name="marks" value="{{$paper->marks}}" required="true" readonly>
      @else
        <input type="text" class="form-control" id="marks" name="marks" value="" required="true">
      @endif
      @if($errors->has('marks')) <p class="help-block">{{ $errors->first('marks') }}</p> @endif
    </div>
  </div>
  <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        @if(isset($paper->id) && $paper->subject->lecturer_id == Auth::User()->id)
          <button type="button" class="btn btn-primary" style="width: 90px !important;" onClick="searchPaper();">Submit</button>
        @elseif(empty($paper->id))
          <button type="button" class="btn btn-primary" style="width: 90px !important;" onClick="searchPaper();">Submit</button>
        @endif
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
        optDept.innerHTML = 'Select Department';
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
        optYears.innerHTML = 'Select Year';
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

  function searchPaper(){
    var subject = document.getElementById('subject').value;
    var department = document.getElementById('department').value;
    var year = document.getElementById('year').value;
    var paper = document.getElementById('paper').value;
    if(document.getElementById('paper_id')){
      var paperId = document.getElementById('paper_id').value;
    } else {
      var paperId = 0;
    }
    if(subject && department && year && paper){
      $.ajax({
        method:'POST',
        url: "{{url('isCollegeOfflinePaperExist')}}",
        data:{subject:subject,department:department,year:year,paper:paper,paper_id:paperId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('nameError').classList.remove('hide');
          document.getElementById('nameError').classList.add('has-error');
        } else {
          document.getElementById('nameError').classList.add('hide');
          document.getElementById('nameError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    } else {
      alert('please enter name.');
    }
  }
</script>
@stop