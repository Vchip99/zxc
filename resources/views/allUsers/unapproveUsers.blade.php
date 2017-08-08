@extends('admin.master')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Un Approve Users </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Users Info </li>
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
                      <th>College</th>
                      <th>Name</th>
                      <th>Approval</th>
                    </tr>
                  </thead>
                  <tbody id="students" class="">
                    @if(count($upApproveUsers) > 0)
                      @foreach($upApproveUsers as $index => $upApproveUser)
                        <tr>
                          <td>{{ $index + 1}}</td>
                          <td>{{ $upApproveUser->college }}</td>
                          <td>{{ $upApproveUser->name }}</td>
                          <td>
                              <input type="checkbox" data-student_id="{{$upApproveUser->id}}" data-college_id="{{$upApproveUser->college_id}}" onclick="approveUser(this);">
                          </td>
                        </tr>
                      @endforeach
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
    if( 0 < msg.length){
      $.each(msg, function(idx, obj) {
        var eleTr = document.createElement('tr');
        var eleIndex = document.createElement('td');
        eleIndex.innerHTML = idx + 1;
        eleTr.appendChild(eleIndex);

        var eleCollege = document.createElement('td');
        eleCollege.innerHTML = obj.college;
        eleTr.appendChild(eleCollege);

        var eleName = document.createElement('td');
        eleName.innerHTML = obj.name;
        eleTr.appendChild(eleName);

        var eleApprove = document.createElement('td');
        eleApprove.innerHTML = '<input type="checkbox" data-student_id="'+ obj.id +'" data-college_id="'+ obj.college_id +'" onclick="approveUser(this);">';
        eleTr.appendChild(eleApprove);

        body.appendChild(eleTr);
      });
    } else {
      var eleTr = document.createElement('tr');
      var eleIndex = document.createElement('td');
      eleIndex.innerHTML = 'No result!';
      eleIndex.setAttribute('colspan', '4');
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