@extends('admin.master')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Admin Payments </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-inr"></i> Payments </li>
      <li class="active"> Admin Payments </li>
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
  #admin_payment td:nth-of-type(1):before { content: "#" ; font-weight: bolder; }
  #admin_payment td:nth-of-type(2):before { content: "Type" ; font-weight: bolder; }
  #admin_payment td:nth-of-type(3):before { content: "Name" ; font-weight: bolder; }
  #admin_payment td:nth-of-type(4):before { content: "Category"; font-weight: bolder;}
  #admin_payment td:nth-of-type(5):before { content: "Sub Category";  font-weight: bolder;}
  #admin_payment td:nth-of-type(6):before { content: "Subject"; font-weight: bolder;}
  #admin_payment td:nth-of-type(7):before { content: "User"; font-weight: bolder;}
  #admin_payment td:nth-of-type(8):before { content: "Admin";  font-weight: bolder;}
  #admin_payment td:nth-of-type(9):before { content: "Date"; font-weight: bolder;}
  #admin_payment td:nth-of-type(10):before { content: "Price"; font-weight: bolder;}
  #admin_payment td:nth-of-type(11):before { content: "Receipt"; font-weight: bolder;}

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
      <div class="container">
        @if(Auth::guard('admin')->user()->hasRole('admin'))
        <div class="row">
          <div class="col-md-3">
            <select class="form-control" id="admin" name="admin" onChange="showAdminPayments(this);">
              <option value="0"> Select Admin </option>
              <option value="All" selected> All </option>
              @if(count($adminNames) > 0)
                @foreach($adminNames as $adminId => $adminName)
                  <option value="{{$adminId}}">{{$adminName}}</option>
                @endforeach
              @endif
            </select>
          </div>
        </div>
        @endif
        <div class="row">
          <div class="col-lg-12" id="">
            <div class="text-center heading" ><h2>Admin Payments</h2></div>
            <table id="admin_payment" class="kullaniciTablosu" >
              <thead>
                  <tr>
                      <th>#</th>
                      <th>Type</th>
                      <th>Name</th>
                      <th>Category</th>
                      <th>Sub Category</th>
                      <th>Subject</th>
                      <th>User</th>
                      <th>Admin</th>
                      <th>Date</th>
                      <th>Price</th>
                      <th>Receipt</th>
                  </tr>
              </thead>
              <tbody id="admin_payments">
                @if(count($results) > 0)
                  @foreach($results as $index =>  $result)
                    <tr style="overflow: auto;">
                      <td>{{$index + 1}}</td>
                      <td>{{ $result['type'] }}</td>
                      <td>{{ $result['name'] }}</td>
                      <td>{{ $result['category'] }}</td>
                      <td>{{ $result['subcategory'] }}</td>
                      <td>{{ $result['subject'] }}</td>
                      <td>{{ $userNames[$result['user_id']] }}</td>
                      <td>{{ $adminNames[$result['admin_id']] }}</td>
                      <td>{{ $result['updated_at'] }}</td>
                      <td>Rs. {{ $result['price'] }}</td>
                      <td>
                        @if('Paper' == $result['type'])
                          <a href="{{url('admin/receipt/paper')}}/{{$result['id']}}" target="_blank">Receipt</a>
                        @elseif('Course' == $result['type'])
                          <a href="{{url('admin/receipt/course')}}/{{$result['id']}}" target="_blank">Receipt</a>
                        @else
                          <a href="{{url('admin/receipt/vkit')}}/{{$result['id']}}" target="_blank">Receipt</a>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                  <tr style="overflow: auto;"><td colspan="8"></td><td>Total</td><td colspan="2">Rs. {{$total}}</td></tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript">
  function showAdminPayments(ele){
    var adminId = parseInt($(ele).val());
    document.getElementById('admin_payments').innerHTML = '';
    $.ajax({
      method: "POST",
      url: "{{url('admin/getAdminPaymentsById')}}",
      data:{admin_id:adminId}
    })
    .done(function( msg ) {
      console.log(msg);
      body = document.getElementById('admin_payments');
      body.innerHTML = '';
      var index = 1;
      if( 0 < msg['payments'].length){
        $.each(msg['payments'], function(idx, obj) {
            var eleTr = document.createElement('tr');
            eleTr.setAttribute('overflow', 'auto');
            var eleIndex = document.createElement('td');
            eleIndex.innerHTML = index++;
            eleTr.appendChild(eleIndex);

            var eleType = document.createElement('td');
            eleType.innerHTML = obj.type;
            eleTr.appendChild(eleType);

            var eleName = document.createElement('td');
            eleName.innerHTML = obj.name;
            eleTr.appendChild(eleName);

            var eleCategory = document.createElement('td');
            eleCategory.innerHTML = obj.category;
            eleTr.appendChild(eleCategory);

            var eleSubCategory = document.createElement('td');
            eleSubCategory.innerHTML = obj.subcategory;
            eleTr.appendChild(eleSubCategory);

            var eleSubject = document.createElement('td');
            eleSubject.innerHTML = obj.subject;
            eleTr.appendChild(eleSubject);

            var eleUser = document.createElement('td');
            eleUser.innerHTML = msg['users'][obj.user_id];
            eleTr.appendChild(eleUser);

            var eleAdmin = document.createElement('td');
            eleAdmin.innerHTML = msg['admins'][obj.admin_id];
            eleTr.appendChild(eleAdmin);

            var eleDate = document.createElement('td');
            eleDate.innerHTML = obj.updated_at;
            eleTr.appendChild(eleDate);

            var eleAmount = document.createElement('td');
            eleAmount.innerHTML = 'Rs. '+ obj.price;
            eleTr.appendChild(eleAmount);

            var eleReceipt = document.createElement('td');
            var urlStr = "{{url('admin/receipt')}}";
            if('Paper' == obj.type){
              eleReceipt.innerHTML = '<a href="'+urlStr+'/paper/'+obj.id+'" target="_blank">Receipt</a>';
            } else if('Course' == obj.type){
              eleReceipt.innerHTML = '<a href="'+urlStr+'/course/'+obj.id+'" target="_blank">Receipt</a>';
            } else {
              eleReceipt.innerHTML = '<a href="'+urlStr+'/vkit/'+obj.id+'" target="_blank">Receipt</a>';
            }
            eleTr.appendChild(eleReceipt);

            body.appendChild(eleTr);
        });
        var eleTr = document.createElement('tr');
        eleTr.setAttribute('overflow', 'auto');
        var eleIndex = document.createElement('td');
        eleIndex.setAttribute('colspan', '8');
        eleTr.appendChild(eleIndex);

        var eleTotal = document.createElement('td');
        eleTotal.innerHTML = 'Toal';
        eleTr.appendChild(eleTotal);

        var eleTotalAmount = document.createElement('td');
        eleTotalAmount.innerHTML = msg['total'];
        eleTotalAmount.setAttribute('colspan', '2');
        eleTr.appendChild(eleTotalAmount);
        body.appendChild(eleTr);
      }
      // if( 0 < msg['purchasedSubCategories'].length){
      //   $.each(msg['purchasedSubCategories'], function(idx, obj) {
      //       var eleTr = document.createElement('tr');
      //       var eleIndex = document.createElement('td');
      //       eleIndex.innerHTML = index++;
      //       eleTr.appendChild(eleIndex);

      //       var eleClient = document.createElement('td');
      //       eleClient.innerHTML = obj.client;
      //       eleTr.appendChild(eleClient);

      //       var eleStartDate = document.createElement('td');
      //       eleStartDate.innerHTML = obj.start_date;
      //       eleTr.appendChild(eleStartDate);

      //       var eleEndDate = document.createElement('td');
      //       eleEndDate.innerHTML = obj.end_date;
      //       eleTr.appendChild(eleEndDate);

      //       var elePlan = document.createElement('td');
      //       elePlan.innerHTML = obj.sub_category;
      //       eleTr.appendChild(elePlan);

      //       var eleAmount = document.createElement('td');
      //       eleAmount.innerHTML = 'Rs. '+ obj.price;
      //       eleTr.appendChild(eleAmount);

      //       var eleStatus = document.createElement('td');
      //       eleStatus.innerHTML = '<button class="btn btn-success btn-sm">Paid</button>';

      //       eleTr.appendChild(eleStatus);
      //       body.appendChild(eleTr);
      //   });
      // }
      // if( 0 < msg['plans'].length || 0 < msg['purchasedSubCategories'].length){
      //   var eleTr = document.createElement('tr');
      //   var eleIndex = document.createElement('td');
      //   eleIndex.innerHTML = '';
      //   eleIndex.setAttribute('colspan', '4');
      //   eleTr.appendChild(eleIndex);

      //   var elePlan = document.createElement('td');
      //   elePlan.innerHTML = '<b>Total:</b>';
      //   eleTr.appendChild(elePlan);

      //   var eleAmount = document.createElement('td');
      //   eleAmount.innerHTML = 'Rs. '+ msg['total'];
      //   eleTr.appendChild(eleAmount);

      //   var eleStatus = document.createElement('td');
      //   eleStatus.innerHTML = '';
      //   eleTr.appendChild(eleStatus);

      //   body.appendChild(eleTr);
      // }
      // if( 0 == msg['plans'].length && 0 == msg['purchasedSubCategories'].length){
      //   var eleTr = document.createElement('tr');
      //   var eleIndex = document.createElement('td');
      //   eleIndex.innerHTML = 'No result!';
      //   eleIndex.setAttribute('colspan', '7');
      //   eleTr.appendChild(eleIndex);
      //   body.appendChild(eleTr);
      // }
    });
  }
</script>
@stop