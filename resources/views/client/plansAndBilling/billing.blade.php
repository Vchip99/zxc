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
.heading h2
{font-weight: bolder;color: #31708f; text-transform: uppercase;
margin-bottom: 20px;
text-shadow: 0px 3px 0px rgba(50,50,50, .3);}
h3{text-shadow: 0px 3px 0px rgba(50,50,50, .3);
font-weight: bolder;}
/*.icon-btn { width: 50px;}*/

</style>
@stop
@section('dashboard_content')
  <div class="container text-center">
    <div class="row ">
    <div class="heading "> <h2>BILLING </h2> </div>
    @if(is_object($clientPlan))
      @if('Credit' == $clientPlan->payment_status || 'free' == $clientPlan->payment_status)
      <div class="body">
        <h3> Your Payment Rs. {{$clientPlan->final_amount}} has been successfully completed for {{$clientPlan->start_date}} to {{$clientPlan->end_date}}. Thank you.</h3>
      <br/>
        <!-- <a class="btn btn-md  btn-primary" href="#" style=" width: 100px;"><strong>Paid </strong></a> -->
      </div>
      @else
      <div class="body">
      <h3> Your Payment Rs. {{$clientPlan->final_amount}} has not been Paid for {{$clientPlan->start_date}} to {{$clientPlan->end_date}}. Kindly pay the payment.</h3>
      <br/>
        <button class="btn btn-md btn-primary" style=" width: 100px;" onClick="submitForm(this);" ><strong>Pay </strong></button>
        <form method="POST" action="{{ url('continuePayment') }}" id="pay_bill">
          {{ csrf_field() }}
          <input type="hidden" name="plan_id" value="{{Auth::guard('client')->user()->plan_id}}" />
          <input type="hidden" name="client_plan_id" value="{{$clientPlan->id}}" />
        </form>
      </div>
      <div class="">
        <br/>
       <h4> <b>Note:</b> please pay your bill before {{ $dueDate }} to avoid deactivation of your  account</h4>
      </div>
      @endif
    @else
      <div class="">
        <br/>
       <h4> <b>Note:</b> Currently no bill created.</h4>
      </div>
    @endif
    <!-- /.box-footer-->
    </div>
  </div>
  <script type="text/javascript">
    function submitForm(ele){
      document.getElementById('pay_bill').submit();
    }
  </script>
@stop