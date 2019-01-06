@extends('admin.master')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Un Approve Users </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-group"></i> Users Info </li>
      <li class="active"> Un Approve Users </li>
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
                <option value="0"> Select College </option>
                <option value="all">All</option>
                @if(count($colleges) > 0)
                  @foreach($colleges as $college)
                    <option value="{{$college->id}}">{{$college->name}}</option>
                  @endforeach
                @endif
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-lg-12" id="all-result">
            <div class="panel panel-info">
              <div class="panel-heading text-center">
                Un Approve Users
              </div>
              <div class="panel-body">
                <table  class="" id="unapprove-users">
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>College/Company</th>
                      <th>Name</th>
                      <th>Designation</th>
                      <th>Approval</th>
                    </tr>
                  </thead>
                  <tbody id="students" class="">
                    @if(count($upApproveUsers) > 0)
                      @foreach($upApproveUsers as $index => $upApproveUser)
                        <tr style="overflow: auto;">
                          <td>{{ $index + 1}}</td>
                          @if($upApproveUser->college_id > 0)
                            <td>{{ $upApproveUser->college->name }}</td>
                          @else
                            <td>{{ $upApproveUser->collegeName }}</td>
                          @endif
                          <td>{{ $upApproveUser->name }}</td>
                          <td>
                            @if(2 == $upApproveUser->user_type)
                              Student
                            @elseif(3 == $upApproveUser->user_type)
                              Lecturer
                            @elseif(4 == $upApproveUser->user_type)
                              Hod
                            @elseif(5 == $upApproveUser->user_type)
                              Director
                            @elseif(6 == $upApproveUser->user_type)
                              TNP
                            @endif
                          </td>
                          <td>
                              <input type="checkbox" data-student_id="{{$upApproveUser->id}}" data-college_id="{{$upApproveUser->college_id}}" onclick="approveUser(this);">
                          </td>
                        </tr>
                      @endforeach
                    @else
                      <tr><td colspan="6"> No Result</td></tr>
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

  function showUsers(){
    var college = document.getElementById('college').value;
    $.ajax({
      method: "POST",
      url: "{{url('admin/unapproveUsersByCollegeId')}}",
      data:{college_id:college}
    })
    .done(function( msg ) {
      body = document.getElementById('students');
      body.innerHTML = '';
      renderRecords(msg, body);
    });
  }

  function renderRecords(msg, body){
    if( 0 < msg['users'].length){
      $.each(msg['users'], function(idx, obj) {
        var eleTr = document.createElement('tr');
        var eleIndex = document.createElement('td');
        eleIndex.innerHTML = idx + 1;
        eleTr.appendChild(eleIndex);

        var eleCollege = document.createElement('td');
        if(obj.college_id > 0){
          eleCollege.innerHTML = msg['colleges'][obj.college_id];
        } else {
          eleCollege.innerHTML = obj.collegeName;
        }
        eleTr.appendChild(eleCollege);

        var eleName = document.createElement('td');
        eleName.innerHTML = obj.name;
        eleTr.appendChild(eleName);

        var eleDesignation = document.createElement('td');
        if(2 == obj.user_type){
          eleDesignation.innerHTML = 'Student';
        } else if(3 == obj.user_type){
          eleDesignation.innerHTML = 'Lecturer';
        } else if(4 == obj.user_type){
          eleDesignation.innerHTML = 'Hod';
        } else if(5 == obj.user_type){
          eleDesignation.innerHTML = 'Director';
        } else if(6 == obj.user_type){
          eleDesignation.innerHTML = 'TNP';
        }
        eleTr.appendChild(eleDesignation);

        var eleApprove = document.createElement('td');
        eleApprove.innerHTML = '<input type="checkbox" data-student_id="'+ obj.id +'" data-college_id="'+ obj.college_id +'" onclick="approveUser(this);">';
        eleTr.appendChild(eleApprove);

        body.appendChild(eleTr);
      });
    } else {
      var eleTr = document.createElement('tr');
      var eleIndex = document.createElement('td');
      eleIndex.innerHTML = 'No result!';
      eleIndex.setAttribute('colspan', '5');
      eleTr.appendChild(eleIndex);
      body.appendChild(eleTr);
    }
  }

  function approveUser(ele){
    var student_id = $(ele).data('student_id');
    var college_id = $(ele).data('college_id');
    var college = document.getElementById('college').value;
    if(student_id > 0){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure. you want to approv this user?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    $.ajax({
                      method: "POST",
                      url: "{{url('admin/approveUser')}}",
                      data: {student_id:student_id,college_id:college_id, selected_college_id:college}
                    })
                    .done(function( msg ) {
                      body = document.getElementById('students');
                      body.innerHTML = '';
                      renderRecords(msg, body);
                    });
                  }
              },
              Cancel: function () {
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