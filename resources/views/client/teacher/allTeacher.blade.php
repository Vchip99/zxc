@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> All Teachers </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-graduation-cap"></i> Teachers Info</li>
      <li class="active"> All Teachers </li>
    </ol>
  </section>
  <style type="text/css">
    .modal-body {
      overflow-x: auto;
    }
  </style>
@stop
@section('dashboard_content')
  <div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="container">
        @if(Session::has('message'))
          <div class="alert alert-success" id="message">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              {{ Session::get('message') }}
          </div>
        @endif
        <div class="row">
          <div class="col-lg-12">
            <div class="panel panel-info">
              <div class="panel-heading text-center">
                All Teachers
              </div>
              <div class="panel-body">
                <table id="allTeachers">
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Name</th>
                      <th>Phone</th>
                      <th>Email/User Id</th>
                      <th>Client Approve</th>
                      <th>Assigned Modules</th>
                      <th>Delete</th>
                    </tr>
                  </thead>
                  <tbody>
                    @if(count($clientTeachers) > 0)
                      @foreach($clientTeachers as  $index => $clientTeacher)
                        <tr style="overflow: auto;">
                          <td> {{ $index + 1 }} </td>
                          <td>{{ $clientTeacher->name }}</td>
                          <td>{{ $clientTeacher->phone }}</td>
                          <td>{{ $clientTeacher->email }}</td>
                          <td>
                            @if(1 == $clientTeacher->client_approve)
                              <input type="checkbox" value="" data-client_user_id="{{ $clientTeacher->id }}" data-client_id="{{ $clientTeacher->client_id }}" onclick="changeApproveStatus(this);" checked="checked">
                            @else
                              <input type="checkbox" value="" data-client_user_id="{{ $clientTeacher->id }}" data-client_id="{{ $clientTeacher->client_id }}" onclick="changeApproveStatus(this);">
                            @endif
                          </td>
                          <td><a href="#assignedModules_{{$clientTeacher->id}}" data-toggle="modal">Approve/Unapprove Modules</a></td>
                          <td><button class="btn btn-danger btn-xs delet-bt delet-btn" data-title="Delete" data-toggle="modal" data-target="#delete" data-client_user_id="{{ $clientTeacher->id }}" data-client_id="{{ $clientTeacher->client_id }}" onclick="deleteTeacher(this);" ><span class="fa fa-trash-o" data-placement="top" data-toggle="tooltip" title="Delete"></span></button></td>
                          <form id="deleteClientTeacher_{{$clientTeacher->id}}" action="{{url('deleteClientTeacher')}}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <input type="hidden" name="teacher_id" value="{{ $clientTeacher->id }}">
                          </form>
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
  @if(count($clientTeachers) > 0)
    @foreach($clientTeachers as  $index => $clientTeacher)
      <div class="modal" id="assignedModules_{{ $clientTeacher->id }}" role="dialog" style="display: none;">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">×</button>
              <h4 class="modal-title">Assigned Modules for {{ $clientTeacher->name }} </h4>
            </div>
            <div class="modal-body">
              <table class="" id="client_user_{{ $clientTeacher->id }}">
                <thead>
                  <tr>
                    <th>Sr. No.</th>
                    <th>Assigned Module</th>
                    <th>Approve Status</th>
                  </tr>
                </thead>
                <tbody id="" class="">
                  @if(count($allModules) > 0)
                    <form method="POST" action="{{ url('changeClientTeacherModuleStatus') }}">
                      {{ csrf_field() }}
                      @foreach($allModules as  $moduleId => $moduleName)
                      <tr>
                        <td> {{ $moduleId }} </td>
                        <td>{{ $moduleName }}</td>
                        <td>
                          @php
                            if(!empty($clientTeacher->assigned_modules)){
                              $assignedModules = explode(',', $clientTeacher->assigned_modules);
                            } else {
                              $assignedModules = [];
                            }
                          @endphp
                          @if(in_array($moduleId, $assignedModules))
                            <input type="checkbox" name="add_modules[]" value="{{$moduleId}}" checked="checked">
                          @else
                            <input type="checkbox" name="add_modules[]" value="{{$moduleId}}" >
                          @endif
                        </td>
                      </tr>
                      @endforeach
                      <input type="hidden" name="client_user_id" value="{{ $clientTeacher->id }}">
                      <tr><td colspan="3"><input type="submit" name="Submit" style="float: right;"></td></tr>
                    </form>
                  @endif
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  @endif
<script type="text/javascript">

  function deleteTeacher(ele){
    var client_user_id = $(ele).data('client_user_id');
    var client_id = $(ele).data('client_id');
    if(client_user_id > 0 && client_id > 0){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure. you want to permanently delete this teacher?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    formId = 'deleteClientTeacher_'+client_user_id;
                    document.getElementById(formId).submit();
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
    if(client_id > 0 && client_user_id > 0 ){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure. you want to change teacher approval?',
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