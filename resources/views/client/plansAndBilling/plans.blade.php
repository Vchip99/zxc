@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Plan  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-inr"></i> Plans & Billing </li>
      <li class="active"> Manage Plan </li>
    </ol>
  </section>
  <style type="text/css">
 .panel.price>.panel-heading{
      border-radius:0px;
       -moz-transition: all .3s ease;
      -o-transition:  all .3s ease;
      -webkit-transition:  all .3s ease;
      height: 100px;

    }
    .panel.price:hover{
      box-shadow: 0px 0px 30px rgba(0,0,0, .2);
    }
    .panel.price:hover>.panel-heading{
      box-shadow: 0px 0px 30px rgba(0,0,0, .2) inset;

    }
    .panel.price>.panel-heading{
      box-shadow: 0px 5px 0px rgba(50,50,50, .2) inset;
      text-shadow:0px 3px 0px rgba(50,50,50, .6);
    }
    .panel.price>.panel-heading h2{
      font-weight: bolder;
    }
    .price .list-group-item{
      border-bottom-:1px solid rgba(250,250,250, .5);
    }
    .panel.price .list-group-item:last-child {
      border-bottom-right-radius: 0px;
      border-bottom-left-radius: 0px;
    }
    .panel.price .list-group-item:first-child {
      border-top-right-radius: 0px;
      border-top-left-radius: 0px;
    }

    .price .panel-footer {
      color: #fff;
      border-bottom:0px;
      background-color:  rgba(0,0,0, .1);
      box-shadow: 0px 3px 0px rgba(0,0,0, .3);
    }


    .panel.price .btn{
      box-shadow: 0 -1px 0px rgba(50,50,50, .2) inset;
      border:0px;
    }
    /* blue panel */


    .price.panel-blue>.panel-heading {
      color: #fff;
      background-color: #608BB4;
      border-color: #78AEE1;
      border-bottom: 1px solid #78AEE1;
    }


    .price.panel-blue>.panel-body {
      color: #fff;
      background-color: #73A3D4;
      height: 50px;
      padding-bottom: 0px;
    }


    .price.panel-blue>.panel-body .lead{
        text-shadow: 0px 3px 0px rgba(50,50,50, .3);
    }

    .price.panel-blue .list-group-item {
      color: #333;
      background-color: rgba(50,50,50, .01);
      font-weight:600;
      text-shadow: 0px 1px 0px rgba(250,250,250, .75);
    }
    .read, .read-1, .read-2{
    overflow: hidden;
    /*font-size: 16px !important;*/
    transition: all 0.3s;
     height: 350px;
     padding-top: 10px;


}
.read-more{
    height: auto;
}
#readMore, #readMore-1, #readMore-2{
  font-weight: bolder;
  cursor: pointer;
  color: #01bacd;
  font-size: 15px;
  text-align: center !important;
  /*margin-bottom: 5px;*/
}
</style>
@stop
@section('dashboard_content')
  &nbsp;
   <div class="container">
        <div class="row ">
          @if(Session::has('message'))
            <div class="alert alert-success" id="message">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ Session::get('message') }}
            </div>
          @endif
          @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
          @endif
            <div class="col-md-3 col-sm-6">
                  <div class="panel price panel-blue">
                    <div class="panel-heading arrow_box text-center">
                     <h2>Free</h2>
                    </div>
                    <div class="panel-body text-center">
                      <p class="lead" style="font-size:20px"><strong>Rs. 0000 / year</strong></p>
                    </div>
                    <ul class="list-group list-group-flush text-center">
                      <li class="list-group-item" style="margin-top: 13px;">Online test series</li>
                      <li class="list-group-item">Online Courses</li>
                      <li class="list-group-item">Notification</li>
                      <li class="list-group-item">Admin massage</li>
                      <li class="list-group-item">Assignment</li>
                      <li class="list-group-item">Payment Gateway</li>
                      <li class="list-group-item">Dashboard for students</li>
                      <li class="list-group-item">Admin panel (ERP)</li>
                      <li class="list-group-item">Up-to 20 login</li>
                    </ul>
                    <div class="panel-footer">
                      @if(1 == Auth::guard('client')->user()->plan_id)
                        <button class="btn btn-lg btn-block btn-info" disabled="true"><span style="color: red;">Current Plan!</span></button>
                      @elseif(2 == Auth::guard('client')->user()->plan_id || 3 == Auth::guard('client')->user()->plan_id || 4 == Auth::guard('client')->user()->plan_id)
                        <button class="btn btn-lg btn-block btn-info" onClick="submitForm(this);" id="{{Auth::guard('client')->user()->id}}_1">Buy Now!</button>
                        <form method="POST" action="{{ url('degradePayment') }}" id="form_{{Auth::guard('client')->user()->id}}_1">
                          {{ csrf_field() }}
                          <input type="hidden" name="plan_id" value="1" />
                        </form>
                      @endif
                    </div>
                  </div>
            </div>
            <div class="col-md-3 col-sm-6">
                  <div class="panel price panel-blue">
                    <div class="panel-heading arrow_box text-center">
                    <h2>Gold</h2>
                    </div>
                    <div class="panel-body text-center">
                      <p class="lead" style="font-size:20px"><strong> Rs. 2999 / year</strong></p>
                    </div>
                    <ul class="list-group list-group-flush text-center read">
                      <li class="list-group-item">Online test series</li>
                      <li class="list-group-item">Online Courses</li>
                      <li class="list-group-item">Notification</li>
                      <li class="list-group-item">Admin massage</li>
                      <li class="list-group-item">Assignment</li>
                      <li class="list-group-item">Payment Gateway</li>
                      <li class="list-group-item">Dashboard for students</li>
                      <li class="list-group-item">Admin panel (ERP)</li>
                      <li class="list-group-item">Unlimited users</li>
                      <li class="list-group-item">Digital Advertisement (1 month)  </li>
                    </ul>
                   <p id="readMore">Show More</p>
                    <div class="panel-footer">
                      @if(1 == Auth::guard('client')->user()->plan_id)
                        <button class="btn btn-lg btn-block btn-info" onClick="submitForm(this);" id="{{Auth::guard('client')->user()->id}}_2">Upgrade Now!</button>
                        <form method="POST" action="{{ url('upgradePayment') }}" id="form_{{Auth::guard('client')->user()->id}}_2">
                          {{ csrf_field() }}
                          <input type="hidden" name="plan_id" value="2" />
                        </form>
                      @elseif(2 == Auth::guard('client')->user()->plan_id)
                        <button class="btn btn-lg btn-block btn-info" disabled="true"><span style="color: red;">Current Plan!</span></button>
                      @elseif(3 == Auth::guard('client')->user()->plan_id || 4 == Auth::guard('client')->user()->plan_id)
                        <button class="btn btn-lg btn-block btn-info" onClick="submitForm(this);" id="{{Auth::guard('client')->user()->id}}_2">Buy Now!</button>
                        <form method="POST" action="{{ url('degradePayment') }}" id="form_{{Auth::guard('client')->user()->id}}_2">
                          {{ csrf_field() }}
                          <input type="hidden" name="plan_id" value="2" />
                        </form>
                      @endif
                    </div>
                  </div>
            </div>
            <div class="col-md-3 col-sm-6">
                  <div class="panel price panel-blue">
                    <div class="panel-heading arrow_box text-center">
                    <h2>Platinum</h2>
                    </div>
                    <div class="panel-body text-center">
                      <p class="lead" style="font-size:20px"><strong>Rs. 4999 / year</strong></p>
                    </div>
                    <ul class="list-group list-group-flush text-center read-1">
                      <li class="list-group-item">Online test series</li>
                      <li class="list-group-item">Online Courses</li>
                      <li class="list-group-item">Notification</li>
                      <li class="list-group-item">Admin massage</li>
                      <li class="list-group-item">Assignment</li>
                      <li class="list-group-item">Payment Gateway</li>
                      <li class="list-group-item">Dashboard for students</li>
                      <li class="list-group-item">Admin panel (ERP)</li>
                      <li class="list-group-item">Unlimited users</li>
                      <li class="list-group-item">Digital Advertisement (1 year)</li>
                      <li class="list-group-item">SEO</li>
                      <li class="list-group-item">Email marketing</li>
                    </ul>
                   <p id="readMore-1" class="text-center"> Show More</p>
                    <div class="panel-footer">
                      @if(1 == Auth::guard('client')->user()->plan_id || 2 == Auth::guard('client')->user()->plan_id)
                        <button class="btn btn-lg btn-block btn-info" onClick="submitForm(this);" id="{{Auth::guard('client')->user()->id}}_3">Upgrade Now!</button>
                        <form method="POST" action="{{ url('upgradePayment') }}" id="form_{{Auth::guard('client')->user()->id}}_3">
                          {{ csrf_field() }}
                          <input type="hidden" name="plan_id" value="3" />
                        </form>
                      @elseif(3 == Auth::guard('client')->user()->plan_id)
                        <button class="btn btn-lg btn-block btn-info" disabled="true"><span style="color: red;">Current Plan!</span></button>
                      @elseif(4 == Auth::guard('client')->user()->plan_id)
                        <button class="btn btn-lg btn-block btn-info" onClick="submitForm(this);" id="{{Auth::guard('client')->user()->id}}_3">Buy Now!</button>
                        <form method="POST" action="{{ url('degradePayment') }}" id="form_{{Auth::guard('client')->user()->id}}_3">
                          {{ csrf_field() }}
                          <input type="hidden" name="plan_id" value="3" />
                        </form>
                      @endif
                    </div>
                  </div>
            </div>
            <div class="col-md-3 col-sm-6">
                  <div class="panel price panel-blue">
                    <div class="panel-heading arrow_box text-center">
                    <h2>Diamond</h2>
                    </div>
                    <div class="panel-body text-center">
                      <p class="lead" style="font-size:20px"><strong> Rs. 9999 / year</strong></p>
                    </div>
                    <ul class="list-group list-group-flush text-center read-2">
                          <li class="list-group-item">Online test series</li>
                          <li class="list-group-item">Online Courses</li>
                          <li class="list-group-item">Notification</li>
                          <li class="list-group-item">Admin massage</li>
                          <li class="list-group-item">Assignment</li>
                          <li class="list-group-item">Payment Gateway</li>
                          <li class="list-group-item">Dashboard for students</li>
                          <li class="list-group-item">Admin panel (ERP)</li>
                          <li class="list-group-item">Unlimited users</li>
                          <li class="list-group-item">Hosting for one year</li>
                          <li class="list-group-item">Domain name</li>
                          <li class="list-group-item">Design websites</li>
                          <li class="list-group-item">Developed website</li>
                          <li class="list-group-item">Editing in any page</li>
                          <li class="list-group-item">Add/remove the page</li>
                          <li class="list-group-item">Solving any technical issues</li>
                          <li class="list-group-item">Digital Marketing (one Month)</li>
                          <li class="list-group-item">SEO</li>
                    </ul>
                   <p id="readMore-2">Show More</p>
                    <div class="panel-footer">
                      @if(4 == Auth::guard('client')->user()->plan_id)
                        <button class="btn btn-lg btn-block btn-info" disabled="true"><span style="color: red;">Current Plan!</span></button>
                      @elseif(1 == Auth::guard('client')->user()->plan_id || 2 == Auth::guard('client')->user()->plan_id || 3 == Auth::guard('client')->user()->plan_id )
                        <button class="btn btn-lg btn-block btn-info" onClick="submitForm(this);" id="{{Auth::guard('client')->user()->id}}_4">Upgrade Now!</button>
                        <form method="POST" action="{{ url('upgradePayment') }}" id="form_{{Auth::guard('client')->user()->id}}_4">
                          {{ csrf_field() }}
                          <input type="hidden" name="plan_id" value="4" />
                        </form>
                      @endif
                    </div>
                  </div>
            </div>
        </div>
        @if(1 != Auth::guard('client')->user()->plan_id)
          <p><b>Note: </b>For Deactivate Plans <a onclick="confirmSubmit();" style="cursor: pointer;">Click Here</a> </p>
          <form method="POST" action="{{ url('deactivatePlan') }}" id="deactivatePlan">
            {{ csrf_field() }}
          </form>
        @endif
     </div>
<script>
$('#readMore').click(function(){
    $('.read').toggleClass('read-more');
    if($(this).text()=='Show Less') $(this).text('Show More');
    else  $(this).text('Show Less');
});
$('#readMore-1').click(function(){
    $('.read-1').toggleClass('read-more');
    if($(this).text()=='Show Less') $(this).text('Show More');
    else  $(this).text('Show Less');
});
$('#readMore-2').click(function(){
    $('.read-2').toggleClass('read-more');
    if($(this).text()=='Show Less') $(this).text('Show More');
    else  $(this).text('Show Less');
});

function submitForm(ele){
  var id = $(ele).attr('id');
  document.getElementById('form_'+id).submit();
}

function confirmSubmit(ele){
  $.confirm({
    title: 'Confirmation',
    content: '<li>If you deactivate current plan, then you will automatically converted in to free plan.</li> <li> After deactivation of current plan, Only first <b>20</b> users can access/login this website.</li>',
    type: 'red',
    typeAnimated: true,
    buttons: {
        Ok: {
            text: 'Ok',
            btnClass: 'btn-red',
            action: function(){
              document.getElementById('deactivatePlan').submit();
            }
        },
        Cancle: function () {
        }
    }
  });
}
</script>
@stop