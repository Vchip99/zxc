@extends((!empty($loginUser->subdomain))?'client.dashboard':'clientuser.dashboard.teacher_dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Offline Marks </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-address-book"></i> Batch </li>
      <li class="active"> Manage Offline Marks </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container ">
    @if(count($errors) > 0)
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
   <form action="{{url('assignOfflinePaperMarks')}}" method="POST">
    {{ csrf_field() }}
      <div class="row">
        <b>Note: For absent student, keep mark as empty.</b>
      </div><br/>
      <div class="row">
        <div class="form-group">
            <div class="col-md-3">
                <div style="margin-bottom: 10px">
                    <select class="form-control" name="batch" id="batch" onChange="selectBatchPaper(this);">
                        <option value="">Select Batch</option>
                        @if(count($batches) > 0)
                            @foreach($batches as $batch)
                                <option value="{{$batch->id}}">{{$batch->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div style="margin-bottom: 10px">
                    <select class="form-control" name="paper" id="paper" onChange="selectBatchStudent(this);">
                        <option value="">Select Paper</option>
                    </select>
                </div>
            </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12" id="all-result">
          <div class="panel panel-info">
            <div class="panel-heading text-center">
              <span class="">Students</span>
            </div>
            <div class="panel-body">
              <table  class="" id="">
                <thead>
                  <tr>
                    <th>Sr. No.</th>
                    <th>Name</th>
                    <th>Mark</th>
                    <th>OutOff</th>
                  </tr>
                </thead>
                <tbody id="client_offline_paper_marks" class="">
                </tbody>
              </table>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12">
              <button type="submit" class="btn btn-primary" style="float: right;width: 90px !important;">Submit</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
<script type="text/javascript">

  function selectBatchPaper(ele){
    var batchId = parseInt($(ele).val());
    var currentToken = $('meta[name="csrf-token"]').attr('content');
    if(batchId){
      $.ajax({
          method:'POST',
          url: "{{url('getOfflinePapersByBatchId')}}",
          data:{_token:currentToken,batch_id:batchId}
      }).done(function( msg ) {
        select = document.getElementById('paper');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Paper';
        select.appendChild(opt);
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              opt.setAttribute('data-marks', obj.marks);
              select.appendChild(opt);
          });
        }
      });
    }
  }
  function selectBatchStudent(ele){
    var paperId = parseInt($(ele).val());
    var paperMarks = $(ele).find(':selected').data('marks');
    var batchId = document.getElementById('batch').value;
    var currentToken = $('meta[name="csrf-token"]').attr('content');
    if(paperId && batchId){
      $.ajax({
          method:'POST',
          url: "{{url('getBatchStudentsAndMarksByBatchIdByPaperId')}}",
          data:{_token:currentToken,batch_id:batchId,paper_id:paperId}
      }).done(function( result ) {
          var users = document.getElementById('client_offline_paper_marks');
          users.innerHTML = '';
          if(result['batchUsers'].length){
            $.each(result['batchUsers'], function(idx, obj) {
              if(result['studentMarks'][obj.id]){
                users.innerHTML +='<tr class="student" id="div_student_'+obj.id+'" ><td>'+(idx + 1)+'</td><td>'+obj.name+'</td><td><input type="text" name="'+obj.id+'" id="student_'+obj.id+'" value="'+result['studentMarks'][obj.id].marks+'"></td><td><input type="text" name="total_marks" value="'+result['studentMarks'][obj.id].total_marks+'" readonly></td></tr>';
              } else {
                users.innerHTML +='<tr class="student" id="div_student_'+obj.id+'" ><td>'+(idx + 1)+'</td><td>'+obj.name+'</td><td><input type="text" name="'+obj.id+'" id="student_'+obj.id+'" value=""></td><td><input type="text" name="total_marks" value="'+paperMarks+'" readonly></td></tr>';
              }
            });
          } else {
            users.innerHTML = '<tr class="student"><td colspan="4">No Result!</td></tr>';
          }
      });
    }
  }
</script>
@stop