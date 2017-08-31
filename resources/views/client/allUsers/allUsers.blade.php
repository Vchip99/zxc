@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Users Info </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> All Users </li>
      <li class="active"> Users Info </li>
    </ol>
  </section>
@stop
@section('dashboard_content')

  <div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="top mrgn_40_btm">
        <div class="container">
          <div class="row">
            <div class="col-md-3 mrgn_10_btm">
              <select class="form-control" id="course" name="course" onChange="showStudents(this.value);">
                <option value="0"> Select Courses </option>
                @if(count($instituteCourses) > 0)
                  @foreach($instituteCourses as $instituteCourse)
                    <option value="{{$instituteCourse->id}}">{{$instituteCourse->name}}</option>
                  @endforeach
                @endif
              </select>
            </div>
            <div class="col-md-3 " id="search">
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
                <table  class="" id="client_users">
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Name</th>
                      <th>Client Approve</th>
                      <th>Course</th>
                      <th>Courses Permission</th>
                      <th>Delete</th>
                    </tr>
                  </thead>
                  <tbody id="mobile_client_users" class="">
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

  function showStudents(courseId){
    if(courseId > 0){
      $.ajax({
        method: "POST",
        url: "{{url('searchUsers')}}",
        data:{course_id:courseId}
      })
      .done(function( msg ) {
        body = document.getElementById('mobile_client_users');
        body.innerHTML = '';
        renderRecords(msg, body);
      });
    }
    document.getElementById('search_student').value = '';
  }
  function searchUsers(student){
    var courseId = document.getElementById('course').value;
    if(student.length > 0){
      $.ajax({
          method: "POST",
          url: "{{url('searchUsers')}}",
          data:{student:student,course_id:courseId}
        })
        .done(function( msg ) {
          body = document.getElementById('mobile_client_users');
          body.innerHTML = '';
          renderRecords(msg, body);
        });
    }
  }

    function renderRecords(msg, body){
    if( 0 < msg['users'].length){
      $.each(msg['users'], function(idx, obj) {
        var eleTr = document.createElement('tr');
        var eleIndex = document.createElement('td');
        eleIndex.innerHTML = idx + 1;
        eleTr.appendChild(eleIndex);

        var eleName = document.createElement('td');
        eleName.innerHTML = '<a href="#studentModal_'+obj.id+'" data-toggle="modal">'+obj.name+'</a>';
        eleTr.appendChild(eleName);

        var eleApprove = document.createElement('td');
        approveInnerHTML = '<input type="checkbox" value="" data-client_user_id="'+ obj.id +'" data-client_id="'+ obj.client_id +'" onclick="changeApproveStatus(this);"';
        if( 1 == obj.client_approve){
          approveInnerHTML += 'checked = checked';
        }
        approveInnerHTML += '>';
        eleApprove.innerHTML = approveInnerHTML;
        eleTr.appendChild(eleApprove);

        var eleCourseName = document.createElement('td');
        eleCourseName.innerHTML = obj.courseName;
        eleTr.appendChild(eleCourseName);

        var elePermission = document.createElement('td');
        elePermission.innerHTML = '<a class="btn" id="'+obj.id +'_'+obj.course_id+'" onclick="showPermissions(this);">Hide/Show </a>';;
        eleTr.appendChild(elePermission);

        var eleDelete = document.createElement('td');
        eleDelete.innerHTML = '<button class="btn btn-danger btn-xs delet-bt delet-btn" data-title="Delete" data-toggle="modal" data-target="#delete" data-client_user_id="'+ obj.id +'" data-client_id="'+ obj.client_id +'" onclick="deleteStudent(this);" ><span class="fa fa-trash-o" data-placement="top" data-toggle="tooltip" title="Delete"></span></button>';
        eleTr.appendChild(eleDelete);

        var eleModel = document.createElement('div');
        eleModel.className = 'modal';
        eleModel.id = 'studentModal_'+obj.id;
        eleModel.setAttribute('role', 'dialog');
        var urlStudentTest = "{{url('userTestResults')}}/"+obj.id+'/'+obj.course_id;
        var urlStudentCourse = "{{url('userCourses')}}/"+obj.id+'/'+obj.course_id;
        var urlStudentPlacement = "{{url('userPlacement')}}/"+obj.id+'/'+obj.course_id;
        var urlStudentVideo = "{{url('userVideo')}}/"+obj.id+'/'+obj.course_id;
        var modelInnerHTML = '';
        modelInnerHTML='<div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button>';
        modelInnerHTML +='<h4 class="modal-title">Student Details</h4>';

        modelInnerHTML +='<div class="form-group"><div class="form-group"><label>Email:</label> '+obj.email+'</div><div class="form-group"><label>Phone:</label> '+obj.phone+'</div><div class="form-group"><a href="'+urlStudentTest+'">Test Result</a></div><div class="form-group"><a href="'+urlStudentCourse+'">Course</a></div>';
        modelInnerHTML +='<div class="form-group"><a href="'+urlStudentPlacement+'">Placement</a></div>';
        modelInnerHTML +='<div class="form-group"><a href="'+urlStudentVideo+'">Student Video Url</a></div>';

        modelInnerHTML +='</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div></div></div>';
        eleModel.innerHTML = modelInnerHTML;
        eleTr.appendChild(eleModel);

        body.appendChild(eleTr);

        var elePerTr = document.createElement('tr');
        elePerTr.id = 'user_permission_'+ obj.id +'_'+obj.course_id;
        elePerTr.className = 'hide expant-child';
        var elePerTd = document.createElement('td');
        elePerTd.setAttribute('colspan', '6');

        var eleTable = document.createElement('table');
        eleTable.className = 'inner-table-border';
        eleTable.id = 'user_permission_'+ obj.id +'_'+obj.course_id;
        var eleThead = document.createElement('thead');
        var eleTheadTr = document.createElement('tr');
        eleTheadTr.innerHTML='<th>Course Name</th><th>Test Permission</th><th>Course Permission</th>';
        eleThead.appendChild(eleTheadTr);
        eleTable.appendChild(eleThead);
        var eleTbody = document.createElement('tbody');
        eleTbody.id = "mobile_client_user_permission"

        var userId = obj.id;
        var courseId = obj.course_id;
        if(undefined !== msg['institueCourses'][userId] && undefined !== msg['institueCourses'][userId][courseId]){
          var obj = msg['institueCourses'][userId][courseId];
          var eleTr = document.createElement('tr');
          var eleCourseName = document.createElement('td');
          eleCourseName.innerHTML = obj.courseName;
          eleTr.appendChild(eleCourseName);

          var eleTestPermission = document.createElement('td');
          testPermissionInnerHTML = '<input id="test_'+obj.client_user_id+'" type="checkbox" value="" data-client_user_id="'+ obj.client_user_id +'" data-client_id="'+ obj.client_id +'" data-client_institute_course_id="'+ obj.client_institute_course_id +'" data-permission_type="test" onclick="changePermissionStatus(this);"';
          if( 1 == obj.test_permission){
            testPermissionInnerHTML += 'checked = checked';
          }
          testPermissionInnerHTML += '>';
          eleTestPermission.innerHTML = testPermissionInnerHTML;
          eleTr.appendChild(eleTestPermission);

          var eleCoursePermission = document.createElement('td');
          coursePermissionInnerHTML = '<input id="course_'+obj.client_user_id+'" type="checkbox" value="" data-client_user_id="'+ obj.client_user_id +'" data-client_id="'+ obj.client_id +'" data-client_institute_course_id="'+ obj.client_institute_course_id +'" data-permission_type="course" onclick="changePermissionStatus(this);"';
          if( 1 == obj.course_permission){
            coursePermissionInnerHTML += 'checked = checked';
          }
          coursePermissionInnerHTML += '>';
          eleCoursePermission.innerHTML = coursePermissionInnerHTML;
          eleTr.appendChild(eleCoursePermission);
          eleTbody.appendChild(eleTr);
        }
        eleTable.appendChild(eleTbody);
        elePerTd.appendChild(eleTable);
        elePerTr.appendChild(elePerTd);
        body.appendChild(elePerTr);

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

  function showPermissions(ele){
    var id = $(ele).prop('id');
    if(document.getElementById('user_permission_'+id).classList.contains('hide')){
      document.getElementById('user_permission_'+id).classList.remove('hide');
    } else {
      document.getElementById('user_permission_'+id).classList.add('hide');
    }
  }

  function changePermissionStatus(ele){
    var client_user_id = $(ele).data('client_user_id');
    var client_id = $(ele).data('client_id');
    var client_institute_course_id = $(ele).data('client_institute_course_id');
    var permission_type = $(ele).data('permission_type');
    if(client_id > 0 && client_user_id > 0 && client_institute_course_id > 0){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure. you want to change course permission?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    $.ajax({
                      method: "POST",
                      url: "{{url('changeClientPermissionStatus')}}",
                      data: {client_id:client_id,client_user_id:client_user_id,client_institute_course_id:client_institute_course_id,permission_type:permission_type}
                    })
                    .done(function( msg ) {
                      if('false' == msg){
                        if('checked' == $(ele).attr('checked')){
                          $(ele).prop('checked', 'checked');
                        } else {
                          $(ele).prop('checked', '');
                        }
                      }
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

  function deleteStudent(ele){;
    var client_user_id = $(ele).data('client_user_id');
    var client_id = $(ele).data('client_id');
    var courseId = document.getElementById('course').value;
    var student = document.getElementById('search_student').value;
    if(client_user_id > 0 && client_id > 0){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure. you want to permanently delete this student?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    $.ajax({
                      method: "POST",
                      url: "{{url('deleteStudent')}}",
                      data:{client_id:client_id,client_user_id:client_user_id,student:student,course_id:courseId}
                    })
                    .done(function( msg ) {
                      body = document.getElementById('mobile_client_users');
                      body.innerHTML = '';
                      renderRecords(msg, body);
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
    var client_user_id = $(ele).data('client_user_id');
    var client_id = $(ele).data('client_id');
    if(client_id > 0 && client_user_id > 0){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure. you want to change user approval?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    $.ajax({
                      method: "POST",
                      url: "{{url('changeClientUserApproveStatus')}}",
                      data: {client_id:client_id,client_user_id:client_user_id}
                    })
                    .done(function( msg ) {
                      if('false' == msg){
                        if('checked' == $(ele).attr('checked')){
                          $(ele).prop('checked', 'checked');
                        } else {
                          $(ele).prop('checked', '');
                        }
                      }
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