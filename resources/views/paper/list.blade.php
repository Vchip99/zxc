@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Paper </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Online Test </li>
      <li class="active"> Manage Paper </li>
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
      <div id="addPaper">
        <a id="addPaper" href="{{url('admin/createPaper')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Paper">Add New Paper</a>&nbsp;&nbsp;
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
          <th>Verification Code</th>
          <th>Created By </th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($testPapers) > 0)
          @foreach($testPapers as $index => $testPaper)
          <tr style="overflow: auto;">
            <th scope="row">{{$index + $testPapers->firstItem()}}</th>
            <td>{{$testPaper->name}}</td>
            <td>{{$testPaper->category}}</td>
            <td>{{$testPaper->subcategory}}</td>
            <td>{{$testPaper->subject}}</td>
            <td>
              @if(!empty($testPaper->verification_code))
                <a data-toggle="modal" data-target="#model_{{$testPaper->id}}">Yes</a>
                <div class="modal fade" id="model_{{$testPaper->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Verification Code</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body" style="overflow-x: auto;">
                        {{$testPaper->verification_code}}
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
                </div>
              @else
                No
              @endif
            </td>
            <td>{{$testPaper->admin}}</td>
            <td>
              <a href="{{url('admin/paper')}}/{{$testPaper->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$testPaper->name}}" />
                </a>
            </td>
            <td>
              <a id="{{$testPaper->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$testPaper->name}}" />
              <form id="deletePaper_{{$testPaper->id}}" action="{{url('admin/deletePaper')}}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                  {{ method_field('DELETE') }}
                  <input type="hidden" name="paper_id" value="{{$testPaper->id}}">
              </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="8">No paper is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $testPapers->links() }}
    </div>
  </div>
  </div>
</div>

<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'If you delete this paper, all questions associated with this paper will be deleted.',
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