@extends((!empty($loginUser->subdomain))?'client.dashboard':'clientuser.dashboard.teacher_dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Manage Paper </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Online Test </li>
      <li class="active"> Manage Paper </li>
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
      <div id="addPaper">
        <a id="addPaper" href="{{url('createOnlineTestSubjectPaper')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Paper">Add New Paper</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Paper </th>
          <th>Category </th>
          <th>Sub Category </th>
          <th>Subject </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($testPapers)>0)
          @foreach($testPapers as $index => $testPaper)
          <tr style="overflow: auto;">
            <th scope="row">{{$index + 1}}</th>
            <td>{{$testPaper->name}}</td>
            <td>{{$testPaper->category->name}}</td>
            <td>{{$testPaper->subcategory->name}}</td>
            <td>{{$testPaper->subject->name}}</td>
            <td>
              @if(($testPaper->created_by > 0 && empty($loginUser->subdomain) && $loginUser->id == $testPaper->created_by) || (!empty($loginUser->subdomain) &&  $loginUser->id == $testPaper->client_id))
                <a href="{{url('onlinetestsubjectpaper')}}/{{$testPaper->id}}/edit"><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$testPaper->name}}" /></a>
              @endif
            </td>
            <td>
              @if(($testPaper->created_by > 0 && empty($loginUser->subdomain) && $loginUser->id == $testPaper->created_by) || (!empty($loginUser->subdomain) &&  $loginUser->id == $testPaper->client_id))
                <a id="{{$testPaper->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$testPaper->name}}" />
                <form id="deletePaper_{{$testPaper->id}}" action="{{url('deleteOnlineTestSubjectPaper')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="paper_id" value="{{$testPaper->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="8">No papers are created.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
  </div>
</div>

<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this paper, all associated question of this paper will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deletePaper_'+id;
                    document.getElementById(formId).submit();
                  }
              },
              Cancel: function () {
              }
          }
        });
    }

</script>
@stop