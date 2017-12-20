@extends('admin.master')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Web Development </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-sitemap"></i> Web Development </li>
      <li class="active"> Web Development </li>
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
  #client_history td:nth-of-type(2):before { content: "Name" ; font-weight: bolder; }
  #client_history td:nth-of-type(3):before { content: "Email"; font-weight: bolder;}
  #client_history td:nth-of-type(4):before { content: "Domain";  font-weight: bolder;}
  #client_history td:nth-of-type(5):before { content: "Amount"; font-weight: bolder;}
  #client_history td:nth-of-type(6):before { content: "Details"; font-weight: bolder;}

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
        <div class="row">
          <div class="col-lg-12" id="">
            <div class="text-center heading" ><h2>Web Development</h2></div>
            <table  class="kullaniciTablosu" >
              <thead>
                  <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Domain</th>
                      <th>Amount</th>
                      <th>Details</th>
                  </tr>
              </thead>
              <tbody id="client_history">
                @if(count($webDevelopments) > 0)
                  @foreach($webDevelopments as $index => $webDevelopment)
                    <tr>
                      <td>{{ $index + 1}}</td>
                      <td>{{ $webDevelopment->name }}</td>
                      <td>{{ $webDevelopment->email }}</td>
                      <td>{{ $webDevelopment->domains }}</td>
                      <td>Rs. {{ $webDevelopment->price }}</td>
                      <td> <a href="#webModal_{{$webDevelopment->id}}" data-toggle="modal">Details</a> </td>
                      <div class="modal" id="webModal_{{ $webDevelopment->id }}" role="dialog" style="display: none;">
                        <div class="modal-dialog modal-sm">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal">×</button>
                              <h4 class="modal-title">Web Development Details</h4>
                              <div class="form-group">
                                <div class="form-group"><label>Name:</label> {{ $webDevelopment->name }}</div>
                                <div class="form-group"><label>Email:</label> {{ $webDevelopment->email }}</div>
                                <div class="form-group"><label>Domains:</label> {{ $webDevelopment->domains }}</div>
                                <div class="form-group"><label>Phone:</label> {{ $webDevelopment->phone }}</div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </tr>
                  @endforeach
                    <tr>
                      <td colspan="3"></td>
                      <td ><b>Total</b></td>
                      <td>Rs. <b> {{ $totalSum }} </b></td>
                      <td></td>
                    </tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop