@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Offline Payment  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-address-book"></i> Offline Payment </li>
      <li class="active"> Manage  Offline Payment </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container">
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
    <div class="form-group row">
      <div>
        <a href="{{url('createOfflinePayment')}}" type="button" class="btn btn-primary" style="float: right; width: 150px !important;" title="Add New Payment">Add New Payment</a>&nbsp;&nbsp;
      </div>
    </div>
  <div>
    <table class="" id="">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Batch</th>
          <th>User</th>
          <th>Amount</th>
          <th>Date</th>
          <th>Recepit</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody id="">
        @if(count($payments) > 0)
          @foreach($payments as $index => $payment)
          <tr style="overflow: auto;">
            <td>{{$index + $payments->firstItem()}}</td>
            <td>{{$payment->batch->name}}</td>
            <td>{{$payment->user->name}}</td>
            <td>{{$payment->amount}}</td>
            <td>{{$payment->updated_at}}</td>
            <td>
              <a href="{{url('offlineReceipt')}}/{{$payment->id}}" target="_blank">Receipt
                </a>
            </td>
            <td>
              <a href="{{url('offlinePayment')}}/{{$payment->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit" />
                </a>
            </td>
            <td>
            <a id="{{$payment->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete" />
                </a>
                <form id="deletePayment_{{$payment->id}}" action="{{url('deleteOfflinePayment')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="payment_id" value="{{$payment->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="7">No Offline Payments.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $payments->links() }}
    </div>
  </div>
  </div>
<script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure, you want to delete this payment?.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    var id = $(ele).attr('id');
                    formId = 'deletePayment_'+id;
                    document.getElementById(formId).submit();
                  }
              },
              Cancel: function () {
              }
          }
        });
    }

</script>
@stop