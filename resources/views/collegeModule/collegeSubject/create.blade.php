@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Subject  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Academic </li>
      <li class="active"> Manage Subject </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container admin_div">
  @if(isset($subject->id))
    <form action="{{url('college/'.Session::get('college_user_url').'/updateCollegeSubject')}}" method="POST" id="submitForm">
    {{ method_field('PUT') }}
    <input type="hidden" id="subject_id" name="subject_id" value="{{$subject->id}}">
  @else
   <form action="{{url('college/'.Session::get('college_user_url').'/createCollegeSubject')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('subject')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="subject">Subject Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="subject" name="subject" value="{{($subject)?$subject->name:null}}" required="true">
        @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
        <span class="hide" id="nameError" style="color: white;">Given name is already exist.Please enter another name.</span>
      </div>
    </div>
    <div class="form-group row @if ($errors->has('depts')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="depts">Departments:</label>
      <div class="col-sm-10">
        @if(isset($subject->id))
          @if(count($departments) > 0)
            @foreach($departments as $department)
              <input type="checkbox" name="depts_arr[]" value="{{$department->id}}" @if(in_array($department->id,$selectedDepts)) checked @endif disabled> {{$department->name}}
              @if(in_array($department->id,$selectedDepts))
                <input type="hidden" name="depts[]" value="{{$department->id}}" >
              @endif
            @endforeach
          @endif
        @else
          @if(count($departments) > 0)
            @foreach($departments as $department)
              <input type="checkbox" name="depts[]" value="{{$department->id}}"> {{$department->name}}
            @endforeach
          @endif
        @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('years')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="years">Years:</label>
      <div class="col-sm-10">
        @if(isset($subject->id))
          <input type="checkbox" name="years_arr[]" value="1" @if(in_array(1,$selectedYears)) checked @endif disabled> First
          @if(in_array(1,$selectedYears))
            <input type="hidden" name="years[]" value="1" >
          @endif
          <input type="checkbox" name="years[]" value="2" @if(in_array(2,$selectedYears)) checked @endif disabled> Second
          @if(in_array(2,$selectedYears))
            <input type="hidden" name="years[]" value="2" >
          @endif
          <input type="checkbox" name="years[]" value="3" @if(in_array(3,$selectedYears)) checked @endif disabled> Third
          @if(in_array(3,$selectedYears))
            <input type="hidden" name="years[]" value="3" >
          @endif
          <input type="checkbox" name="years[]" value="4" @if(in_array(4,$selectedYears)) checked @endif disabled> Fourth
          @if(in_array(4,$selectedYears))
            <input type="hidden" name="years[]" value="4" >
          @endif
        @else
          <input type="checkbox" name="years[]" value="1"> First
          <input type="checkbox" name="years[]" value="2"> Second
          <input type="checkbox" name="years[]" value="3"> Third
          <input type="checkbox" name="years[]" value="4"> Fourth
          @endif
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
      @if(isset($subject->id) && $subject->lecturer_id == Auth::User()->id)
        <button type="button" class="btn btn-primary" style="width: 90px !important;" onclick="searchSubject();">Submit</button>
      @elseif(empty($subject->id))
        <button type="button" class="btn btn-primary" style="width: 90px !important;" onclick="searchSubject();">Submit</button>
      @endif
      </div>
    </div>
    </form>
  </div>
<script type="text/javascript">
  function searchSubject(){
    var subject = document.getElementById('subject').value;
    if(document.getElementById('subject_id')){
      var subjectId = document.getElementById('subject_id').value;
    } else {
      var subjectId = 0;
    }
    if(subject){
      $.ajax({
        method:'POST',
        url: "{{url('isCollegeSubjectExist')}}",
        data:{subject:subject,subject_id:subjectId}
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