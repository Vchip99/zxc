@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Subject </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Online Test </li>
      <li class="active"> Manage Subject </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container">
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
    <div class="form-group row">
      <div id="addSubjectDiv">
        <a id="addSubject" href="{{url('admin/createSubject')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Subject">Add New Subject</a>&nbsp;&nbsp;
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
          <th>Created By </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($testSubjects) > 0)
          @foreach($testSubjects as $index => $testSubject)
          <tr style="overflow: auto;">
            <th scope="row">{{$index + $testSubjects->firstItem()}}</th>
            <td>{{$testSubject->name}}</td>
            <td>{{$testSubject->category}}</td>
            <td>{{$testSubject->subcategory}}</td>
            <td>{{$testSubject->admin}}</td>
            <td>
              <a href="{{url('admin/subject')}}/{{$testSubject->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$testSubject->name}}" />
                </a>
            </td>
            <td>
              <a id="{{$testSubject->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$testSubject->name}}" />
              <form id="deleteSubject_{{$testSubject->id}}" action="{{url('admin/deleteSubject')}}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                  {{ method_field('DELETE') }}
                  <input type="hidden" name="subject_id" value="{{$testSubject->id}}">
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
        {{ $testSubjects->links() }}
    </div>
  </div>
  </div>

  <script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'If you delete this subject, all associated papers and questions of this subject will be deleted.',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var id = $(ele).attr('id');
                      formId = 'deleteSubject_'+id;
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