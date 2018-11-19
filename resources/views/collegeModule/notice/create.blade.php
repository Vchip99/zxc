@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Notice  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Academic </li>
      <li class="active"> Manage Notice </li>
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
  @if(isset($collegeNotice->id))
    <form action="{{url('college/'.Session::get('college_user_url').'/updateCollegeNotice')}}" method="POST" id="submitForm">
      {{method_field('PUT')}}
      <input type="hidden" id="notice_id" name="notice_id" value="{{$collegeNotice->id}}">
  @else
      <form action="{{url('college/'.Session::get('college_user_url').'/createCollegeNotice')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('is_emergency')) has-error @endif">
    <label for="is_emergency" class="col-sm-2 col-form-label">Emergency Notice:</label>
    <div class="col-sm-3">
      @if(isset($collegeNotice->id))
        No <input type="radio" name="is_emergency" value="0" @if(0 == $collegeNotice->is_emergency) checked @endif> Yes <input type="radio" name="is_emergency" value="1" @if(1 == $collegeNotice->is_emergency) checked @endif>
      @else
        No <input type="radio" name="is_emergency" value="0" checked> Yes <input type="radio" name="is_emergency" value="1">
      @endif
      @if($errors->has('notice')) <p class="help-block">{{ $errors->first('notice') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('department')) has-error @endif">
    <label class="col-sm-2 col-form-label">Departments:</label>
    <div class="col-sm-3">
    	@if(isset($collegeNotice->id))
    		@if(count($collegeDepts) > 0)
            @php
              $noticeDepts = explode(',',$collegeNotice->college_dept_ids);
            @endphp
	            @foreach($collegeDepts as $collegeDept)
                  @if(in_array($collegeDept->id,$noticeDepts))
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
      @if(isset($collegeNotice->id))
        @if(count($years) > 0)
            @php
              $noticeYears = explode(',',$collegeNotice->years);
            @endphp
            @foreach($years as $year)
                @if(1 == $year && in_array($year,$noticeYears))
                    <input type="text" class="form-control" name="year_text" value="First" readonly>
                    <input type="hidden" id="year" name="year[]" value="{{$year}}">
                @endif
                @if(2 == $year && in_array($year,$noticeYears))
                    <input type="text" class="form-control" name="year_text" value="Second" readonly>
                    <input type="hidden" id="year" name="year[]" value="{{$year}}">
                @endif
                @if(3 == $year && in_array($year,$noticeYears))
                    <input type="text" class="form-control" name="year_text" value="Third" readonly>
                    <input type="hidden" id="year" name="year[]" value="{{$year}}">
                @endif
                @if(4 == $year && in_array($year,$noticeYears))
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
                    <option value="1" @if(1 == $collegeNotice->year) selected @endif> First</option>
                  @endif
                  @if(2 == $year)
                    <option value="2" @if(2 == $collegeNotice->year) selected @endif> Second</option>
                  @endif
                  @if(3 == $year)
                    <option value="3" @if(3 == $collegeNotice->year) selected @endif> Third</option>
                  @endif
                  @if(4 == $year)
                    <option value="4" @if(4 == $collegeNotice->year) selected @endif> Fourth</option>
                  @endif
                @endforeach
            @endif
          </select>
          @if($errors->has('year')) <p class="help-block">{{ $errors->first('year') }}</p> @endif
        @endif
    </div>
  </div>
  <div class="form-group row">
      <label for="" class="col-sm-2 col-form-label">Date:</label>
      <div class="col-sm-3">
        <input type="text"  class="form-control" name="date" id="date" @if(isset($collegeNotice->id)) value="{{$collegeNotice->date}}" @endif placeholder="Date" required>
      </div>
      <script type="text/javascript">
          $(function () {
              $('#date').datetimepicker({
                format: 'YYYY-MM-DD'
              });
          });
      </script>
    </div>
  <div class="form-group row @if ($errors->has('notice')) has-error @endif">
    <label for="notice" class="col-sm-2 col-form-label">Notice:</label>
    <div class="col-sm-3">
      @if(isset($collegeNotice->id))
        <textarea class="form-control" id="notice" name="notice" required cols="5" rows="10">{{$collegeNotice->notice}}</textarea>
      @else
        <textarea class="form-control" id="notice" name="notice" required cols="5" rows="5"></textarea>
      @endif
      @if($errors->has('notice')) <p class="help-block">{{ $errors->first('notice') }}</p> @endif
    </div>
    <div class="col-sm-6" style="color: white">* Only first 120 alphabets/letters will be send as sms if setting is on</div>
  </div>
  <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        @if(isset($collegeNotice->id) && $collegeNotice->created_by == Auth::user()->id)
          <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
        @elseif(empty($collegeNotice->id))
          <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
        @else
          <a href="{{url('college/'.Session::get('college_user_url').'/manageCollegeNotice')}}" class="btn btn-primary" style="width: 90px !important;">Back</a>
        @endif
      </div>
    </div>
  </div>
</form>
@stop