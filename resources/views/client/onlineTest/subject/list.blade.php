@extends((!empty($loginUser->subdomain))?'client.dashboard':'clientuser.dashboard.teacher_dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Manage Subject </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Online Test </li>
      <li class="active"> Manage Subject </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
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
        <a id="addSubject" href="{{url('createOnlineTestSubject')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Subject">Add New Subject</a>&nbsp;&nbsp;
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
        @if(count($testSubjects)>0)
          @foreach($testSubjects as $index => $testSubject)
          <tr style="overflow: auto;">
            <th scope="row">{{$index + 1}}</th>
            <td>{{$testSubject->name}}</td>
            <td>{{$testSubject->category->name}}</td>
            <td>{{$testSubject->subcategory->name}}</td>
            <td>
              @if(($testSubject->created_by > 0 && empty($loginUser->subdomain) && $loginUser->id == $testSubject->created_by) || (!empty($loginUser->subdomain) &&  $loginUser->id == $testSubject->client_id))
                <a href="{{url('onlinetestsubject')}}/{{$testSubject->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$testSubject->name}}" /></a>
              @endif
            </td>
            <td>
              @if(($testSubject->created_by > 0 && empty($loginUser->subdomain) && $loginUser->id == $testSubject->created_by) || (!empty($loginUser->subdomain) &&  $loginUser->id == $testSubject->client_id))
                <a id="{{$testSubject->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$testSubject->name}}" />
                <form id="deleteSubject_{{$testSubject->id}}" action="{{url('deleteOnlineTestSubject')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="subject_id" value="{{$testSubject->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="7">No subjects are created.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
  </div>

  <script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this subject, all associated papers, questions of this subject will be deleted.',
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