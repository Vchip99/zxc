@extends('admin.master')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> All Users </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-group"></i> Users Info </li>
      <li class="active"> All Users </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="top mrgn_40_btm">
        <div class="container">
          <div class="row">
            <div class="col-md-3 mrgn_10_btm">
              <select class="form-control" id="college" name="college" onChange="showUsers(this.value);">
                <option value=""> Select College </option>
                <option value="All">All</option>
                <option value="other">Other</option>
                @if(count($colleges) > 0)
                  @foreach($colleges as $college)
                    <option value="{{$college->id}}">{{$college->name}}</option>
                  @endforeach
                @endif
              </select>
            </div>
            <div class="col-md-3 mrgn_10_btm hide" id="showUsers">
              <select class="form-control" id="user" name="user_type" onChange="showDepartments();" required="true">
                <option value="">Select User</option>
                <option value="2">Student</option>
                <option value="3">Lecturer</option>
                <option value="4">HOD</option>
                <option value="5">Principal / Director</option>
                <option value="6">TNP officer</option>
              </select>
            </div>
            <div class="col-md-3 mrgn_10_btm hide" id="dept">
              <select class="form-control" id="selected_dept" name="departemnt" onChange="showYears();">
                <option value=""> Select Departemnt </option>
                <option value="All">All</option>
              </select>
            </div>
            <div class="col-md-3 mrgn_10_btm hide" id="showYears">
              <select class="form-control" id="selected_year" name="year" onChange="showStudents(this.value);">
                <option value=""> Select Year </option>
                <option value="All">All</option>
                <option value="1">First Year</option>
                <option value="2">Second Year</option>
                <option value="3">Third Year</option>
                <option value="4">Fourth Year</option>
              </select>
            </div>
            <div class="col-md-3 hide" id="search">
              <div class="input-group">
                <input type="text" id="search_student" name="student" class="form-control" placeholder="Search..." onkeyup="searchUsers(this.value);">
                  <span class="input-group-btn">
                    <button type="button" name="search" id="search-btn" class="btn btn-flat" ><i class="fa fa-search"></i>
                    </button>
                  </span>
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
                User Records
              </div>
              <div class="panel-body">
                <table  class="hide" id="other_student_record">
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Name</th>
                      <th>College/Company</th>
                      <th>Approval</th>
                      <th>Delete</th>
                    </tr>
                  </thead>
                  <tbody id="otherStudents" class="">
                  </tbody>
                </table>
                <table  class="hide" id="student-record">
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
                  <tbody id="students" class="">
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
                <table  class="hide" id="principal_tnp_record">
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Name</th>
                      <th>Approval</th>
                      <th>Delete</th>
                    </tr>
                  </thead>
                  <tbody id="principal_tnp" class="">
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

  function showStudents(){
    var college = document.getElementById('college').value;
    if(document.getElementById('user')){
      var user_type = document.getElementById('user').value;
    } else {
      user_type = 0;
    }
    if(document.getElementById('selected_dept')){
      var selected_dept = document.getElementById('selected_dept').value;
    } else {
      var selected_dept = 0;
    }
    if(document.getElementById('selected_year')){
      var selected_year = document.getElementById('selected_year').value;
    } else {
      var selected_year = 0;
    }
    if(user_type && college){
      $.ajax({
        method: "POST",
        url: "{{url('admin/searchUsers')}}",
        data:{college_id:college, user_type:user_type, department_id:selected_dept, selected_year:selected_year}
      })
      .done(function( msg ) {
        if(2 == user_type){
          body = document.getElementById('students');
        }
        if(3 == user_type || 4 == user_type){
          body = document.getElementById('lecture_hods');
        }
        if(5 == user_type || 6 == user_type){
          body = document.getElementById('principal_tnp');
        }
        body.innerHTML = '';
        renderRecords(msg, body);
      });
    }
    document.getElementById('search_student').value = '';
  }

  function searchUsers(student){
    if(student.length > 0){
      var college = document.getElementById('college').value;
      if(document.getElementById('user')){
        var user_type = document.getElementById('user').value;
      } else {
        user_type = 0;
      }
      if(document.getElementById('selected_dept')){
        var selected_dept = document.getElementById('selected_dept').value;
      } else {
        var selected_dept = 0;
      }
      if(document.getElementById('selected_year')){
        var selected_year = document.getElementById('selected_year').value;
      } else {
        var selected_year = 0;
      }
      $.ajax({
          method: "POST",
          url: "{{url('admin/searchUsers')}}",
          data:{college_id:college, user_type:user_type, department_id:selected_dept, selected_year:selected_year, student:student}
        })
        .done(function( msg ) {
          if('other' == college){
            body = document.getElementById('otherStudents');
          } else {
            if(2 == user_type){
              body = document.getElementById('students');
            }
            if(3 == user_type || 4 == user_type){
              body = document.getElementById('lecture_hods');
            }
            if(5 == user_type || 6 == user_type){
              body = document.getElementById('principal_tnp');
            }
          }
          body.innerHTML = '';
          renderRecords(msg, body);
        });
    }else{
      showOtherStudents();
    }
  }

  function showYears(){
    var user_type = document.getElementById('user').value;

    document.getElementById('selected_year').value = '';
    document.getElementById('search_student').value = '';

    document.getElementById('students').innerHTML = '';
    document.getElementById('lecture_hods').innerHTML = '';
    document.getElementById('principal_tnp').innerHTML = '';
    if(3 == user_type || 4 == user_type){
      document.getElementById('showYears').classList.add('hide');
      showStudents()
    } else {
      document.getElementById('showYears').classList.remove('hide');
    }
  }

  function showDepartments(){
    var college = document.getElementById('college').value;
    var user_type = document.getElementById('user').value;
    document.getElementById('students').innerHTML = '';
    document.getElementById('lecture_hods').innerHTML = '';
    document.getElementById('principal_tnp').innerHTML = '';
    document.getElementById('search').classList.remove('hide');
    if((2 == user_type || 3 == user_type || 4 == user_type)){
      document.getElementById('selected_year').value = '';
      document.getElementById('dept').classList.remove('hide');
      if(2 == user_type){
        document.getElementById('student-record').classList.remove('hide');
        document.getElementById('lectures_hods_record').classList.add('hide');
        document.getElementById('principal_tnp_record').classList.add('hide');
        document.getElementById('showYears').classList.remove('hide');
      }
      if(3 == user_type || 4 == user_type){
        document.getElementById('student-record').classList.add('hide');
        document.getElementById('principal_tnp_record').classList.add('hide');
        document.getElementById('lectures_hods_record').classList.remove('hide');
        document.getElementById('showYears').classList.add('hide');
      }
      if(college > 0){
        $.ajax({
          method: "POST",
          url: "{{url('admin/getDepartments')}}",
          data:{college:college}
        })
        .done(function( msg ) {
          document.getElementById('dept').classList.remove('hide');
          select = document.getElementById('selected_dept');
          select.innerHTML = '';
          var opt = document.createElement('option');
          opt.value = '';
          opt.innerHTML = 'Select Department';
          select.appendChild(opt);
          var optAll = document.createElement('option');
          optAll.value = 'All';
          optAll.innerHTML = 'All';
          select.appendChild(optAll);
          if( 0 < msg.length){
            $.each(msg, function(idx, obj) {
                var opt = document.createElement('option');
                opt.value = obj.id;
                opt.innerHTML = obj.name;
                select.appendChild(opt);
            });
          }
        });
      } else {
        document.getElementById('selected_dept').selectedIndex = '';
        document.getElementById('selected_year').selectedIndex = '';
      }
    } else {
      document.getElementById('dept').classList.add('hide');
      document.getElementById('showYears').classList.add('hide');
      document.getElementById('principal_tnp_record').classList.remove('hide');
      document.getElementById('student-record').classList.add('hide');
      document.getElementById('lectures_hods_record').classList.add('hide');
      document.getElementById('selected_dept').selectedIndex = '';
      document.getElementById('selected_year').selectedIndex = '';
      showStudents();
    }
    document.getElementById('search_student').value = '';
  }

  function showUsers(collegeId){
    if('other' == collegeId){
      document.getElementById('showUsers').classList.add('hide');
      document.getElementById('user').value = '';
      document.getElementById('other_student_record').classList.remove('hide');
      document.getElementById('student-record').classList.add('hide');
      document.getElementById('dept').classList.add('hide');
      document.getElementById('showYears').classList.add('hide');
      document.getElementById('principal_tnp_record').classList.add('hide');
      document.getElementById('student-record').classList.add('hide');
      document.getElementById('lectures_hods_record').classList.add('hide');
      document.getElementById('search').classList.remove('hide');
      showOtherStudents();
    } else {
      document.getElementById('showUsers').classList.remove('hide');
      document.getElementById('user').value = '';
      document.getElementById('other_student_record').classList.add('hide');
      document.getElementById('search').classList.add('hide');
    }
    document.getElementById('search_student').value = '';
  }


  function showOtherStudents(){
    $.ajax({
      method: "POST",
      url: "{{url('admin/showOtherStudents')}}"
    })
    .done(function( msg ) {
      body = document.getElementById('otherStudents');
      body.innerHTML = '';
      renderRecords(msg, body);
    });
  }

  function renderRecords(msg, body){
    if( 0 < msg.length){
      $.each(msg, function(idx, obj) {
        var eleTr = document.createElement('tr');
        var eleIndex = document.createElement('td');
        eleIndex.innerHTML = idx + 1;
        eleTr.appendChild(eleIndex);

        var eleName = document.createElement('td');
        if(2 == obj.user_type){
          eleName.innerHTML = '<a href="#studentModal_'+obj.id+'" data-toggle="modal">'+obj.name+'</a>';
        } else {
          eleName.innerHTML = obj.name;
        }
        eleTr.appendChild(eleName);
        if(obj.college_id > 0 && 2 == obj.user_type || 3 == obj.user_type || 4 == obj.user_type){
          var eleDept = document.createElement('td');
          eleDept.innerHTML = obj.department;
          eleTr.appendChild(eleDept);

          if(2 == obj.user_type ){
            var eleRollNo = document.createElement('td');
            eleRollNo.innerHTML = obj.roll_no;
            eleTr.appendChild(eleRollNo);
          }
        } else if('other' == obj.college_id){
          var eleDept = document.createElement('td');
          eleDept.innerHTML = obj.other_source;
          eleTr.appendChild(eleDept);
          if(obj.roll_no){
            var eleRollNo = document.createElement('td');
            eleRollNo.innerHTML = obj.roll_no;
            eleTr.appendChild(eleRollNo);
          }
        }

        var eleApprove = document.createElement('td');
        approveInnerHTML = '<input type="checkbox" value="" data-student_id="'+ obj.id +'" data-college_id="'+ obj.college_id +'" data-college_dept_id="'+ obj.college_dept_id +'" data-user_type="'+ obj.user_type +'" data-year="'+ obj.year +'" onclick="changeApproveStatus(this);"';
        if( 1 == obj.admin_approve){
          approveInnerHTML += 'checked = checked';
        }
        approveInnerHTML += '>';
        eleApprove.innerHTML = approveInnerHTML;
        eleTr.appendChild(eleApprove);

        var eleDelete = document.createElement('td');
        eleDelete.innerHTML = '<button class="btn btn-danger btn-xs delet-bt delet-btn" data-title="Delete" data-toggle="modal" data-target="#delete" data-student_id="'+ obj.id +'" data-college_id="'+ obj.college_id +'" data-college_dept_id="'+ obj.college_dept_id +'" data-user_type="'+ obj.user_type +'" data-year="'+ obj.year +'" onclick="deleteStudent(this);" ><span class="fa fa-trash-o" data-placement="top" data-toggle="tooltip" title="Delete"></span></button>';
        eleTr.appendChild(eleDelete);
        if(2 == obj.user_type){
          var eleModel = document.createElement('div');
          eleModel.className = 'modal';
          eleModel.id = 'studentModal_'+obj.id;
          eleModel.setAttribute('role', 'dialog');
          var urlStudentTest = "{{url('admin/userTestResults')}}/"+obj.id;
          var urlStudentCourse = "{{url('admin/userCourses')}}/"+obj.id;
          var urlStudentPlacement = "{{url('admin/userPlacement')}}/"+obj.id;
          var urlStudentVideo = "{{url('admin/userVideo')}}/"+obj.id;
          var modelInnerHTML = '';
          modelInnerHTML='<div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button>';
          if(2 == obj.user_type ){
            modelInnerHTML +='<h4 class="modal-title">Student Details</h4>';
          } else if(3 == obj.user_type ){
            modelInnerHTML +='<h4 class="modal-title">Lecturer Details</h4>';
          } else if(4 == obj.user_type ){
            modelInnerHTML +='<h4 class="modal-title">Hod Details</h4>';
          } else if(5 == obj.user_type ){
            modelInnerHTML +='<h4 class="modal-title">Principal Details</h4>';
          } else if(6 == obj.user_type ){
            modelInnerHTML +='<h4 class="modal-title">Tnp Details</h4>';
          }
          modelInnerHTML +='</div><div class="modal-body">';
          if('other' != obj.college_id && 2 == obj.user_type ){
            modelInnerHTML +='<div class="form-group"><label>Year:</label> '+obj.year+'</div>';
          }
          modelInnerHTML +='<div class="form-group"><label>Email:</label> '+obj.email+'</div><div class="form-group"><label>Phone:</label> '+obj.phone+'</div><div class="form-group"><a href="'+urlStudentTest+'">Test Result</a></div><div class="form-group"><a href="'+urlStudentCourse+'">Course</a></div>';
          if(2 == obj.user_type ){
            modelInnerHTML +='<div class="form-group"><a href="'+urlStudentPlacement+'">Placement</a></div>';
            modelInnerHTML +='<div class="form-group"><a href="'+urlStudentVideo+'">Student Video Url</a></div>';
          }
          modelInnerHTML +='</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div></div></div>';
          eleModel.innerHTML = modelInnerHTML;
          eleTr.appendChild(eleModel);
        }
        body.appendChild(eleTr);
      });
    } else {
      var eleTr = document.createElement('tr');
      var eleIndex = document.createElement('td');
      eleIndex.innerHTML = 'No result!';
      eleIndex.setAttribute('colspan', '6');
      eleTr.appendChild(eleIndex);
      body.appendChild(eleTr);
    }
  }

  function deleteStudent(ele){;
    var studentId = $(ele).data('student_id');
    var college_id = $(ele).data('college_id');
    var department_id = $(ele).data('college_dept_id');
    var user_type = $(ele).data('user_type');
    var selected_year = $(ele).data('year');
    if(2 == user_type){
      message = 'Are you sure. you want to permanently delete this student?'
    } else {
      message = 'Are you sure. you want to permanently delete this User?'
    }
    if(studentId > 0){
      $.confirm({
        title: 'Confirmation',
        content: message,
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    $.ajax({
                      method: "POST",
                      url: "{{url('admin/deleteStudent')}}",
                      data:{student_id:studentId,college_id:college_id,department_id:department_id,user_type:user_type,selected_year:selected_year}
                    })
                    .done(function( msg ) {
                      if('other' == college_id && 2 == user_type){
                        body = document.getElementById('otherStudents');
                      } else if(college_id > 0 && 2 == user_type){
                        body = document.getElementById('students');
                      } else if(college_id > 0 && (3 == user_type || 4 == user_type)){
                        body = document.getElementById('lecture_hods');
                      }  else if(college_id > 0 && (5 == user_type || 6 == user_type)){
                        body = document.getElementById('principal_tnp');
                      }
                      body.innerHTML = '';
                      renderRecords(msg['students'], body);
                    });
                  }
              },
              Cancle: function () {
              }
          }
        });
    }
  }

  function changeApproveStatus(ele){
    var studentId = $(ele).data('student_id');
    var college_id = $(ele).data('college_id');
    var department_id = $(ele).data('college_dept_id');
    var user_type = $(ele).data('user_type');
    var selected_year = $(ele).data('year');
    if(studentId > 0){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure. you want to change approval status?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    $.ajax({
                      method: "POST",
                      url: "{{url('admin/changeUserApproveStatus')}}",
                      data: {student_id:studentId,college_id:college_id,department_id:department_id,user_type:user_type,selected_year:selected_year}
                    })
                    .done(function( msg ) {
                      if('other' == college_id && 2 == user_type){
                        body = document.getElementById('otherStudents');
                      } else if(college_id > 0 && 2 == user_type){
                        body = document.getElementById('students');
                      } else if(college_id > 0 && (3 == user_type || 4 == user_type)){
                        body = document.getElementById('lecture_hods');
                      }  else if(college_id > 0 && (5 == user_type || 6 == user_type)){
                        body = document.getElementById('principal_tnp');
                      }
                      body.innerHTML = '';
                      renderRecords(msg, body);
                    });
                  }
              },
              Cancle: function () {
                if('checked' == $(ele).attr('checked')){
                  $(ele).prop('checked', 'checked');
                } else {
                  $(ele).prop('checked', '');
                }
              }
          }
        });

    }
  }
</script>
@stop