@extends('dashboard.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Users </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-group"></i> Users Info</li>
      <li class="active">Users </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
	<div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="top mrgn_40_btm">
        <div class="container">
          @if(Session::has('message'))
            <div class="alert alert-success" id="message">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ Session::get('message') }}
            </div>
          @endif
          <div class="row">
            <div class="">
            <input type="hidden" id="login_User_Type" name="login_User_Type" value="{{Auth::user()->user_type}}">
            <div class="col-md-3 mrgn_10_btm">
              <select class="form-control" id="dept" onChange="resetStudents();">
                <option value="0"> Select Department </option>
                <option value="All" @if('All' == $selectedDept) selected @endif>All</option>
                @if(count($collegeDepts) > 0)
                  @foreach($collegeDepts as $collegeDept)
                    @if($selectedDept == $collegeDept->id)
                      <option value="{{$collegeDept->id}}" selected>{{$collegeDept->name}}</option>
                    @else
                      <option value="{{$collegeDept->id}}">{{$collegeDept->name}}</option>
                    @endif
                  @endforeach
                @endif
              </select>
            </div>
            @if(4 == Auth::user()->user_type || 5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
            <div class="col-md-3 mrgn_10_btm" id="showUsers">
              <select class="form-control" id="user" name="user_type" onChange="showResult();" required="true">
                <option value="0">Select User Type</option>
                <option value="2" @if(2 == $selectedUserType) selected @endif>Student</option>
                <option value="3" @if(3 == $selectedUserType) selected @endif>Lecturer</option>
                @if(5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
                  <option value="4" @if(4 == $selectedUserType) selected @endif>Hod</option>
                @endif
              </select>
            </div>
            @endif
            @if(3 == Auth::user()->user_type)
              <div class="col-md-3 mrgn_10_btm" id="div_year">
            @else
              @if(!empty($selectedYear))
                <div class="col-md-3 mrgn_10_btm" id="div_year">
              @else
                <div class="col-md-3 mrgn_10_btm hide" id="div_year">
              @endif
            @endif
                <select class="form-control" id="selected_year" name="year" onChange="showStudents(this);">
                  <option value="0"> Select Year </option>
                  <option value="All" @if('All' == $selectedYear) selected @endif>All</option>
                  <option value="1" @if(1 == $selectedYear) selected @endif>First Year</option>
                  <option value="2" @if(2 == $selectedYear) selected @endif>Second Year</option>
                  <option value="3" @if(3 == $selectedYear) selected @endif>Third Year</option>
                  <option value="4" @if(4 == $selectedYear) selected @endif>Fourth Year</option>
                </select>
              </div>
              <div class="col-md-3 ">
                <div class="input-group">
                  <input type="text" id="search_student" name="student" class="form-control" placeholder="Search..." onkeyup="searchStudent(this.value);">
                    <span class="input-group-btn">
                      <button type="submit" name="search" id="search-btn" class="btn btn-flat" ><i class="fa fa-search"></i>
                      </button>
                    </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-lg-12" id="all-result">
            <div class="panel panel-info">
              <div class="panel-heading text-center">
                Records
              </div>
              <div class="panel-body">
                @if(2 == $selectedUserType)
                  <table id="student-record">
                @else
                  <table class="hide" id="student-record">
                @endif
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Name</th>
                      <th>Department</th>
                      <th>Year</th>
                      <th>Roll No.</th>
                      <th>Approval</th>
                      <th>Delete</th>
                      @if(3 == Auth::user()->user_type || 4 == Auth::user()->user_type)
                        <th>Edit Profile</th>
                      @endif
                    </tr>
                  </thead>
                  <tbody id="studentsTbl">
                    @if(count($users) > 0)
                      @foreach($users as $index => $user)
                        <tr style="overflow-x: auto;">
                          <td>{{ $index + 1}}</td>
                          <td>
                            <a href="#studentModal_{{$user->id}}" data-toggle="modal">{{$user->name}}</a>
                          </td>
                          <td>{{$collegeDeptNames[$user->college_dept_id]}}</td>
                          <td>{{$user->year}}</td>
                          <td>{{$user->roll_no}}</td>
                          <td>
                            @if(1 == $user->admin_approve)
                              <input type="checkbox" value="" data-student_id="{{$user->id}}" data-college_id="{{$user->college_id}}" data-department_id="{{$user->college_dept_id}}" data-year="{{$user->year}}" onclick="changeApproveStatus(this);" checked="checked">
                            @else
                              <input type="checkbox" value="" data-student_id="{{$user->id}}" data-college_id="{{$user->college_id}}" data-department_id="{{$user->college_dept_id}}" data-year="{{$user->year}}" onclick="changeApproveStatus(this);">
                            @endif
                          </td>
                          <td>
                            <button class="btn btn-danger btn-xs delet-bt delet-btn" data-title="Delete" data-toggle="modal" data-target="#delete" data-student_id="3" onclick="deleteUser(this);">
                              <span class="fa fa-trash-o" data-placement="top" data-toggle="tooltip" title="Delete"></span>
                            </button>
                            <form id="deleteCollegeUser_{{$user->id}}" action="{{url('college/'.Session::get('college_user_url').'/deleteStudentFromCollege')}}" method="POST" style="display: none;">
                              {{ csrf_field() }}
                              {{ method_field("DELETE") }}
                              <input type="hidden" name="student_id" value="{{$user->id}}">
                              <input type="hidden" name="college_id" value="{{$user->college_id}}">
                              <input type="hidden" name="department_id" value="{{$user->college_dept_id}}">
                              <input type="hidden" name="year" value="{{$user->year}}">
                            </form>
                          </td>
                          @if(3 == Auth::user()->user_type || 4 == Auth::user()->user_type)
                            <td>
                              <a href="#studentProfile_{{$user->id}}" data-toggle="modal">Edit</a>
                            </td>
                          @endif
                        </tr>
                        <div class="modal fade in" id="studentModal_{{$user->id}}" role="dialog" style=" padding-right: 15px;">
                          <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">×</button>
                                <h4 class="modal-title">Student Details</h4>
                              </div>
                              <div class="modal-body">
                                <div class="form-group">
                                  <label>Year:</label> {{$user->year}}
                                </div>
                                <div class="form-group">
                                  <label>Email:</label> {{$user->email}}
                                </div>
                                <div class="form-group">
                                  <label>Phone:</label> {{$user->phone}}
                                </div>
                                <div class="form-group">
                                  <a href="{{url('college/'.Session::get('college_user_url').'/studentCollegeTestResults')}}/{{$user->id}}">Test Result</a>
                                </div>
                                  <div class="form-group">
                                    <a href="{{url('college/'.Session::get('college_user_url').'/studentCollegeCourses')}}/{{$user->id}}">Course</a>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                              </div>
                            </div>
                          </div>
                        </div>
                        @if(3 == Auth::user()->user_type || 4 == Auth::user()->user_type)
                          <div id="studentProfile_{{$user->id}}" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <button class="close" data-dismiss="modal">×</button>
                                  <h2  class="modal-title">Edit Profile</h2>
                                </div>
                                <div class="modal-body">
                                  <form action="{{url('college/'.Session::get('college_user_url').'/updateUserProfile')}}" method="POST" enctype="multipart/form-data">
                                    {{ method_field('PUT') }}
                                    {{ csrf_field() }}
                                    <fieldset>
                                      <div class="form-group row">
                                        <label>Name:</label>
                                        <input class="form-control" placeholder="name" name="name" type="text" value="{{$user->name}}" required>
                                      </div>
                                      <div class="form-group" id="deptDiv" >
                                        <label>Department:</label>
                                        <select class="form-control  slt mrgn_20_top" name="department" id="dept" required>
                                          <option value="">Select Department</option>
                                          @if(count($collegeDepts) > 0)
                                            @foreach($collegeDepts as $collegeDept)
                                              <option value="{{$collegeDept->id}}" @if($collegeDept->id == $user->college_dept_id) selected @endif >{{$collegeDept->name}}</option>
                                            @endforeach
                                          @endif
                                        </select>
                                      </div>
                                        <div class="form-group @if ($errors->has('year')) has-error @endif " id="year" >
                                          <label>Year:</label>
                                          <select class="form-control  slt mrgn_20_top" name="year" required>
                                            <option value="">Select Year</option>
                                            <option value="1" @if(1 == $user->year) selected @endif >First Year</option>
                                            <option value="2" @if(2 == $user->year) selected @endif >Second Year </option>
                                            <option value="3" @if(3 == $user->year) selected @endif >Third Year</option>
                                            <option value="4" @if(4 == $user->year) selected @endif >Final Year</option>
                                          </select>
                                        </div>
                                        <div class="form-group mrgn_20_top" id="rollNo">
                                          <label>Roll No:</label>
                                          <input type="number" class="form-control" name="roll_no" id="roll" value="{{$user->roll_no}}" placeholder="Roll No."  min="0" required/>
                                          <span class="help-block"></span>
                                        </div>
                                      <div class="form-group">
                                        <label>Photo:</label>
                                        <input class="form-control" placeholder="Mobile No." name="photo" type="file">
                                        <label>Existing Photo:</label> {{basename($user->photo)}}
                                      </div>
                                      <div class="form-group">
                                        <label>Resume:</label>
                                        <input class="form-control" placeholder="Mobile No." name="resume" type="file">
                                        <label>Existing Resume:</label> {{basename($user->resume)}}
                                      </div>
                                      <input type="hidden" name="college_id" value="{{ $user->college_id }}">
                                      <input type="hidden" name="user_id" value="{{ $user->id }}">
                                      <button data-dismiss="modal" class="btn btn-info" type="button">Cancel</button>
                                      <button class="btn btn-info" type="submit">Submit</button>
                                    </fieldset>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>
                        @endif
                        @endforeach
                    @else
                      <tr>
                        <td colspan="8"> No Result! </td>
                      </tr>
                    @endif
                  </tbody>
                </table>
                @if(3 == $selectedUserType || 4 == $selectedUserType)
                  <table id="lectures_hods_record">
                @else
                  <table class="hide" id="lectures_hods_record">
                @endif
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Name</th>
                      <th>Department</th>
                      <th>Approval</th>
                      <th>Delete</th>
                    </tr>
                  </thead>
                  <tbody id="lecture_hods" class="">
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript">

  function showDepartments(){
    var user_type = parseInt(document.getElementById('user').value);
    var login_User_Type = parseInt(document.getElementById('login_User_Type').value);
    if(document.getElementById('dept')){
      document.getElementById('dept').value = 0;
    }
    document.getElementById('selected_year').value = 0;
    document.getElementById('search_student').value ='';
    if(2 == user_type){
      document.getElementById('student-record').classList.remove('hide');
      document.getElementById('lectures_hods_record').classList.add('hide');
      document.getElementById('div_year').classList.remove('hide');
      document.getElementById('studentsTbl').innerHTML = '';
    }
    if(3 == user_type || 4 == user_type){
      document.getElementById('lecture_hods').innerHTML = '';
      document.getElementById('lectures_hods_record').classList.remove('hide');
      document.getElementById('student-record').classList.add('hide');
      document.getElementById('div_year').classList.add('hide');
    }
    if(4 == login_User_Type && 3 == user_type){
      showStudents();
    }
  }

  function showResult(){
    var user_type = parseInt(document.getElementById('user').value);
    document.getElementById('search_student').value ='';
    if(2 == user_type){
      document.getElementById('div_year').classList.remove('hide');
      resetYear();
    }
    if(3 == user_type || 4 == user_type){
      document.getElementById('div_year').classList.add('hide');
      resetYear();
      showStudents();
    }
  }

  function resetStudents(){
    if(document.getElementById('user')){
      document.getElementById('user').selectedIndex = '0';
    }
    document.getElementById('div_year').classList.remove('hide');
    resetYear();
  }

  function resetYear(){
    document.getElementById('selected_year').value = 0;
    document.getElementById('studentsTbl').innerHTML = '';
  }
  function showStudents(){
    document.getElementById('search_student').value ='';
    if(document.getElementById('selected_year')){
      var year = document.getElementById('selected_year').value;
    } else {
      var year = 0;
    }
    if(document.getElementById("dept")){
        var department = document.getElementById("dept").value;
    } else {
        var department = 0;
    }
    if(document.getElementById("user")){
        var user_type = parseInt(document.getElementById("user").value);
    } else {
        var login_User_Type = parseInt(document.getElementById('login_User_Type').value);
        if(3 == login_User_Type){
          var user_type = 2;
        } else {
          var user_type = 0;
        }
    }
      $.ajax({
        method: "POST",
        url: "{{url('searchStudent')}}",
        data: {year:year, department:department, user_type:user_type}
      })
      .done(function( msg ) {
        if(2 == user_type){
          document.getElementById('student-record').classList.remove('hide');
          document.getElementById('lectures_hods_record').classList.add('hide');
          body = document.getElementById('studentsTbl');
          body.innerHTML = '';
        } else if(3 == user_type || 4 == user_type){
          document.getElementById('lectures_hods_record').classList.remove('hide');
          document.getElementById('student-record').classList.add('hide');
          body = document.getElementById('lecture_hods');
          body.innerHTML = '';
        }
        if(msg['users'].length > 0){
          renderResult(msg,body,user_type);
        } else {
          var eleTr = document.createElement('tr');
          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = 'No result!';
          eleIndex.setAttribute('colspan', '7');
          eleTr.appendChild(eleIndex);
          body.appendChild(eleTr);
        }
      });
  }

  function searchStudent(student){
    if(document.getElementById('selected_year')){
      var year = document.getElementById('selected_year').value;
    } else {
      var year = 0;
    }
    if(document.getElementById("dept")){
        var department = document.getElementById("dept").value;
    } else {
        var department = 0;
    }
    if(document.getElementById("user")){
        var user_type = parseInt(document.getElementById("user").value);
    } else {
        var login_User_Type = parseInt(document.getElementById('login_User_Type').value);
        if(3 == login_User_Type){
          var user_type = 2;
        } else {
          var user_type = 0;
        }
    }

      $.ajax({
        method: "POST",
        url: "{{url('searchStudent')}}",
        data: {student:student, year:year, department:department, user_type:user_type}
      })
      .done(function( msg ) {
        if(2 == user_type){
          body = document.getElementById('students');
        } else if(3 == user_type || 4 == user_type){
          body = document.getElementById('lecture_hods');
        }
        body.innerHTML = '';
        if(msg['users'].length > 0){
          renderResult(msg,body,user_type);
        } else {
          if(2 == user_type){
            document.getElementById('students').innerHTML = '';
            body = document.getElementById('students');
          } else if(3 == user_type){
            document.getElementById('lecture_hods').innerHTML = '';
            body = document.getElementById('lecture_hods');
          }
          var eleTr = document.createElement('tr');
          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = 'No Result!';
          eleIndex.setAttribute('colspan', '8');
          eleTr.appendChild(eleIndex);
          body.appendChild(eleTr);
        }
      });
  }

  function renderResult(msg,body,user_type){
    var login_User_Type = parseInt(document.getElementById('login_User_Type').value);
    $.each(msg['users'], function(idx, obj) {
      var eleTr = document.createElement('tr');
      eleTr.setAttribute('style','overflow-x: auto;');
      var eleIndex = document.createElement('td');
      eleIndex.innerHTML = idx + 1;
      eleTr.appendChild(eleIndex);

      var eleName = document.createElement('td');
      eleName.innerHTML = '<a href="#studentModal_'+obj.id+'" data-toggle="modal">'+obj.name+'</a>';
      eleTr.appendChild(eleName);

      var eleDept = document.createElement('td');
      if((3 == user_type && 4 == login_User_Type) || (4 == user_type && 5 == login_User_Type) && msg['departments'].length > 0){
        eleDept.innerHTML = '<a href="#deptModal_'+obj.id+'" data-toggle="modal">'+obj.department+'</a>';
      }else {
        eleDept.innerHTML = obj.department;
      }
      eleTr.appendChild(eleDept);

      if(2 == user_type){
        var eleYear = document.createElement('td');
        eleYear.innerHTML = obj.year;
        eleTr.appendChild(eleYear);

        var eleRollNo = document.createElement('td');
        eleRollNo.innerHTML = obj.roll_no;
        eleTr.appendChild(eleRollNo);
      }

      var eleApprove = document.createElement('td');
      approveInnerHTML = '<input type="checkbox" value="" data-student_id="'+obj.id+'" data-college_id="'+obj.college_id+'" data-department_id="'+obj.college_dept_id+'" data-year="'+obj.year+'" onclick="changeApproveStatus(this);"';
      if( 1 == obj.admin_approve){
        approveInnerHTML += 'checked = checked';
      }
      approveInnerHTML += '>';
      eleApprove.innerHTML = approveInnerHTML;
      eleTr.appendChild(eleApprove);

      var eleDelete = document.createElement('td');
      eleDelete.innerHTML = '<button class="btn btn-danger btn-xs delet-bt delet-btn" data-title="Delete" data-toggle="modal" data-target="#delete" data-student_id="'+ obj.id +'" onclick="deleteUser(this);" ><span class="fa fa-trash-o" data-placement="top" data-toggle="tooltip" title="Delete"></span></button>';
      var url = "{{url('college/'.Session::get('college_user_url').'/deleteStudentFromCollege')}}";
      var csrfField = '{{ csrf_field() }}';
      var deleteMethod ='{{ method_field("DELETE") }}';
      eleDelete.innerHTML += '<form id="deleteCollegeUser_'+ obj.id +'" action="'+url+'" method="POST" style="display: none;">'+csrfField+''+deleteMethod+'<input type="hidden" name="student_id" value="'+obj.id+'"><input type="hidden" name="college_id" value="'+obj.college_id+'"><input type="hidden" name="department_id" value="'+obj.college_dept_id+'"><input type="hidden" name="year" value="'+obj.year+'"></form>';
      eleTr.appendChild(eleDelete);

      var eleModel = document.createElement('div');
      eleModel.className = 'modal fade';
      eleModel.id = 'studentModal_'+obj.id;
      eleModel.setAttribute('role', 'dialog');
      var urlStudentTest = "{{url('college/'.Session::get('college_user_url').'/studentCollegeTestResults')}}/"+obj.id;
      var urlStudentCourse = "{{url('college/'.Session::get('college_user_url').'/studentCollegeCourses')}}/"+obj.id;
      var urllecturerPaper = "{{url('college/'.Session::get('college_user_url').'/lecturerPapers')}}/"+obj.id;
      var urlLecturerCourse = "{{url('college/'.Session::get('college_user_url').'/lecturerCourses')}}/"+obj.id;
      var modelInnerHTML = '';
      if(2 == user_type ){
        modelInnerHTML='<div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Student Details</h4></div><div class="modal-body">';

        modelInnerHTML +='<div class="form-group"><label>Year:</label> '+obj.year+'</div>';
      } else {
        modelInnerHTML='<div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Lecturer Details</h4></div><div class="modal-body">';
      }
      modelInnerHTML +='<div class="form-group"><label>Email:</label> '+obj.email+'</div><div class="form-group"><label>Phone:</label> '+obj.phone+'</div>';
      if(2 == user_type ){
        modelInnerHTML +='<div class="form-group"><a href="'+urlStudentTest+'">Test Result</a></div><div class="form-group"><a href="'+urlStudentCourse+'">Course</a></div>';
      } else {
        modelInnerHTML +='<div class="form-group"><a href="'+urllecturerPaper+'">Lecturer Papers</a></div><div class="form-group"><a href="'+urlLecturerCourse+'">Lecturer Course</a></div>';
      }
      modelInnerHTML +='</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div></div></div>';
      eleModel.innerHTML = modelInnerHTML;
      eleTr.appendChild(eleModel);

      if(2 == user_type && (3 == login_User_Type || 4 == login_User_Type)){
        var eleProfile = document.createElement('td');
        eleProfile.innerHTML = '<a href="#studentProfile_'+obj.id+'" data-toggle="modal">Edit</a>';
        eleTr.appendChild(eleProfile);

        var eleProfileModel = document.createElement('div');
        eleProfileModel.className = 'modal fade';
        eleProfileModel.id = 'studentProfile_'+obj.id;
        eleProfileModel.setAttribute('role', 'dialog');
        var updateUserProfile = "{{url('college/'.Session::get('college_user_url').'/updateUserProfile')}}";
        var csrfField = '{{ csrf_field() }}';
        var putMethod ='{{ method_field("PUT") }}';
        var modelProfileInnerHTML = '';
        modelProfileInnerHTML='<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button class="close" data-dismiss="modal">×</button><h2 class="modal-title">Edit Profile</h2></div><div class="modal-body"><form action="'+updateUserProfile+'" method="POST" enctype="multipart/form-data">'+csrfField+''+putMethod;

        modelProfileInnerHTML +='<fieldset><div class="form-group row"><label>Name:</label><input class="form-control" placeholder="name" name="name" value="'+obj.name+'" required="" type="text"></div>';
        modelProfileInnerHTML +='<div class="form-group" id="deptDiv"><label>Department:</label><select class="form-control  slt mrgn_20_top" name="department" id="dept" required=""><option value="">Select Department</option>';
        if(msg['assignDepts'].length > 0){
          $.each(msg['assignDepts'],function(idx,dept){
            if(obj.college_dept_id == dept.id){
              modelProfileInnerHTML +='<option value="'+dept.id+'" selected>'+dept.name+'</option>';
            } else {
              modelProfileInnerHTML +='<option value="'+dept.id+'">'+dept.name+'</option>';
            }
          });
        }
        modelProfileInnerHTML +='</select></div>';
        modelProfileInnerHTML +='<div class="form-group  " id="year"><label>Year:</label><select class="form-control  slt mrgn_20_top" name="year" required=""><option value="">Select Year</option>';
        if(1 == obj.year){
          modelProfileInnerHTML +='<option value="1" selected>First Year</option>';
        } else {
          modelProfileInnerHTML +='<option value="1">First Year</option>';
        }
        if(2 == obj.year){
          modelProfileInnerHTML +='<option value="2" selected>Second Year </option>';
        } else {
          modelProfileInnerHTML +='<option value="2">Second Year </option>';
        }
        if(3 == obj.year){
          modelProfileInnerHTML +='<option value="3" selected>Third Year</option>';
        } else {
          modelProfileInnerHTML +='<option value="3">Third Year</option>';
        }
        if(4 == obj.year){
          modelProfileInnerHTML +='<option value="4" selected>Final Year</option>';
        } else {
          modelProfileInnerHTML +='<option value="4">Final Year</option>';
        }
        modelProfileInnerHTML +='</select></div>';
        modelProfileInnerHTML +='<div class="form-group mrgn_20_top" id="rollNo"><label>Roll No:</label><input class="form-control" name="roll_no" id="roll" value="'+obj.roll_no+'" placeholder="Roll No." min="0" required="" type="number"><span class="help-block"></span></div>';

        modelProfileInnerHTML +='<div class="form-group"><label>Photo:</label><input class="form-control" placeholder="Mobile No." name="photo" type="file"><label>Existing Photo:</label></div>';

        modelProfileInnerHTML +='<div class="form-group"><label>Resume:</label><input class="form-control" placeholder="Mobile No." name="resume" type="file"><label>Existing Resume:</label></div>';

        modelProfileInnerHTML +='<input name="college_id" value="'+obj.college_id+'" type="hidden"><input name="user_id" value="'+obj.id+'" type="hidden"><button data-dismiss="modal" class="btn btn-info" type="button">Cancel</button><button class="btn btn-info" type="submit">Submit</button></fieldset></form></div></div></div>';
        eleProfileModel.innerHTML = modelProfileInnerHTML;
        eleTr.appendChild(eleProfileModel);
      }

      if(msg['departments'].length > 0){
        var eleDeptModel = document.createElement('div');
        eleDeptModel.className = 'modal fade';
        eleDeptModel.id = 'deptModal_'+obj.id;
        eleDeptModel.setAttribute('role', 'dialog');
        var urlAssignedDept = "{{url('assignDepatementsToUser')}}";
        var csrfField = '{{ csrf_field() }}';
        var deptModel = '';
        deptModel += '<div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">×</button><h4 class="modal-title">Assigned Departments</h4><b>Note:</b>If remove depatement then data of this user associated with this department will be deleted.</div><form action="'+urlAssignedDept+'" method="POST">'+csrfField+'<div class="modal-body">';
        deptModel += '<div class="form-group">';
          var assignedDepts = JSON.parse("[" + obj.assigned_college_depts + "]");
          $.each(msg['departments'],function(idx,dept){
            if(obj.college_dept_id == dept.id){
              deptModel += '<input type="checkbox" name="departments[]" value="'+dept.id+'" checked disabled>'+dept.name;
            } else if(assignedDepts.indexOf(dept.id) > -1){
              deptModel += '<input type="checkbox" name="departments[]" value="'+dept.id+'" checked>'+dept.name;
            } else {
              if(5 == login_User_Type){
                deptModel += '<input type="checkbox" name="departments[]" value="'+dept.id+'" >'+dept.name;
              } else {
                deptModel += '<input type="checkbox" name="departments[]" value="'+dept.id+'" disabled>'+dept.name;
              }
            }
          });
        deptModel += '</div><input type="hidden" name="user" value="'+obj.id+'"><input type="hidden" name="departments[]" value="'+obj.college_dept_id+'">';
        deptModel += '</div><div class="modal-footer"><button type="submit" class="btn btn-default" style="float:right;">Submit</button></div></form></div></div>';
        eleDeptModel.innerHTML = deptModel;
        eleTr.appendChild(eleDeptModel);
      }

      body.appendChild(eleTr);
    });
  }

  function changeApproveStatus(ele){
    var collegeId = $(ele).data('college_id');
    var departmentId = $(ele).data('department_id');
    var studentId = $(ele).data('student_id');
    var year = $(ele).data('year');
    if(collegeId > 0 && departmentId > 0 && studentId > 0){
      $.ajax({
        method: "POST",
        url: "{{url('changeApproveStatus')}}",
        data: {college_id:collegeId, department_id:departmentId, student_id:studentId, year:year}
      })
      .done(function( msg ) {

      });
    }
  }

  function deleteUser(ele){;
    var studentId = $(ele).data('student_id');
    if(studentId > 0){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure. you want to delete this user?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    document.getElementById('deleteCollegeUser_'+studentId).submit();
                  }
              },
              Cancle: function () {
              }
          }
        });
    }
  }

</script>
<script type="text/javascript">
  $(document).ready(function(){
        setTimeout(function() {
          $('.alert-success').fadeOut('fast');
        }, 10000);
    });
</script>
@stop