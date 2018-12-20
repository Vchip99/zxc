@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Sub Admin Paper </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-user"></i> Sub Admin </li>
      <li class="active"> Sub Admin Paper </li>
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
          <select name="admins" class=" form-control" onChange="getSubAdminPapers(this.value);">
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
            <th>Paper </th>
            <th>Category </th>
            <th>Sub Category </th>
            <th>Subject </th>
            <th>Sub Admin </th>
            <th>Date </th>
          </tr>
        </thead>
        <tbody id="subadminPapers">
          @if(count($papers) > 0)
            @foreach($papers as $index => $paper)
            <tr style="overflow: auto;">
              <th scope="row">{{$index + $papers->firstItem()}}</th>
              <td>{{$paper->name}}</td>
              <td>{{$paper->category}}</td>
              <td>{{$paper->subcategory}}</td>
              <td>{{$paper->subject}}</td>
              <td>{{$paper->admin}}</td>
              <td>{{$paper->updated_at}}</td>
            </tr>
            @endforeach
          @else
              <tr><td colspan="7">No papers is created.</td></tr>
          @endif
        </tbody>
      </table>
      <div style="float: right;">
        {{$papers->links()}}
      </div>
    </div>
  </div>
<script type="text/javascript">
  function getSubAdminPapers(adminId){
    document.getElementById('subadminPapers').innerHTML = '';
    $.ajax({
      method: "POST",
      url: "{{url('admin/getSubAdminPapers')}}",
      data:{admin_id:adminId}
    })
    .done(function( msg ) {
      body = document.getElementById('subadminPapers');
      body.innerHTML = '';
      if( 0 < msg.length){
        $.each(msg, function(idx, obj) {
          var eleTr = document.createElement('tr');
          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = idx + 1;
          eleTr.appendChild(eleIndex);

          var elePaper = document.createElement('td');
          elePaper.innerHTML = obj.name;
          eleTr.appendChild(elePaper);

          var eleCategory = document.createElement('td');
          eleCategory.innerHTML = obj.category;
          eleTr.appendChild(eleCategory);

          var eleSubCategory = document.createElement('td');
          eleSubCategory.innerHTML = obj.subcategory;
          eleTr.appendChild(eleSubCategory);

          var eleSubject = document.createElement('td');
          eleSubject.innerHTML = obj.name;
          eleTr.appendChild(eleSubject);

          var eleAdmin = document.createElement('td');
          eleAdmin.innerHTML = obj.admin;
          eleTr.appendChild(eleAdmin);

          var eleDate = document.createElement('td');
          eleDate.innerHTML = obj.updated_at;
          eleTr.appendChild(eleDate);
          body.appendChild(eleTr);
        });
      } else {
        var eleTr = document.createElement('tr');
        var eleIndex = document.createElement('td');
        eleIndex.innerHTML = 'No papers';
        eleIndex.setAttribute('colspan',7);
        eleTr.appendChild(eleIndex);
        body.appendChild(eleTr);
      }
    });
  }

</script>
@stop