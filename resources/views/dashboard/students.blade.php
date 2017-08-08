@extends('dashboard.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Users </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Users Info</li>
      <li class="active">Users </li>
    </ol>
  </section>
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
@stop
@section('dashboard_content')
	<div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="top mrgn_40_btm">
        <div class="container">
          <div class="row">
            <div class="">
            <input type="hidden" id="login_User_Type" name="login_User_Type" value="{{Auth::user()->user_type}}">
            @if(4 == Auth::user()->user_type || 5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
            <div class="col-md-3 mrgn_10_btm" id="showUsers">
              <select class="form-control" id="user" name="user_type" onChange="showDepartments();" required="true">
                <option value="0">Select User Type</option>
                <option value="2">Student</option>
                <option value="3">Lecturer</option>
                @if(5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
                  <option value="4">Hod</option>
                @endif
              </select>
            </div>
            @endif
            @if(5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
              <div class="col-md-3 mrgn_10_btm">
                <select class="form-control" id="dept" onChange="showResult(this);">
                  <option value="0"> Select Department </option>
                  @if(count($collegeDepts) > 0)
                    @foreach($collegeDepts as $collegeDept)
                      <option value="{{$collegeDept->id}}">{{$collegeDept->name}}</option>
                    @endforeach
                  @endif
                </select>
              </div>
            @endif
            @if(3 == Auth::user()->user_type)
              <div class="col-md-3 mrgn_10_btm" id="div_year">
            @else
              <div class="col-md-3 mrgn_10_btm hide" id="div_year">
            @endif
                <select class="form-control" id="selected_year" name="year" onChange="showStudents(this);">
                  <option value="0"> Select Year </option>
                  <option value="1">First Year</option>
                  <option value="2">Second Year</option>
                  <option value="3">Third Year</option>
                  <option value="4">Fourth Year</option>
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
                @if(3 == Auth::user()->user_type)
                  <table  class="" id="student-record">
                @else
                  <table  class="hide" id="student-record">
                @endif
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Name</th>
                      <th>Department</th>
                      <th>Roll No.</th>
                      <th>Approval</th>
                      <th>Delete</th>
                    </tr>
                  </thead>
                  <tbody id="students">
                  </tbody>
                </table>
                <table  class="hide" id="lectures_hods_record">
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
      document.getElementById('students').innerHTML = '';
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
      resetYear();
    }
    if(3 == user_type || 4 == user_type){
      showStudents();
    }
  }

  function resetYear(){
    document.getElementById('selected_year').value = 0;
    document.getElementById('students').innerHTML = '';
  }
  function showStudents(){
    document.getElementById('search_student').value ='';
    if(document.getElementById('selected_year')){
      var year = document.getElementById('selected_year').value;
    } else {
      var year = 0;
    }
    if(document.getElementById("dept")){
        var department = parseInt(document.getElementById("dept").value);
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
          body = document.getElementById('students');
        } else if(3 == user_type || 4 == user_type){
          body = document.getElementById('lecture_hods');
        }
        body.innerHTML = '';
        if( 0 < msg.length){
          renderResult(msg,body,user_type);
        } else {
          var eleTr = document.createElement('tr');
          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = 'No result!';
          eleIndex.setAttribute('colspan', '6');
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
        var department = parseInt(document.getElementById("dept").value);
    } else {
        var department = 0;
    }
    if(document.getElementById("user")){
        var user_type = parseInt(document.getElementById("user").value);
    } else {
        var user_type = 0;
    }
    if(user_type > 0) {
      $.ajax({
        method: "POST",
        url: "{{url('searchStudent')}}",
        data: {student:student, year:year, department:department, user_type:user_type}
      })
      .done(function( msg ) {
        if(2 == user_type){
          body = document.getElementById('students');
          document.getElementById('lecture_hods').innerHTML = '';
        } else if(3 == user_type){
          body = document.getElementById('lecture_hods');
          document.getElementById('students').innerHTML = '';
        }
        body.innerHTML = '';
        if( 0 < msg.length){
          renderResult(msg,body,user_type);
        } else {
          if(2 == user_type){
            body = document.getElementById('students');
            document.getElementById('lecture_hods').innerHTML = '';
          } else if(3 == user_type){
            body = document.getElementById('lecture_hods');
            document.getElementById('students').innerHTML = '';
          }
          var eleTr = document.createElement('tr');
          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = 'No result!';
          eleIndex.setAttribute('colspan', '6');
          eleTr.appendChild(eleIndex);
          body.appendChild(eleTr);
        }
      });
    } else {
      if(2 == user_type){
        body = document.getElementById('students');
        document.getElementById('lecture_hods').innerHTML = '';
        document.getElementById('students').innerHTML = '';
      } else if(3 == user_type){
        body = document.getElementById('lecture_hods');
        document.getElementById('students').innerHTML = '';
        document.getElementById('lecture_hods').innerHTML = '';
      } else {
        document.getElementById('student-record').classList.remove('hide');
        body = document.getElementById('students');
        body.innerHTML = '';
        document.getElementById('lecture_hods').innerHTML = '';
        document.getElementById('students').innerHTML = '';
      }
      var eleTr = document.createElement('tr');
      var eleIndex = document.createElement('td');
      if(0 == user_type) {
        eleIndex.innerHTML = 'Select user type.';
      } else {
        eleIndex.innerHTML = 'Enter search string.';
      }
      if(3 == user_type){
        eleIndex.setAttribute('colspan', '5');
      } else {
        eleIndex.setAttribute('colspan', '6');
      }
      eleTr.appendChild(eleIndex);
      body.appendChild(eleTr);
    }
  }

  function renderResult(msg,body,user_type){
    $.each(msg, function(idx, obj) {
      var eleTr = document.createElement('tr');
      var eleIndex = document.createElement('td');
      eleIndex.innerHTML = idx + 1;
      eleTr.appendChild(eleIndex);

      var eleName = document.createElement('td');
      eleName.innerHTML = '<a href="#studentModal_'+obj.id+'" data-toggle="modal">'+obj.name+'</a>';
      eleTr.appendChild(eleName);

      var eleDept = document.createElement('td');
      eleDept.innerHTML = obj.department;
      eleTr.appendChild(eleDept);
      if(2 == user_type){
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
      var url = "{{url('deleteStudentFromCollege')}}";
      var csrfField = '{{ csrf_field() }}';
      var deleteMethod ='{{ method_field("DELETE") }}';
      eleDelete.innerHTML += '<form id="deleteCollegeUser_'+ obj.id +'" action="'+url+'" method="POST" style="display: none;">'+csrfField+''+deleteMethod+'<input type="hidden" name="student_id" value="'+obj.id+'"><input type="hidden" name="college_id" value="'+obj.college_id+'"><input type="hidden" name="department_id" value="'+obj.college_dept_id+'"><input type="hidden" name="year" value="'+obj.year+'"></form>';
      eleTr.appendChild(eleDelete);

      var eleModel = document.createElement('div');
      eleModel.className = 'modal fade';
      eleModel.id = 'studentModal_'+obj.id;
      eleModel.setAttribute('role', 'dialog');
      var urlStudentTest = "{{url('studentTestResults')}}/"+obj.id;
      var urlStudentCourse = "{{url('studentCourses')}}/"+obj.id;
      var urlStudentPlacement = "{{url('studentPlacement')}}/"+obj.id;
      var modelInnerHTML = '';
      modelInnerHTML='<div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Student Details</h4></div><div class="modal-body">';
      if(2 == user_type ){
        modelInnerHTML +='<div class="form-group"><label>Year:</label> '+obj.year+'</div>';
      }
      modelInnerHTML +='<div class="form-group"><label>Email:</label> '+obj.email+'</div><div class="form-group"><label>Phone:</label> '+obj.phone+'</div><div class="form-group"><a href="'+urlStudentTest+'">Test Result</a></div><div class="form-group"><a href="'+urlStudentCourse+'">Course</a></div>';
      if(2 == user_type ){
        modelInnerHTML +='<div class="form-group"><a href="'+urlStudentPlacement+'">Placement</a></div>';
      }
      modelInnerHTML +='</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div></div></div>';
      eleModel.innerHTML = modelInnerHTML;
      eleTr.appendChild(eleModel);

      body.appendChild(eleTr);
    });
  }

  function changeApproveStatus(ele){
    var collegeId = $(ele).data('college_id');
    var departmentId = $(ele).data('department_id');
    var studentId = $(ele).data('student_id');
    var year = $(ele).data('year');
    if(collegeId > 0 && departmentId > 0 && studentId > 0 && year > 0){
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
        content: 'Are you sure. you want to delete this student?',
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