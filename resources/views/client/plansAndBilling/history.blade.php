@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Billing  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-inr"></i> Plans & Billing </li>
      <li class="active"> Manage Billing </li>
    </ol>
  </section>
<style type="text/css">
table {
  width: 100%;
  border-collapse: collapse;
  background-color: #fff;
}
/* Zebra striping */
tr:nth-of-type(odd) {
  background: #eee;
}
th {
  background: #333;
  color: white;
  font-weight: bold;
}-3*6
td, th {
  padding: 6px;
  border: 1px solid #ccc;
  text-align: left;
}
/*
Max width before this PARTICULAR table gets nasty
This query will take effect for any screen smaller than 760px
and also iPads specifically.
*/
@media
only screen and (max-width: 760px),
(min-device-width: 768px) and (max-device-width: 1024px)  {

  /* Force table to not be like tables anymore */
  table, thead, tbody, th, td, tr {
    display: block;
  }

  /* Hide table headers (but not display: none;, for accessibility) */
  thead tr {
    position: absolute;
    top: -9999px;
    left: -9999px;
  }

  tr { border: 1px solid #ccc; }

  td {
    /* Behave  like a "row" */
    border: none;
    border-bottom: 1px solid #eee;
    position: relative;
    padding-left: 50%;
  }

  td:before {
    /* Now like a table header */
    position: absolute;
    /* Top/left values mimic padding */
    top: 6px;
    left: 6px;
    width: 45%;
    padding-right: 10px;
    white-space: nowrap;
  }

  /*
  Label the data
  */
  #client_history td:nth-of-type(1):before { content: "#" ; font-weight: bolder; }
  #client_history td:nth-of-type(2):before { content: "START DATE" ; font-weight: bolder; }
  #client_history td:nth-of-type(3):before { content: "END DATE"; font-weight: bolder;}
  #client_history td:nth-of-type(4):before { content: "PLAN";  font-weight: bolder;}
  #client_history td:nth-of-type(5):before { content: "AMOUNT"; font-weight: bolder;}
  #client_history td:nth-of-type(6):before { content: "STATUS"; font-weight: bolder;}
}

/**/
.heading h2
{font-weight: bolder;color: #31708f; text-transform: uppercase;
margin-bottom: 20px;
text-shadow: 0px 3px 0px rgba(50,50,50, .3);}
</style>
@stop
@section('dashboard_content')
  <div class="container">
      <div class="row">
         <div class="col-lg-12" id="">
            <div class="text-center heading" ><h2>Billing History</h2></div>
            <table  class="kullaniciTablosu" id="dataTables-example">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>START DATE</th>
                        <th>END DATE</th>
                        <th>PLAN/SUB CATEGORY*</th>
                        <th>AMOUNT</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                @php
                  $index = 1;
                @endphp
                <tbody id="client_history">
                  @if(count($clientPlans) > 0)
                    @foreach($clientPlans as $clientPlan)
                      <tr class="">
                        <td>{{ $index++ }}</td>
                        <td>{{$clientPlan->start_date}}</td>
                        <td>{{$clientPlan->end_date}}</td>
                        <td>{{$clientPlan->plan->name}}</td>
                        <td>Rs. {{$clientPlan->final_amount}}</td>
                        @if(1 == $clientPlan->plan_id)
                          <td>Paid</td>
                        @else
                          @if('' != $clientPlan->payment_status)
                            <td>{{ $clientPlan->payment_status }}</td>
                          @else
                            <td><button class="btn btn-warning btn-sm" id="{{$clientPlan->id}}" onClick="submitForm(this);" >Pay</button></td>
                            <form method="POST" action="{{ url('continuePayment') }}" id="pay_bill_{{$clientPlan->id}}">
                              {{ csrf_field() }}
                              <input type="hidden" name="plan_id" value="{{$clientPlan->plan_id}}" />
                              <input type="hidden" name="client_plan_id" value="{{$clientPlan->id}}" />
                            </form>
                          @endif
                        @endif
                      </tr>
                    @endforeach
                  @endif
                  @if(count($payableSubCategories) > 0)
                    @foreach($payableSubCategories as  $purchasedPayableSubCategory)
                      <tr class="">
                        <td>{{ $index++ }}</td>
                        <td>{{$purchasedPayableSubCategory->start_date}}</td>
                        <td>{{$purchasedPayableSubCategory->end_date}}</td>
                        <td>{{$purchasedPayableSubCategory->sub_category}}</td>
                        <td>Rs. {{$purchasedPayableSubCategory->admin_price}}</td>
                        <td>Credit</td>
                      </tr>
                    @endforeach
                  @endif
                  @if(count($clientPlans) < 0 && count($payableSubCategories) < 0)
                    <tr class=""><td colspan="6">No Result!</td></tr>
                  @endif
                </tbody>
            </table>
          </div>
       </div>
    </div>
  <script type="text/javascript">
    function submitForm(ele){
      var id = $(ele).attr('id');
      document.getElementById('pay_bill_'+id).submit();
    }
  </script>
@stop