@extends('clientuser.dashboard.dashboard')
@section('dashboard_header')
  <link href="{{asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Offline Payments </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Payments</li>
      <li class="active"> My Offline Payments </li>
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
		                <select id="batch" class="form-control" name="batch" onChange="showPayments(this);" title="Batch">
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
		               		Payments
	              		</div>
		              	<div class="panel-body">
			                <table  class="" id="dataTables-example">
			                  <thead>
			                    <tr>
			                      <th>Sr. No.</th>
                            <th>Batch</th>
                            <th>Date</th>
			                      <th>Payment</th>
                            <th>Receipt</th>
			                    </tr>
			                  </thead>
			                  <tbody  id="payments">
			                    @if(count($payments) > 0)
                            @php
                              $total = 0;
                            @endphp
			                      @foreach($payments as $index => $payment)
			                        <tr style="overflow: auto;">
			                          <td>{{$index + 1}}</td>
			                          <td>{{$payment->batch->name}}</td>
                                <td>{{date('Y-m-d',strtotime($payment->created_at))}}</td>
                                <td>{{$payment->amount}}</td>
                                <td><a href="{{ url('offlineReceipt')}}/{{$payment->id}}" target="_blank">Receipt</a></td>
			                        </tr>
                              @php
                                $total += $payment->amount;
                              @endphp
			                      @endforeach
                            <tr style="overflow: auto;">
                              <td colspan="2"></td>
                              <td>Total</td>
                              <td colspan="2">{{$total}}</td>
                            </tr>
			                    @elseif(0 == count($payments))
			                      <tr>
			                        <td colspan="5">No Payments.</td>
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
 function showPayments(ele){
    var batchId = parseInt($(ele).val());
    $.ajax({
      method: "POST",
      url: "{{url('getOfflinePaymentsByBatchIdByUserId')}}",
      data: {batch_id:batchId}
    })
    .done(function( result ) {
      body = document.getElementById('payments');
      body.innerHTML = '';
      var total=0;
      if( result.length){
        $.each(result, function(idx, obj) {
          var eleTr = document.createElement('tr');
          var eleIndex = document.createElement('td');
          eleIndex.innerHTML = idx + 1;
          eleTr.appendChild(eleIndex);

          var eleBatch = document.createElement('td');
          eleBatch.innerHTML = obj.batch;
          eleTr.appendChild(eleBatch);

          var eleDate = document.createElement('td');
          eleDate.innerHTML = obj.date;
          eleTr.appendChild(eleDate);

          var eleAmount = document.createElement('td');
          eleAmount.innerHTML = obj.amount;
          eleTr.appendChild(eleAmount);
          total += parseInt(obj.amount);

          var eleReceipt = document.createElement('td');
          eleReceipt.innerHTML = '<a href="{{url('offlineReceipt')}}/'+obj.id+'" target="_blank">Receipt</a>';
          eleTr.appendChild(eleReceipt);

          body.appendChild(eleTr);
        });
        var eleTr = document.createElement('tr');
        var eleIndex = document.createElement('td');
        eleIndex.setAttribute('colspan', '2');
        eleTr.appendChild(eleIndex);

        var eleTotal = document.createElement('td');
        eleTotal.innerHTML = 'Total';
        eleTr.appendChild(eleTotal);

        var eleAmount = document.createElement('td');
        eleAmount.innerHTML = total;
        eleAmount.setAttribute('colspan', '2');
        eleTr.appendChild(eleAmount);

        body.appendChild(eleTr);
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