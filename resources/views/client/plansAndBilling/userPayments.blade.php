@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> User Payments  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-inr"></i> Plans & Billing </li>
      <li class="active"> User Payments </li>
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
  #client_history td:nth-of-type(2):before { content: "User" ; font-weight: bolder; }
  #client_history td:nth-of-type(3):before { content: "Type" ; font-weight: bolder; }
  #client_history td:nth-of-type(4):before { content: "Name"; font-weight: bolder;}
  #client_history td:nth-of-type(5):before { content: "Amount";  font-weight: bolder;}
  #client_history td:nth-of-type(6):before { content: "Date"; font-weight: bolder;}
}

/**/
.heading h2
{font-weight: bolder;color: #31708f; text-transform: uppercase;
margin-bottom: 20px;
text-shadow: 0px 3px 0px rgba(50,50,50, .3);}
</style>
@stop
@section('dashboard_content')
  <div class="top mrgn_40_btm">
    <div class="container">
      <div class="row">
        <div class="col-md-3 mrgn_10_btm">
          <select class="form-control" id="client" name="client" onChange="showClientUserPayments(this);">
            <option value="0"> Select User </option>
            <option value="All"> All </option>
            @if(count($clientUsers) > 0)
              @foreach($clientUsers as $clientUser)
                <option value="{{$clientUser->id}}">{{$clientUser->name}}</option>
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
            <div class="text-center heading" ><h2>User Payments</h2></div>
            <table  class="kullaniciTablosu" id="dataTables-example">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody id="client_history">
                <tr><td colspan="6">please select user</td></tr>
                </tbody>
            </table>
          </div>
       </div>
    </div>
<script type="text/javascript">
  function showClientUserPayments(ele){
    var userId = $(ele).val();
    document.getElementById('client_history').innerHTML = '';
    $.ajax({
      method: "POST",
      url: "{{url('getClientUserPayments')}}",
      data:{client_user_id:userId}
    })
    .done(function( msg ) {
      body = document.getElementById('client_history');
      body.innerHTML = '';
      if( msg['purchased'] && 0 < msg['purchased'].length){
        $.each(msg['purchased'], function(idx, obj) {
            var eleTr = document.createElement('tr');
            var eleIndex = document.createElement('td');
            eleIndex.innerHTML = idx + 1;
            eleTr.appendChild(eleIndex);

            var eleUser = document.createElement('td');
            eleUser.innerHTML = obj['user'];
            eleTr.appendChild(eleUser);

            var eleType = document.createElement('td');
            eleType.innerHTML = obj['type'];
            eleTr.appendChild(eleType);

            var eleName = document.createElement('td');
            eleName.innerHTML = obj['name'];
            eleTr.appendChild(eleName);

            var eleAmount = document.createElement('td');
            eleAmount.innerHTML = 'Rs. '+ obj['amount'];
            eleTr.appendChild(eleAmount);

            var eleDate = document.createElement('td');
            eleDate.innerHTML = format_time(new Date(obj['date']['date']));
            eleTr.appendChild(eleDate);

            body.appendChild(eleTr);
        });

        var eleTr = document.createElement('tr');
        var eleIndex = document.createElement('td');
        eleIndex.innerHTML = '';
        eleIndex.setAttribute('colspan', '3');
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

  function format_time(date_obj) {
    // formats a javascript Date object into a 12h AM/PM time string
    var day = date_obj.getDate();
    var month = date_obj.getMonth()+1;
    var year = date_obj.getFullYear();
    var hour = date_obj.getHours();
    var minute = date_obj.getMinutes();
    var amPM = (hour > 11) ? " PM" : " AM";
    if(hour > 12) {
      hour -= 12;
    } else if(hour == 0) {
      hour = "12";
    }
    if(minute < 10) {
      minute = "0" + minute;
    }
    return year+"-"+month+"-"+day+" "+hour+":"+minute+":"+amPM;
  }
</script>
@stop