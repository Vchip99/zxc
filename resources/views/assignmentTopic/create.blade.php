@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Topic  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Assignment </li>
      <li class="active"> Manage Topic </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container admin_div">
  @if(isset($topic->id))
    <form action="{{url('college/'.Session::get('college_user_url').'/updateAssignmentTopic')}}" method="POST" id="submitForm">
      {{method_field('PUT')}}
      <input type="hidden" id="topic_id" name="topic_id" value="{{$topic->id}}">
  @else
      <form action="{{url('college/'.Session::get('college_user_url').'/createAssignmentTopic')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('subject')) has-error @endif">
    <label class="col-sm-2 col-form-label">Subject:</label>
    <div class="col-sm-3">
      @if(isset($topic->id))
        @if(count($subjects) > 0)
            @foreach($subjects as $subject)
              @if( $topic->college_subject_id == $subject->id)
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
  <div class="form-group row @if ($errors->has('departments')) has-error @endif">
    <label class="col-sm-2 col-form-label">Departments:</label>
    <div class="col-sm-3">
      @if(isset($topic->id))
        @if(count($collegeDepts) > 0)
            @foreach($collegeDepts as $collegeDept)
                @if(in_array($collegeDept->id,$selectedDepts))
                  <input type="text" class="form-control" name="department_text" value="{{$collegeDept->name}}" readonly>
                  <input type="hidden" name="departments[]" value="{{$collegeDept->id}}">
                @endif
            @endforeach
        @endif
      @else
        <select class="form-control" id="departments" name="departments[]" required title="Department" multiple>
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
  <div class="form-group row @if ($errors->has('years')) has-error @endif">
    <label class="col-sm-2 col-form-label">Years:</label>
    <div class="col-sm-3">
      @if(isset($topic->id))
        @if(count($years) > 0)
            @foreach($years as $year)
              @if(1 == $year)
                  @if(in_array($year,$selectedYears))
                    <input type="text" class="form-control" name="{{$year}}_text" value="First" readonly>
                    <input type="hidden" name="years[]" value="{{$year}}">
                  @endif
                @endif
                @if(2 == $year)
                  @if(in_array($year,$selectedYears))
                    <input type="text" class="form-control" name="{{$year}}_text" value="Second" readonly>
                    <input type="hidden" name="years[]" value="{{$year}}">
                  @endif
                @endif
                @if(3 == $year)
                  @if(in_array($year,$selectedYears))
                    <input type="text" class="form-control" name="{{$year}}_text" value="Third" readonly>
                    <input type="hidden" name="years[]" value="{{$year}}">
                  @endif
                @endif
                @if(4 == $year)
                  @if(in_array($year,$selectedYears))
                    <input type="text" class="form-control" name="{{$year}}_text" value="Fourth" readonly>
                    <input type="hidden" name="years[]" value="{{$year}}">
                  @endif
                @endif
            @endforeach
          @endif
      @else
        <select class="form-control" id="years" name="years[]" required title="Year" multiple>
          <option value="">Select Years</option>
          @if(count($years) > 0)
            @foreach($years as $year)
              @if(1 == $year)
                <option value="1" @if(in_array($year,$selectedYears)) selected @endif> First</option>
              @endif
              @if(2 == $year)
                <option value="2" @if(in_array($year,$selectedYears)) selected @endif> Second</option>
              @endif
              @if(3 == $year)
                <option value="3" @if(in_array($year,$selectedYears)) selected @endif> Third</option>
              @endif
              @if(4 == $year)
                <option value="4" @if(in_array($year,$selectedYears)) selected @endif> Fourth</option>
              @endif
            @endforeach
          @endif
        </select>
        @if($errors->has('year')) <p class="help-block">{{ $errors->first('year') }}</p> @endif
      @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('topic')) has-error @endif">
    <label for="topic" class="col-sm-2 col-form-label">Topic Name:</label>
    <div class="col-sm-3">
      @if(isset($topic))
        <input type="text" class="form-control" id="topic" name="topic" value="{{$topic->name}}" required="true">
      @else
        <input type="text" class="form-control" id="topic" name="topic" value="" required="true">
      @endif
      @if($errors->has('topic')) <p class="help-block">{{ $errors->first('topic') }}</p> @endif
      <span class="hide" id="nameError" style="color: white;">Given name is already exist with selected subject.Please enter another name.</span>
    </div>
  </div>
  <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        @if(isset($topic->id) && $topic->subject->lecturer_id == Auth::User()->id)
          <button type="button" class="btn btn-primary" style="width: 90px !important;" onClick="searchTopic();">Submit</button>
        @elseif(empty($topic->id))
          <button type="button" class="btn btn-primary" style="width: 90px !important;" onClick="searchTopic();">Submit</button>
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
        selectDept = document.getElementById('departments');
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

        selectYears = document.getElementById('years');
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

  function searchTopic(){
    var subject = document.getElementById('subject').value;
    var topic = document.getElementById('topic').value;
    if(document.getElementById('topic_id')){
      var topicId = document.getElementById('topic_id').value;
    } else {
      var topicId = 0;
    }
    if(subject && topic){
      $.ajax({
        method:'POST',
        url: "{{url('isAssignmentTopicExist')}}",
        data:{subject_id:subject,topic:topic,topic_id:topicId}
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