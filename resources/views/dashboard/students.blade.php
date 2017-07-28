@extends('dashboard.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Students </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Students Dashboard</li>
      <li class="active">Students </li>
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
            @if(5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
              <div class="col-md-3 mrgn_10_btm">
                <select class="form-control" id="dept" onChange="resetYear(this);">
                  <option value="0"> Select Department </option>
                  @if(count($collegeDepts) > 0)
                    @foreach($collegeDepts as $collegeDept)
                      <option value="{{$collegeDept->id}}">{{$collegeDept->name}}</option>
                    @endforeach
                  @endif
                </select>
              </div>
            @endif
              <div class="col-md-3 mrgn_10_btm">
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
                  <input type="text" name="student" class="form-control" placeholder="Search..." onkeyup="searchStudent(this.value);">
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
               Student Records
              </div>
              <div class="panel-body">
                <table  class="" id="student-record">
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
                    @if(count($students) > 0)
                      @foreach($students as $index => $student)
                        <tr class="">
                          <td>{{$index + 1}}</td>
                          <td><a href="#studentModal_{{$student->id}}" data-toggle="modal">{{$student->name}}</a></td>
                          <td>{{$student->department->name}}</td>
                          <td>{{$student->roll_no}}</td>
                          <td>
                            <input type="checkbox" value="" data-student_id="{{$student->id}}" data-college_id="{{$student->college_id}}" data-department_id="{{$student->college_dept_id}}" data-year="{{$student->year}}" onclick="changeApproveStatus(this);"
                            @if(1 == $student->admin_approve)
                              checked = checked
                            @endif
                            >
                          </td>
                          <td><button class="btn btn-danger btn-xs delet-bt delet-btn" data-title="Delete" data-toggle="modal" data-target="#delete" data-student_id="{{$student->id}}" onclick="deleteUser(this);" ><span class="fa fa-trash-o" data-placement="top" data-toggle="tooltip" title="Delete"></span></button>
                          <form id="deleteCollegeUser_{{$student->id}}" action="{{url('deleteStudent')}}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <input type="hidden" name="student_id" value="{{$student->id}}">
                            <input type="hidden" name="college_id" value="{{$student->college_id}}">
                            <input type="hidden" name="department_id" value="{{$student->college_dept_id}}">
                            <input type="hidden" name="year" value="{{$student->year}}">
                          </form>
                          </td>
                          <div class="modal fade" id="studentModal_{{$student->id}}" role="dialog">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  <h4 class="modal-title">Student Details</h4>
                                </div>
                                <div class="modal-body">
                                  <div class="form-group">
                                    <label>Email:</label> {{$student->email}}
                                  </div>
                                  <div class="form-group">
                                    <label>Phone:</label> {{$student->phone}}
                                  </div>
                                  <div class="form-group">
                                    <a href="{{url('studentTestResults')}}/{{$student->id}}">Test Result</a>
                                  </div>
                                  <div class="form-group">
                                    <a href="{{url('studentCourses')}}/{{$student->id}}">Course</a>
                                  </div>
                                  <div class="form-group">
                                    <a href="{{url('studentPlacement')}}/{{$student->id}}"">Placement</a>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </tr>
                      @endforeach
                    @else
                      <tr><td colspan="6">No Students.</td></tr>
                    @endif
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
  function resetYear(){
    document.getElementById('selected_year').value = 0;
    document.getElementById('students').innerHTML = '';
  }
  function showStudents(){
    var year = document.getElementById('selected_year').value;
    if(document.getElementById("dept")){
        var department = parseInt(document.getElementById("dept").value);
    } else {
        var department = 0;
    }
      $.ajax({
        method: "POST",
        url: "{{url('searchStudent')}}",
        data: {year:year, department:department}
      })
      .done(function( msg ) {
        body = document.getElementById('students');
        body.innerHTML = '';
        if( 0 < msg.length){
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

            var eleRollNo = document.createElement('td');
            eleRollNo.innerHTML = obj.roll_no;
            eleTr.appendChild(eleRollNo);

            var eleApprove = document.createElement('td');
            approveInnerHTML = '<input type="checkbox" value="" data-student_id="'+obj.id+'" data-college_id="'+obj.college_id+'" data-department_id="'+obj.college_dept_id+'" data-year="'+obj.year+'" onclick="changeApproveStatus(this);"';
            if( 1 == obj.admin_approve){
              approveInnerHTML += 'checked = checked';
            }
            approveInnerHTML += '>';
            eleApprove.innerHTML = approveInnerHTML;
            eleTr.appendChild(eleApprove);

            var eleDelete = document.createElement('td');
            eleDelete.innerHTML = '<button class="btn btn-danger btn-xs delet-bt delet-btn" data-title="Delete" data-toggle="modal" data-target="#delete" data-student_id="'+ obj.id +'" onclick="deleteUser(this);" ><span class="glyphicon glyphicon-trash" data-placement="top" data-toggle="tooltip" title="Delete"></span></button>';
            var url = "{{url('deleteStudent')}}";
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
            modelInnerHTML='<div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Student Details</h4></div><div class="modal-body"><div class="form-group"><label>Year:</label> '+obj.year+'</div><div class="form-group"><label>Email:</label> '+obj.email+'</div><div class="form-group"><label>Phone:</label> '+obj.phone+'</div><div class="form-group"><a href="'+urlStudentTest+'">Test Result</a></div><div class="form-group"><a href="'+urlStudentCourse+'">Course</a></div>';
            if(obj.college_id > 0 && 2 == obj.user_type ){
              modelInnerHTML +='<div class="form-group"><a href="'+urlStudentPlacement+'">Placement</a></div>';
            }
            modelInnerHTML +='</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div></div></div>';
            eleModel.innerHTML = modelInnerHTML;
            eleTr.appendChild(eleModel);

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
      });
  }

  function searchStudent(student){
    var year = document.getElementById('selected_year').value;
    if(document.getElementById("dept")){
        var department = parseInt(document.getElementById("dept").value);
    } else {
        var department = 0;
    }
    if(student.length > 3) {
      $.ajax({
        method: "POST",
        url: "{{url('searchStudent')}}",
        data: {student:student, year:year, department:department}
      })
      .done(function( msg ) {
        body = document.getElementById('students');
        body.innerHTML = '';
        if( 0 < msg.length){
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

            var eleRollNo = document.createElement('td');
            eleRollNo.innerHTML = obj.roll_no;
            eleTr.appendChild(eleRollNo);

            var eleApprove = document.createElement('td');
            approveInnerHTML = '<input type="checkbox" value="" data-student_id="'+obj.id+'" data-college_id="'+obj.college_id+'" data-department_id="'+obj.college_dept_id+'" data-year="'+obj.year+'" onclick="changeApproveStatus(this);"';
            if( 1 == obj.admin_approve){
              approveInnerHTML += 'checked = checked';
            }
            approveInnerHTML += '>';
            eleApprove.innerHTML = approveInnerHTML;
            eleTr.appendChild(eleApprove);

            var eleDelete = document.createElement('td');
            eleDelete.innerHTML = '<button class="btn btn-danger btn-xs delet-bt delet-btn" data-title="Delete" data-toggle="modal" data-target="#delete" data-student_id="'+ obj.id +'" onclick="deleteUser(this);" ><span class="glyphicon glyphicon-trash" data-placement="top" data-toggle="tooltip" title="Delete"></span></button>';
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
            var modelInnerHTML = '';
            modelInnerHTML='<div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Student Details</h4></div><div class="modal-body"><div class="form-group"><label>Year:</label> '+obj.year+'</div><div class="form-group"><label>Email:</label> '+obj.email+'</div><div class="form-group"><label>Phone:</label> '+obj.phone+'</div><div class="form-group"><a href="'+urlStudentTest+'">Test Result</a></div><div class="form-group"><a href="'+urlStudentCourse+'">Course</a></div>';
            if(obj.college_id > 0 && 2 == obj.user_type ){
              modelInnerHTML +='<div class="form-group"><a href="'+urlStudentPlacement+'">Placement</a></div>';
            }
            modelInnerHTML +='</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div></div></div>';
            eleModel.innerHTML = modelInnerHTML;
            eleTr.appendChild(eleModel);

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
      });
    }
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