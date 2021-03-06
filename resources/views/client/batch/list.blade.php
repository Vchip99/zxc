@extends((!empty($loginUser->subdomain))?'client.dashboard':'clientuser.dashboard.teacher_dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Batch </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-address-book"></i> Batch </li>
      <li class="active"> Manage Batch </li>
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
        <a href="{{url('createBatch')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add New Batch">Add New Batch</a>&nbsp;&nbsp;
      </div>
    </div>
    <div>
      <table class="" id="clientBatchTable">
        <thead class="thead-inverse">
          <tr>
            <th>#</th>
            <th>Batch</th>
            <th>Edit</th>
            <th>Delete</th>
          </tr>
        </thead>
        <tbody id="batches">
          @if(count($batches) > 0)
            @foreach($batches as $index => $batch)
            <tr style="overflow: auto;">
              <td>{{$index + 1}}</td>
              <td><a href="#batchModal_{{ $batch->id }}" data-toggle="modal">{{$batch->name}}</a></td>
              <td>
                @if(($batch->created_by > 0 && empty($loginUser->subdomain) && $loginUser->id == $batch->created_by) || (!empty($loginUser->subdomain) &&  $loginUser->id == $batch->client_id))
                  <a href="{{url('batch')}}/{{$batch->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$batch->name}}" /></a>
                @endif
              </td>
              <td>
                @if(($batch->created_by > 0 && empty($loginUser->subdomain) && $loginUser->id == $batch->created_by) || (!empty($loginUser->subdomain) &&  $loginUser->id == $batch->client_id))
                  <a id="{{$batch->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$batch->name}}" />
                  </a>
                  <form id="deleteBatch_{{$batch->id}}" action="{{url('deleteBatch')}}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <input type="hidden" name="batch_id" value="{{$batch->id}}">
                  </form>
                @endif
              </td>
            </tr>
            @endforeach
          @else
            <tr><td colspan="4">No batches are created.</td></tr>
          @endif
        </tbody>
      </table>
      @if(count($batches) > 0)
        @foreach($batches as $index => $batch)
          <div class="modal" id="batchModal_{{ $batch->id }}" role="dialog" style="display: none;">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">×</button>
                  <h4 class="modal-title">Batch Users </h4>
                </div>
                <div class="modal-body">
                  <table class="">
                    <thead>
                      <tr>
                        <th>Sr. No.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                      </tr>
                    </thead>
                    <tbody id="" class="">
                      @if(isset($batchUsers[$batch->id]) && count($batchUsers[$batch->id]) > 0)
                        @foreach($batchUsers[$batch->id] as  $index => $batchUser)
                          <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $batchUser['name'] }}</td>
                            <td>{{ $batchUser['email'] }}</td>
                            <td>{{ $batchUser['phone'] }}</td>
                          </tr>
                        @endforeach
                      @else
                        <tr><td colspan="4">No students in this batch</td></tr>
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      @endif
      <div style="float: right;">
        {{ $batches->links() }}
      </div>
    </div>
  </div>
<script type="text/javascript">
    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'If you delete this batch, all associated subjects, topics, assignments and its answers, attendance, offline papers will be deleted. Also offline uploaded transactions will be deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deleteBatch_'+id;
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