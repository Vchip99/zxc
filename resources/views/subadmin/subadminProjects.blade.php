@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Sub Admin Projects </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-user"></i> Sub Admin </li>
      <li class="active"> Sub Admin Projects </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <div class="container ">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div class="row col-sm-2" style="padding-bottom: 10px;">
    @if(count($admins) > 0)
      <select name="admins" class=" form-control" onChange="getSubAdminCourses(this.value);">
        <option value=""> Select Sub Admin</option>
        @foreach($admins as $admin)
          <option value="{{$admin->id}}"> {{$admin->name}} </option>
        @endforeach
      </select>
    @endif
    </div>
    <div class="row">
      <table class="table admin_table">
        <thead class="thead-inverse">
          <tr>
            <th>#</th>
            <th>Project </th>
            <th>Category </th>
            <th>Sub Admin </th>
            <th>Approve </th>
            <th>Date </th>
          </tr>
        </thead>
        <tbody id="subadminProjects">
          @if(count($projects) > 0)
            @foreach($projects as $index => $project)
            <tr style="overflow: auto;">
              <th scope="row">{{$index + $projects->firstItem()}}</th>
              <td>{{$project->name}}</td>
              <td>{{$project->category}}</td>
              <td>{{$project->admin}}</td>
              <td>
                @if(1 == $project->admin_approve)
                  <input type="checkbox" data-id="{{$project->id}}" onClick="toggleApprove(this);" checked="true">
                @else
                  <input type="checkbox" data-id="{{$project->id}}" onClick="toggleApprove(this);">
                @endif
              </td>
              <td>{{$project->updated_at}}</td>
            </tr>
            @endforeach
          @else
              <tr><td colspan="5">No project is created.</td></tr>
          @endif
        </tbody>
      </table>
      <div style="float: right;">
        {{$projects->links()}}
      </div>
    </div>
  </div>
<script type="text/javascript">
  function getSubAdminCourses(adminId){
    document.getElementById('subadminProjects').innerHTML = '';
    $.ajax({
      method: "POST",
      url: "{{url('admin/getSubAdminProjects')}}",
      data:{admin_id:adminId}
    })
    .done(function( msg ) {
      body = document.getElementById('subadminProjects');
      body.innerHTML = '';
      if( 0 < msg.length){
        $.each(msg, function(idx, obj) {
          var eleTr = document.createElement('tr');
          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = idx + 1;
          eleTr.appendChild(eleIndex);

          var eleProject = document.createElement('td');
          eleProject.innerHTML = obj.name;
          eleTr.appendChild(eleProject);

          var eleCategory = document.createElement('td');
          eleCategory.innerHTML = obj.category;
          eleTr.appendChild(eleCategory);

          var eleAdmin = document.createElement('td');
          eleAdmin.innerHTML = obj.admin;
          eleTr.appendChild(eleAdmin);

          var eleApprove = document.createElement('td');
          if(1 == obj.admin_approve){
            eleApprove.innerHTML = '<input type="checkbox" data-id="'+obj.id+'" onClick="toggleApprove(this);" checked="true">';
          } else {
            eleApprove.innerHTML = '<input type="checkbox" data-id="'+obj.id+'" onClick="toggleApprove(this);">';
          }
          eleTr.appendChild(eleApprove);

          var eleDate = document.createElement('td');
          eleDate.innerHTML = obj.updated_at;
          eleTr.appendChild(eleDate);
          body.appendChild(eleTr);
        });
      } else {
        var eleTr = document.createElement('tr');
        var eleIndex = document.createElement('td');
        eleIndex.innerHTML = 'No project';
        eleIndex.setAttribute('colspan',5);
        eleTr.appendChild(eleIndex);
        body.appendChild(eleTr);
      }
    });
  }

  function toggleApprove(ele){
    var projectId = $(ele).data('id');
    $.ajax({
      method: "POST",
      url: "{{url('admin/changeSubAdminProjectApproval')}}",
      data:{project_id:projectId}
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

</script>
@stop