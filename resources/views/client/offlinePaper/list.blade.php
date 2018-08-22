@extends((!empty($loginUser->subdomain))?'client.dashboard':'clientuser.dashboard.teacher_dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Offline Paper  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-address-book"></i> Batch </li>
      <li class="active"> Manage  Offline Paper </li>
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
      <div>
        <a href="{{url('createOfflinePaper')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add New Paper">Add New Paper</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="" id="">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Paper</th>
          <th>Marks</th>
          <th>Batch</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody id="clientOfflinePapers">
        @if(count($papers) > 0)
          @foreach($papers as $index => $paper)
          <tr>
            <td>{{$index + 1}}</td>
            <td>{{$paper->name}}</td>
            <td>{{$paper->marks}}</td>
            <td>
              {{$paper->batch->name}}
            </td>
            <td>
              @if(($paper->created_by > 0 && empty($loginUser->subdomain) && $loginUser->id == $paper->created_by) || (!empty($loginUser->subdomain) &&  $loginUser->id == $paper->client_id))
                <a href="{{url('offlinePaper')}}/{{$paper->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$paper->name}}" /></a>
              @endif
            </td>
            <td>
              @if(($paper->created_by > 0 && empty($loginUser->subdomain) && $loginUser->id == $paper->created_by) || (!empty($loginUser->subdomain) &&  $loginUser->id == $paper->client_id))
                <a id="{{$paper->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$paper->name}}" /></a>
                <form id="deletePaper_{{$paper->id}}" action="{{url('deleteOfflinePaper')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="paper_id" value="{{$paper->id}}">
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="6">No papers are created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $papers->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this paper, marks of students associated with this paper will be deleted.',
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
              Cancle: function () {
              }
          }
        });
    }

</script>
@stop