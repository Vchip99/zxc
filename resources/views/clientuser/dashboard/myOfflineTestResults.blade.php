@extends('clientuser.dashboard.dashboard')
@section('dashboard_header')
  <link href="{{asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Offline Test Results </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Online Test</li>
      <li class="active"> My Offline Test Results </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
<div class="content-wrapper v-container tab-content" >
    <div class="">
      	<div class="container">
	        <div class="row">
	          	<div class="mrgn_20_btm">
	              	<div class="col-sm-4 mrgn_10_btm">
		                <select id="batch" class="form-control" name="batch" onChange="showResult(this);" title="Batch">
		                  	<option value="">Select Batch</option>
		                  	@if(count($batches) > 0)
			                    @foreach($batches as $batch)
			                      <option value="{{$batch->id}}">{{$batch->name}}</option>
			                    @endforeach
		                  	@endif
		            	</select>
	              	</div>
	            </div>
	          	<div class="col-lg-12" id="all-result">
		            <div class="panel panel-info">
		              	<div class="panel-heading text-center">
		               		RESULT
	              		</div>
		              	<div class="panel-body">
			                <table  class="" id="dataTables-example">
			                  <thead>
			                    <tr>
			                      <th>Sr. No.</th>
			                      <th>Batch</th>
			                      <th>Paper</th>
			                      <th>Marks</th>
			                      <th>Rank</th>
			                    </tr>
			                  </thead>
			                  <tbody  id="test-result">
			                    @if(count($results) > 0)
			                      @foreach($results as $index => $result)
			                        <tr class="">
			                          <td>{{$index + 1}}</td>
			                          <td>{{$result->batch->name}}</td>
			                          <td>{{$result->paper->name}}</td>
			                          <td>
                                  @if('' != trim($result->marks))
                                    {{$result->marks}} / {{$result->total_marks}}
                                  @else
                                    absent
                                  @endif
                                </td>
			                          <td>
                                  @if('' != trim($result->marks))
                                    {{$result->rank()}}
                                  @else
                                    absent
                                  @endif
                                </td>
			                        </tr>
			                      @endforeach
			                    @elseif(0 == count($results))
			                      <tr class="">
			                        <td colspan="5">No result for selected user.</td>
			                      </tr>
			                    @endif
			                  </tbody>
			                </table>
		               	</div>
		            </div>
	          	</div>
	        </div>
      	</div>
    </div>
</div>
<script type="text/javascript">
 function showResult(ele){
    var batchId = parseInt($(ele).val());
    $.ajax({
      method: "POST",
      url: "{{url('showUserOfflineTestResultsByBatchIdByUserId')}}",
      data: {batch_id:batchId}
    })
    .done(function( msg ) {
      body = document.getElementById('test-result');
      body.innerHTML = '';
      if( msg['scores'] && 0 < msg['scores'].length){
        $.each(msg['scores'], function(idx, obj) {
          var eleTr = document.createElement('tr');
          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = idx + 1;
          eleTr.appendChild(eleIndex);

          var eleBatch = document.createElement('td');
          eleBatch.innerHTML = obj.batch;
          eleTr.appendChild(eleBatch);

          var elePaper = document.createElement('td');
          elePaper.innerHTML = obj.paper;
          eleTr.appendChild(elePaper);

          var eleScore = document.createElement('td');
          if(obj.marks){
            eleScore.innerHTML = obj.marks+'/'+obj.total_marks;
          } else {
            eleScore.innerHTML = 'absent';
          }
          eleTr.appendChild(eleScore);

          var eleRank = document.createElement('td');
          eleRank.innerHTML = msg['ranks'][obj.id];
          eleTr.appendChild(eleRank);
          body.appendChild(eleTr);
        });
      } else {
        var eleTr = document.createElement('tr');
        var eleIndex = document.createElement('td');
        eleIndex.innerHTML = 'No result!';
        eleIndex.setAttribute('colspan', '5');
        eleTr.appendChild(eleIndex);
        body.appendChild(eleTr);
      }
    });
  }
</script>
@stop