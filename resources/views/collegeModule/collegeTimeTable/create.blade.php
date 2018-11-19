@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> College Time Table </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-clock-o"></i> Time Table </li>
      <li class="active"> College Time Table </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container admin_div">
  @if(isset($timeTable->id))
    <form action="{{url('college/'.Session::get('college_user_url').'/updateCollegeTimeTable')}}" method="POST" id="submitForm" enctype="multipart/form-data">
    {{ method_field('PUT') }}
    <input type="hidden" id="time_table_id" name="time_table_id" value="{{$timeTable->id}}">
  @else
   <form action="{{url('college/'.Session::get('college_user_url').'/createCollegeTimeTable')}}" method="POST" id="submitForm" enctype="multipart/form-data">
  @endif
    {{ csrf_field() }}

    <div class="form-group row @if ($errors->has('depts')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="depts">Department:</label>
      <div class="col-sm-3">
        @if(isset($timeTable->id))
          @if(count($collegeDepts) > 0)
              @foreach($collegeDepts as $collegeDept)
                  @if($timeTable->college_dept_id == $collegeDept->id)
                    <input type="text" class="form-control" name="department_text" value="{{$collegeDept->name}}" readonly>
                    <input type="hidden" id="department" name="department" value="{{$collegeDept->id}}">
                  @endif
              @endforeach
          @endif
        @else
        <select class="form-control" id="department" name="department" required title="Department">
          <option value="">Select Department</option>
          @if(count($collegeDepts) > 0)
              @foreach($collegeDepts as $collegeDept)
                @if(count($assignedDepts) > 0)
                  @if(in_array($collegeDept->id,$assignedDepts))
                    <option value="{{$collegeDept->id}}">{{$collegeDept->name}}</option>
                  @endif
                @else
                  <option value="{{$collegeDept->id}}">{{$collegeDept->name}}</option>
                @endif
              @endforeach
          @endif
        </select>
          @if($errors->has('department')) <p class="help-block">{{ $errors->first('department') }}</p> @endif
      @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('year')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="year">Year:</label>
      <div class="col-sm-3">
        @if(isset($timeTable->id))
          @if(1 == $timeTable->year)
            <input type="text" class="form-control" name="year_text" value="First" readonly>
          @elseif(2 == $timeTable->year)
            <input type="text" class="form-control" name="year_text" value="Second" readonly>
          @elseif(3 == $timeTable->year)
            <input type="text" class="form-control" name="year_text" value="Third" readonly>
          @else
            <input type="text" class="form-control" name="year_text" value="Fourth" readonly>
          @endif
          <input type="hidden" class="form-control" id="year" name="year" value="{{$timeTable->year}}">
        @else
          <select class="form-control" id="year" name="year" required title="Year">
            <option>Select Year</option>
            <option value="1">First</option>
            <option value="2">Second</option>
            <option value="3">Third</option>
            <option value="4">Fourth</option>
          </select>
        @endif
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="image_path">Image:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control"  name="image_path" id="image_path" >
        @if($errors->has('image_path')) <p class="has-error">{{ $errors->first('image_path') }}</p> @endif
        @if(isset($timeTable->image_path))
          <b><span>Existing Image: {!! basename($timeTable->image_path) !!}</span></b>
        @endif
      </div>
    </div>
     <span class="hide" id="ttError" style="color: white;">Record exist for selected department and year.Please enter another department and/or year.</span>
     <input type="hidden" name="type" value="1">
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="button" class="btn btn-primary" style="width: 90px !important;" onclick="searchTimeTable();">Submit</button>
      </div>
    </div>
    </form>
  </div>
<script type="text/javascript">
  function searchTimeTable(){
    var department = document.getElementById('department').value;
    var year = document.getElementById('year').value;
    if(document.getElementById('time_table_id')){
      var timeTableId = document.getElementById('time_table_id').value;
    } else {
      var timeTableId = 0;
    }
    if(year && department){
      $.ajax({
        method:'POST',
        url: "{{url('isCollegeTimeTableExist')}}",
        data:{year:year,department:department,time_table_id:timeTableId}
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
    } else if(!department){
      alert('please select department.');
    } else if(!year){
      alert('please select year.');
    }
  }
</script>
@stop