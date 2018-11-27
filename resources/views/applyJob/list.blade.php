@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Apply Job</h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-link"></i> Placement </li>
      <li class="active"> Apply Job</li>
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
    <div id="addSubCategoryDiv">
      <a id="addSubCategory" href="{{url('admin/createApplyJob')}}" type="button" class="btn btn-primary" style="float: right;" title="Add Apply Job">Add Apply Job</a>&nbsp;&nbsp;
    </div>
  </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Company Name</th>
          <th>Job Description</th>
          <th>Edit Apply Job</th>
          <th>Delete Apply Job</th>
        </tr>
      </thead>
      <tbody>
        @if(count($applyJobs)>0)
          @foreach($applyJobs as $index => $applyJob)
          <tr style="overflow: auto;">
            <td scope="row">{{$index + $applyJobs->firstItem()}}</td>
            <td>{{$applyJob->company}}</td>
            <td>{!! mb_strimwidth( $applyJob->job_description , 0, 400, '...') !!}</td>
            <td>
              <a href="{{url('admin/applyJob')}}/{{$applyJob->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$applyJob->company}}" />
                </a>
            </td>
            <td>
                <a id="{{$applyJob->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$applyJob->company}}" />
                <form id="deleteJob_{{$applyJob->id}}" action="{{url('admin/deleteApplyJob')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="apply_job_id" value="{{$applyJob->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="4">No apply job is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $applyJobs->links() }}
    </div>
  </div>
  </div>

  <script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'Do you want to delete this company job.',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var id = $(ele).attr('id');
                      formId = 'deleteJob_'+id;
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