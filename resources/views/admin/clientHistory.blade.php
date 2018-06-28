@extends('admin.master')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Client Billing History </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-inr"></i> Plans & Billing </li>
      <li class="active"> Client Billing History </li>
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
}
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
  #client_history td:nth-of-type(3):before { content: "START DATE" ; font-weight: bolder; }
  #client_history td:nth-of-type(4):before { content: "END DATE"; font-weight: bolder;}
  #client_history td:nth-of-type(5):before { content: "PLAN/Sub Category";  font-weight: bolder;}
  #client_history td:nth-of-type(6):before { content: "AMOUNT"; font-weight: bolder;}
  #client_history td:nth-of-type(7):before { content: "STATUS"; font-weight: bolder;}

}

/**/
.heading h2
{font-weight: bolder;color: #31708f; text-transform: uppercase;
margin-bottom: 20px;
text-shadow: 0px 3px 0px rgba(50,50,50, .3);}
</style>
@stop
@section('admin_content')
  <div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="top mrgn_40_btm">
        <div class="container">
          <div class="row">
            <div class="col-md-3 mrgn_10_btm">
              <select class="form-control" id="client" name="client" onChange="showClientHistory(this);">
                <option value="0"> Select Client </option>
                <option value="All"> All </option>
                @if(count($clients) > 0)
                  @foreach($clients as $client)
                    <option value="{{$client->id}}">{{$client->name}}</option>
                  @endforeach
                @endif
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-lg-12" id="">
            <div class="text-center heading" ><h2>Billing History</h2></div>
            <table  class="kullaniciTablosu" >
              <thead>
                  <tr>
                      <th>#</th>
                      <th>Client</th>
                      <th>START DATE</th>
                      <th>END DATE</th>
                      <th>PLAN/Sub Category</th>
                      <th>AMOUNT</th>
                      <th>STATUS</th>
                  </tr>
              </thead>
              <tbody id="client_history">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript">
  function showClientHistory(ele){
    var clientId = parseInt($(ele).val());
    document.getElementById('client_history').innerHTML = '';
    $.ajax({
      method: "POST",
      url: "{{url('admin/getClientHistory')}}",
      data:{client_id:clientId}
    })
    .done(function( msg ) {
      body = document.getElementById('client_history');
      body.innerHTML = '';
      var index = 1;
      if( 0 < msg['plans'].length){
        $.each(msg['plans'], function(idx, obj) {
            var eleTr = document.createElement('tr');
            var eleIndex = document.createElement('td');
            eleIndex.innerHTML = index++;
            eleTr.appendChild(eleIndex);

            var eleClient = document.createElement('td');
            eleClient.innerHTML = obj.client;
            eleTr.appendChild(eleClient);

            var eleStartDate = document.createElement('td');
            eleStartDate.innerHTML = obj.start_date;
            eleTr.appendChild(eleStartDate);

            var eleEndDate = document.createElement('td');
            eleEndDate.innerHTML = obj.end_date;
            eleTr.appendChild(eleEndDate);

            var elePlan = document.createElement('td');
            elePlan.innerHTML = obj.plan;
            eleTr.appendChild(elePlan);

            var eleAmount = document.createElement('td');
            eleAmount.innerHTML = 'Rs. '+ obj.final_amount;
            eleTr.appendChild(eleAmount);

            var eleStatus = document.createElement('td');
            if(1 == obj.plan_id){
              eleStatus.innerHTML = '<button class="btn btn-success btn-sm">Paid</button>';
            } else {
              if('Credit' == obj.payment_status){
                eleStatus.innerHTML = '<button class="btn btn-success btn-sm">Paid</button>';
              } else {
                eleStatus.innerHTML = '<button class="btn btn-warning btn-sm">Pay</button>';
              }
            }
            eleTr.appendChild(eleStatus);
            body.appendChild(eleTr);
        });
      }
      if( 0 < msg['purchasedSubCategories'].length){
        $.each(msg['purchasedSubCategories'], function(idx, obj) {
            var eleTr = document.createElement('tr');
            var eleIndex = document.createElement('td');
            eleIndex.innerHTML = index++;
            eleTr.appendChild(eleIndex);

            var eleClient = document.createElement('td');
            eleClient.innerHTML = obj.client;
            eleTr.appendChild(eleClient);

            var eleStartDate = document.createElement('td');
            eleStartDate.innerHTML = obj.start_date;
            eleTr.appendChild(eleStartDate);

            var eleEndDate = document.createElement('td');
            eleEndDate.innerHTML = obj.end_date;
            eleTr.appendChild(eleEndDate);

            var elePlan = document.createElement('td');
            elePlan.innerHTML = obj.sub_category;
            eleTr.appendChild(elePlan);

            var eleAmount = document.createElement('td');
            eleAmount.innerHTML = 'Rs. '+ obj.price;
            eleTr.appendChild(eleAmount);

            var eleStatus = document.createElement('td');
            eleStatus.innerHTML = '<button class="btn btn-success btn-sm">Paid</button>';

            eleTr.appendChild(eleStatus);
            body.appendChild(eleTr);
        });
      }
      if( 0 < msg['plans'].length || 0 < msg['purchasedSubCategories'].length){
        var eleTr = document.createElement('tr');
        var eleIndex = document.createElement('td');
        eleIndex.innerHTML = '';
        eleIndex.setAttribute('colspan', '4');
        eleTr.appendChild(eleIndex);

        var elePlan = document.createElement('td');
        elePlan.innerHTML = '<b>Total:</b>';
        eleTr.appendChild(elePlan);

        var eleAmount = document.createElement('td');
        eleAmount.innerHTML = 'Rs. '+ msg['total'];
        eleTr.appendChild(eleAmount);

        var eleStatus = document.createElement('td');
        eleStatus.innerHTML = '';
        eleTr.appendChild(eleStatus);

        body.appendChild(eleTr);
      }
      if( 0 > msg['plans'].length && 0 > msg['purchasedSubCategories'].length){
        var eleTr = document.createElement('tr');
        var eleIndex = document.createElement('td');
        eleIndex.innerHTML = 'No result!';
        eleIndex.setAttribute('colspan', '7');
        eleTr.appendChild(eleIndex);
        body.appendChild(eleTr);
      }
    });
  }
</script>
@stop