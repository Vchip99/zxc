@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Sub Admin Sub Categories </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-user"></i> Sub Admin </li>
      <li class="active"> Sub Admin Sub Categories </li>
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
    <div class="row" style="padding-bottom: 10px;">
      <div class="col-sm-2">
        @if(count($admins) > 0)
          <select name="admins" class=" form-control" onChange="getSubAdminSubCategories(this.value);">
            <option value=""> Select Sub Admin</option>
            @foreach($admins as $admin)
              <option value="{{$admin->id}}"> {{$admin->name}} </option>
            @endforeach
          </select>
        @endif
      </div>
      <a href="{{ url('admin/manageSubadminSubCategories')}}" class="btn " style="border-radius: 0px !important;border: 1px solid black;" title="Sub Categories"><i class="fa fa-list"></i></a>&nbsp;
      <a href="{{ url('admin/manageSubadminSubjects')}}" class="btn " style="border-radius: 0px !important;border: 1px solid black;" title="Subjects"><i class="fa fa-book"></i></a>&nbsp;
      <a href="{{ url('admin/manageSubadminPapers')}}" class="btn " style="border-radius: 0px !important;border: 1px solid black;" title="Papers"><i class="fa fa-files-o"></i></a>&nbsp;
    </div>
    <div class="row">
      <table class="table admin_table">
        <thead class="thead-inverse">
          <tr>
            <th>#</th>
            <th>Sub Category </th>
            <th>Category </th>
            <th>Sub Admin </th>
            <th>Approve </th>
            <th>Date </th>
          </tr>
        </thead>
        <tbody id="subadminSubCategories">
          @if(count($subCategories) > 0)
            @foreach($subCategories as $index => $subCategory)
            <tr style="overflow: auto;">
              <th scope="row">{{$index + $subCategories->firstItem()}}</th>
              <td>{{$subCategory->name}}</td>
              <td>{{$subCategory->category}}</td>
              <td>{{$subCategory->admin}}</td>
              <td>
                @if(1 == $subCategory->admin_approve)
                  <input type="checkbox" data-id="{{$subCategory->id}}" onClick="toggleApprove(this);" checked="true">
                @else
                  <input type="checkbox" data-id="{{$subCategory->id}}" onClick="toggleApprove(this);">
                @endif
              </td>
              <td>{{$subCategory->updated_at}}</td>
            </tr>
            @endforeach
          @else
              <tr><td colspan="6">No course is created.</td></tr>
          @endif
        </tbody>
      </table>
      <div style="float: right;">
        {{$subCategories->links()}}
      </div>
    </div>
  </div>
<script type="text/javascript">
  function getSubAdminSubCategories(adminId){
    document.getElementById('subadminSubCategories').innerHTML = '';
    $.ajax({
      method: "POST",
      url: "{{url('admin/getSubAdminSubCategories')}}",
      data:{admin_id:adminId}
    })
    .done(function( msg ) {
      body = document.getElementById('subadminSubCategories');
      body.innerHTML = '';
      if( 0 < msg.length){
        $.each(msg, function(idx, obj) {
          var eleTr = document.createElement('tr');
          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = idx + 1;
          eleTr.appendChild(eleIndex);

          var eleSubCategory = document.createElement('td');
          eleSubCategory.innerHTML = obj.name;
          eleTr.appendChild(eleSubCategory);

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
        eleIndex.innerHTML = 'No Sub Categories';
        eleIndex.setAttribute('colspan',6);
        eleTr.appendChild(eleIndex);
        body.appendChild(eleTr);
      }
    });
  }

  function toggleApprove(ele){
    var subcategoryId = $(ele).data('id');
    $.ajax({
      method: "POST",
      url: "{{url('admin/changeSubAdminSubCategoryApproval')}}",
      data:{sub_category_id:subcategoryId}
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