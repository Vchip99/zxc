@extends('admin.master')
@section('admin_content')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Manage Subject </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-file-pdf-o"></i> Study Material </li>
      <li class="active"> Manage Subject </li>
    </ol>
  </section>
@stop
  <div class="container ">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div class="form-group row">
      <div >
        <a href="{{url('admin/createStudyMaterialSubject')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Subject"> Add New Subject</a>&nbsp;&nbsp;&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Subject </th>
          <th>Category </th>
          <th>Sub Category </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($subjects) > 0)
          @foreach($subjects as $index => $subject)
          <tr style="overflow: auto;">
            <th scope="row">{{$index + $subjects->firstItem()}}</th>
            <td>{{$subject->name}}</td>
            <td>{{$subject->category->name}}</td>
            <td>{{$subject->subcategory->name}}</td>
            <td>
              <a href="{{url('admin/studyMaterialSubject')}}/{{$subject->id}}/edit"
                    ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$subject->name}}" />
                </a>
            </td>
            <td>
            <a id="{{$subject->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$subject->name}}" />
                </a>
                <form id="deleteStudyMaterialSubject_{{$subject->id}}" action="{{url('admin/deleteStudyMaterialSubject')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="subject_id" value="{{$subject->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
            <tr><td colspan="6">No subject is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{$subjects->links()}}
    </div>
  </div>
  </div>
<script type="text/javascript">
  function confirmDelete(ele){
     $.confirm({
      title: 'Confirmation',
      content: 'If you delete this subject, then all topics associated with this subject will be deleted.',
      type: 'red',
      typeAnimated: true,
      buttons: {
            Ok: {
                text: 'Ok',
                btnClass: 'btn-red',
                action: function(){
                  var id = $(ele).attr('id');
                  formId = 'deleteStudyMaterialSubject_'+id;
                  document.getElementById(formId).submit();
                }
            },
            Cancle: function () {
            }
        }
      });
  }
</script>
@stop