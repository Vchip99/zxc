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
      height: 80px;
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
    .free_first{
      height: 369px;
    }
    .paid_secind_third{
      height: 381px;
    }
    #readMore, #readMore-1, #readMore-2{
      font-weight: bolder;
      cursor: pointer;
      color: #01bacd;
      font-size: 15px;
      text-align: center !important;
      /*margin-bottom: 5px;*/
    }
    .price .modal-title,.price .row {
      color: black !important;
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
                      <p class="lead" style="font-size:20px"><strong>Rs. {{ $allPlan[1]->amount}} / year Or <br>Rs. {{ $allPlan[1]->monthly_amount}} / month </strong></p>
                    </div>
                    <ul class="list-group list-group-flush text-center free_first">
                      <li class="list-group-item" style="margin-top: 13px;">Online test series</li>
                      <li class="list-group-item">Online Courses</li>
                      <li class="list-group-item">Notification</li>
                      <li class="list-group-item">Admin message</li>
                      <li class="list-group-item">Assignment</li>
                      <li class="list-group-item">Payment Gateway</li>
                      <li class="list-group-item">Dashboard for students</li>
                      <li class="list-group-item">Admin panel (ERP)</li>
                      <li class="list-group-item">Up-to 10 login</li>
                    </ul>
                    <div class="panel-footer">
                      @if(1 == Auth::guard('client')->user()->plan_id)
                        <button class="btn btn-lg btn-block btn-info" disabled="true"><span style="color: red;">Current Plan!</span></button>
                      @elseif(2 == Auth::guard('client')->user()->plan_id || 3 == Auth::guard('client')->user()->plan_id || 4 == Auth::guard('client')->user()->plan_id)
                        <button class="btn btn-lg btn-block btn-info" disabled="true" id="{{Auth::guard('client')->user()->id}}_1" data-toggle="modal" data-target="#degradePlanModel_{{$allPlan[1]->id}}">Buy Now!</button>
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
                      <p class="lead" style="font-size:20px"><strong> Rs. {{ $allPlan[2]->amount}} / year Or <br>Rs. {{ $allPlan[2]->monthly_amount}} / month </strong></p>
                    </div>
                    <ul class="list-group list-group-flush text-center read">
                      <li class="list-group-item">Online test series</li>
                      <li class="list-group-item">Online Courses</li>
                      <li class="list-group-item">Notification</li>
                      <li class="list-group-item">Admin message</li>
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
                        <button class="btn btn-lg btn-block btn-info" id="{{Auth::guard('client')->user()->id}}_2" data-toggle="modal" data-target="#upgradePlanModel_{{$allPlan[2]->id}}">Upgrade Now!</button>
                        <div class="modal fade" id="upgradePlanModel_{{$allPlan[2]->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Upgrade Plan</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <form action="{{url('upgradePayment')}}" method="POST" enctype="multipart/form-data" id="{{Auth::guard('client')->user()->id}}_1">
                              {{ csrf_field() }}
                              <div class="modal-body">>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Select Plan</label>
                                    <div class="col-sm-8">
                                      <input type="radio" name="plan_upgrade_{{$allPlan[2]->id}}" value="1" checked="true"  data-id="{{$allPlan[2]->id}}" onclick="changeTotal(this,'upgrade')" required>
                                        Yearly:Rs <span id="yearly_upgrade_{{$allPlan[2]->id}}">{{ $allPlan[2]->amount}}</span>
                                      <input type="radio" name="plan_upgrade_{{$allPlan[2]->id}}" value="0" data-id="{{$allPlan[2]->id}}" onclick="changeTotal(this,'upgrade')" required>
                                        Monthly:Rs <span id="monthly_upgrade_{{$allPlan[2]->id}}">{{ $allPlan[2]->monthly_amount}}</span>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Select Month/Year</label>
                                    <div class="col-sm-8">
                                      <input type="number" id="duration_upgrade_{{$allPlan[2]->id}}" name="duration" value="1" min="1" data-id="{{$allPlan[2]->id}}" onchange="showTotal(this,'upgrade');" required>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Plan Total</label>
                                    <div class="col-sm-8">
                                      <input type="text" name="plan_total" id="total_upgrade_{{$allPlan[2]->id}}" value="{{$allPlan[2]->amount}}" readonly="true" required>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Existing Amount</label>
                                    <div class="col-sm-8">
                                      <input type="text" name="existing_plan_total" id="existing_upgrade_{{$allPlan[2]->id}}" value="{{ $existingAmount * -1}}" readonly="true" required>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Total Amount</label>
                                    <div class="col-sm-8">
                                      <input type="text" name="total" id="final_total_upgrade_{{$allPlan[2]->id}}" value="{{$allPlan[2]->amount  + $existingAmount }}" readonly="true" required>
                                    </div>
                                    <p id="final_condition_upgrade_{{$allPlan[2]->id}}" class="hide">* If fianl amount < 10 then you have to pay at least 10 Rs.</p>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                <input type="hidden" name="plan_id" value="{{$allPlan[2]->id}}">
                                <input type="hidden" name="plan_type" value="upgrade">
                                <button type="submit" class="btn btn-primary">Pay</button>
                              </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      @elseif(2 == Auth::guard('client')->user()->plan_id)
                        <button class="btn btn-lg btn-block btn-info" data-toggle="modal" data-target="#continuePlanModel_{{$allPlan[2]->id}}"><span style="color: red;">Current Plan!</span></button>
                        <div class="modal fade" id="continuePlanModel_{{$allPlan[2]->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Continue Plan</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <form action="{{url('continuePayment')}}" method="POST" enctype="multipart/form-data" id="{{Auth::guard('client')->user()->id}}_1">
                              {{ csrf_field() }}
                              <div class="modal-body">>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Select Plan</label>
                                    <div class="col-sm-8">
                                      <input type="radio" name="plan_continue_{{$allPlan[2]->id}}" value="1" checked="true"  data-id="{{$allPlan[2]->id}}" onclick="changeTotal(this,'continue')" required>
                                        Yearly:Rs <span id="yearly_continue_{{$allPlan[2]->id}}">{{ $allPlan[2]->amount}}</span>
                                      <input type="radio" name="plan_continue_{{$allPlan[2]->id}}" value="0" data-id="{{$allPlan[2]->id}}" onclick="changeTotal(this,'continue')" required>
                                        Monthly:Rs <span id="monthly_continue_{{$allPlan[2]->id}}">{{ $allPlan[2]->monthly_amount}}</span>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Select Month/Year</label>
                                    <div class="col-sm-8">
                                      <input type="number" id="duration_continue_{{$allPlan[2]->id}}" name="duration" value="1" min="1" data-id="{{$allPlan[2]->id}}" onchange="showTotal(this,'continue');" required>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Total</label>
                                    <div class="col-sm-8">
                                      <input type="text" name="total" id="total_continue_{{$allPlan[2]->id}}" value="{{$allPlan[2]->amount}}" readonly="true" required>
                                    </div>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                <input type="hidden" name="plan_id" value="{{$allPlan[2]->id}}">
                                <input type="hidden" name="plan_type" value="continue">
                                <button type="submit" class="btn btn-primary">Pay</button>
                              </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      @elseif(3 == Auth::guard('client')->user()->plan_id || 4 == Auth::guard('client')->user()->plan_id)
                        <button class="btn btn-lg btn-block btn-info" id="{{Auth::guard('client')->user()->id}}_2" data-toggle="modal" data-target="#degradePlanModel_{{$allPlan[2]->id}}">Buy Now!</button>
                        <div class="modal fade" id="degradePlanModel_{{$allPlan[2]->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Degrade Plan</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <form action="{{url('degradePayment')}}" method="POST" enctype="multipart/form-data" id="{{Auth::guard('client')->user()->id}}_1">
                              {{ csrf_field() }}
                              <div class="modal-body">>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Select Plan</label>
                                    <div class="col-sm-8">
                                      <input type="radio" name="plan_degrade_{{$allPlan[2]->id}}" value="1" checked="true"  data-id="{{$allPlan[2]->id}}" onclick="changeTotal(this,'degrade')" required>
                                        Yearly:Rs <span id="yearly_degrade_{{$allPlan[2]->id}}">{{ $allPlan[2]->amount}}</span>
                                      <input type="radio" name="plan_degrade_{{$allPlan[2]->id}}" value="0" data-id="{{$allPlan[2]->id}}" onclick="changeTotal(this,'degrade')" required>
                                        Monthly:Rs <span id="monthly_degrade_{{$allPlan[2]->id}}">{{ $allPlan[2]->monthly_amount}}</span>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Select Month/Year</label>
                                    <div class="col-sm-8">
                                      <input type="number" id="duration_degrade_{{$allPlan[2]->id}}" name="duration" value="1" min="1" data-id="{{$allPlan[2]->id}}" onchange="showTotal(this,'degrade');" required>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Total</label>
                                    <div class="col-sm-8">
                                      <input type="text" name="total" id="total_degrade_{{$allPlan[2]->id}}" value="{{$allPlan[2]->amount}}" readonly="true" required>
                                    </div>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                <input type="hidden" name="plan_id" value="{{$allPlan[2]->id}}">
                                <input type="hidden" name="plan_type" value="degrade">
                                <button type="submit" class="btn btn-primary">Pay</button>
                              </div>
                              </form>
                            </div>
                          </div>
                        </div>
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
                      <p class="lead" style="font-size:20px"><strong>Rs. {{ $allPlan[3]->amount}} / year Or <br>Rs. {{ $allPlan[3]->monthly_amount}} / month </strong></p>
                    </div>
                    <ul class="list-group list-group-flush text-center read-1 paid_secind_third">
                      <li class="list-group-item">All in Gold plan</li>
                      <li class="list-group-item">Custom web-site design</li>
                      <li class="list-group-item">Developed website</li>
                      <li class="list-group-item">Hosting for a year</li>
                      <li class="list-group-item">Maintenance for a year</li>
                      <li class="list-group-item">Solving any technical issues</li>
                      <li class="list-group-item">Add/Remove pages</li>
                      <li class="list-group-item">SEO (Basic)</li>
                    </ul>
                    <div class="panel-footer">
                      @if(1 == Auth::guard('client')->user()->plan_id || 2 == Auth::guard('client')->user()->plan_id)
                        <button class="btn btn-lg btn-block btn-info" id="{{Auth::guard('client')->user()->id}}_3" data-toggle="modal" data-target="#upgradePlanModel_{{$allPlan[3]->id}}">Upgrade Now!</button>
                        <div class="modal fade" id="upgradePlanModel_{{$allPlan[3]->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Upgrade Plan</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <form action="{{url('upgradePayment')}}" method="POST" enctype="multipart/form-data" id="{{Auth::guard('client')->user()->id}}_1">
                              {{ csrf_field() }}
                              <div class="modal-body">>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Select Plan</label>
                                    <div class="col-sm-8">
                                      <input type="radio" name="plan_upgrade_{{$allPlan[3]->id}}" value="1" checked="true"  data-id="{{$allPlan[3]->id}}" onclick="changeTotal(this,'upgrade')" required>
                                        Yearly:Rs <span id="yearly_upgrade_{{$allPlan[3]->id}}">{{ $allPlan[3]->amount}}</span>
                                      <input type="radio" name="plan_upgrade_{{$allPlan[3]->id}}" value="0" data-id="{{$allPlan[3]->id}}" onclick="changeTotal(this,'upgrade')" required>
                                        Monthly:Rs <span id="monthly_upgrade_{{$allPlan[3]->id}}">{{ $allPlan[3]->monthly_amount}}</span>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Select Month/Year</label>
                                    <div class="col-sm-8">
                                      <input type="number" id="duration_upgrade_{{$allPlan[3]->id}}" name="duration" value="1" min="1" data-id="{{$allPlan[3]->id}}" onchange="showTotal(this,'upgrade');" required>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Plan Total</label>
                                    <div class="col-sm-8">
                                      <input type="text" name="plan_total" id="total_upgrade_{{$allPlan[3]->id}}" value="{{$allPlan[3]->amount}}" readonly="true" required>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Existing Amount</label>
                                    <div class="col-sm-8">
                                      <input type="text" name="existing_plan_total" id="existing_upgrade_{{$allPlan[3]->id}}" value="{{ $existingAmount * -1}}" readonly="true" required>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Total Amount</label>
                                    <div class="col-sm-8">
                                      <input type="text" name="total" id="final_total_upgrade_{{$allPlan[3]->id}}" value="{{$allPlan[3]->amount  + $existingAmount }}" readonly="true" required>
                                    </div>
                                    <p id="final_condition_upgrade_{{$allPlan[3]->id}}" class="hide">* If fianl amount < 10 then you have to pay at least 10 Rs.</p>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                <input type="hidden" name="plan_id" value="{{$allPlan[3]->id}}">
                                <input type="hidden" name="plan_type" value="upgrade">
                                <button type="submit" class="btn btn-primary">Pay</button>
                              </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      @elseif(3 == Auth::guard('client')->user()->plan_id)
                        <button class="btn btn-lg btn-block btn-info" data-toggle="modal" data-target="#continuePlanModel_{{$allPlan[3]->id}}"><span style="color: red;">Current Plan!</span></button>
                        <div class="modal fade" id="continuePlanModel_{{$allPlan[3]->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Continue Plan</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <form action="{{url('continuePayment')}}" method="POST" enctype="multipart/form-data" id="{{Auth::guard('client')->user()->id}}_1">
                              {{ csrf_field() }}
                              <div class="modal-body">>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Select Plan</label>
                                    <div class="col-sm-8">
                                      <input type="radio" name="plan_continue_{{$allPlan[3]->id}}" value="1" checked="true"  data-id="{{$allPlan[3]->id}}" onclick="changeTotal(this,'continue')" required>
                                        Yearly:Rs <span id="yearly_continue_{{$allPlan[3]->id}}">{{ $allPlan[3]->amount}}</span>
                                      <input type="radio" name="plan_continue_{{$allPlan[3]->id}}" value="0" data-id="{{$allPlan[3]->id}}" onclick="changeTotal(this,'continue')" required>
                                        Monthly:Rs <span id="monthly_continue_{{$allPlan[3]->id}}">{{ $allPlan[3]->monthly_amount}}</span>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Select Month/Year</label>
                                    <div class="col-sm-8">
                                      <input type="number" id="duration_continue_{{$allPlan[3]->id}}" name="duration" value="1" min="1" data-id="{{$allPlan[3]->id}}" onchange="showTotal(this,'continue');" required>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Total</label>
                                    <div class="col-sm-8">
                                      <input type="text" name="total" id="total_continue_{{$allPlan[3]->id}}" value="{{$allPlan[3]->amount}}" readonly="true" required>
                                    </div>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                <input type="hidden" name="plan_id" value="{{$allPlan[3]->id}}">
                                <input type="hidden" name="plan_type" value="continue">
                                <button type="submit" class="btn btn-primary">Pay</button>
                              </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      @elseif(4 == Auth::guard('client')->user()->plan_id)
                        <button class="btn btn-lg btn-block btn-info" id="{{Auth::guard('client')->user()->id}}_3" data-toggle="modal" data-target="#degradePlanModel_{{$allPlan[3]->id}}">Buy Now!</button>
                        <div class="modal fade" id="degradePlanModel_{{$allPlan[3]->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Degrade Plan</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <form action="{{url('degradePayment')}}" method="POST" enctype="multipart/form-data" id="{{Auth::guard('client')->user()->id}}_1">
                              {{ csrf_field() }}
                              <div class="modal-body">>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Select Plan</label>
                                    <div class="col-sm-8">
                                      <input type="radio" name="plan_degrade_{{$allPlan[3]->id}}" value="1" checked="true"  data-id="{{$allPlan[3]->id}}" onclick="changeTotal(this,'degrade')" required>
                                        Yearly:Rs <span id="yearly_degrade_{{$allPlan[3]->id}}">{{ $allPlan[3]->amount}}</span>
                                      <input type="radio" name="plan_degrade_{{$allPlan[3]->id}}" value="0" data-id="{{$allPlan[3]->id}}" onclick="changeTotal(this,'degrade')" required>
                                        Monthly:Rs <span id="monthly_degrade_{{$allPlan[3]->id}}">{{ $allPlan[3]->monthly_amount}}</span>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Select Month/Year</label>
                                    <div class="col-sm-8">
                                      <input type="number" id="duration_degrade_{{$allPlan[3]->id}}" name="duration" value="1" min="1" data-id="{{$allPlan[3]->id}}" onchange="showTotal(this,'degrade');" required>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Total</label>
                                    <div class="col-sm-8">
                                      <input type="text" name="total" id="total_degrade_{{$allPlan[3]->id}}" value="{{$allPlan[3]->amount}}" readonly="true" required>
                                    </div>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                <input type="hidden" name="plan_id" value="{{$allPlan[3]->id}}">
                                <input type="hidden" name="plan_type" value="degrade">
                                <button type="submit" class="btn btn-primary">Pay</button>
                              </div>
                              </form>
                            </div>
                          </div>
                        </div>
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
                      <p class="lead" style="font-size:20px"><strong> Rs. {{ $allPlan[4]->amount}} / year Or <br>Rs. {{ $allPlan[4]->monthly_amount}} / month </strong></p>
                    </div>
                    <ul class="list-group list-group-flush text-center read-2 paid_secind_third">
                      <li class="list-group-item">All in Platinum plan</li>
                      <li class="list-group-item">SEO (Advance)</li>
                    </ul>
                    <div class="panel-footer">
                      @if(4 == Auth::guard('client')->user()->plan_id)
                        <button class="btn btn-lg btn-block btn-info" data-toggle="modal" data-target="#continuePlanModel_{{$allPlan[4]->id}}"><span style="color: red;">Current Plan!</span></button>
                        <div class="modal fade" id="continuePlanModel_{{$allPlan[4]->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Continue Plan</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <form action="{{url('continuePayment')}}" method="POST" enctype="multipart/form-data" id="{{Auth::guard('client')->user()->id}}_1">
                              {{ csrf_field() }}
                              <div class="modal-body">>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Select Plan</label>
                                    <div class="col-sm-8">
                                      <input type="radio" name="plan_continue_{{$allPlan[4]->id}}" value="1" checked="true"  data-id="{{$allPlan[4]->id}}" onclick="changeTotal(this,'continue')" required>
                                        Yearly:Rs <span id="yearly_continue_{{$allPlan[4]->id}}">{{ $allPlan[4]->amount}}</span>
                                      <input type="radio" name="plan_continue_{{$allPlan[4]->id}}" value="0" data-id="{{$allPlan[4]->id}}" onclick="changeTotal(this,'continue')" required>
                                        Monthly:Rs <span id="monthly_continue_{{$allPlan[4]->id}}">{{ $allPlan[4]->monthly_amount}}</span>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Select Month/Year</label>
                                    <div class="col-sm-8">
                                      <input type="number" id="duration_continue_{{$allPlan[4]->id}}" name="duration" value="1" min="1" data-id="{{$allPlan[4]->id}}" onchange="showTotal(this,'continue');" required>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Total</label>
                                    <div class="col-sm-8">
                                      <input type="text" name="total" id="total_continue_{{$allPlan[4]->id}}" value="{{$allPlan[4]->amount}}" readonly="true" required>
                                    </div>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                <input type="hidden" name="plan_id" value="{{$allPlan[4]->id}}">
                                <input type="hidden" name="plan_type" value="continue">
                                <button type="submit" class="btn btn-primary">Pay</button>
                              </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      @elseif(1 == Auth::guard('client')->user()->plan_id || 2 == Auth::guard('client')->user()->plan_id || 3 == Auth::guard('client')->user()->plan_id )
                        <button class="btn btn-lg btn-block btn-info" id="{{Auth::guard('client')->user()->id}}_4" data-toggle="modal" data-target="#upgradePlanModel_{{$allPlan[4]->id}}">Upgrade Now!</button>
                        <div class="modal fade" id="upgradePlanModel_{{$allPlan[4]->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Upgrade Plan</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <form action="{{url('upgradePayment')}}" method="POST" enctype="multipart/form-data" id="{{Auth::guard('client')->user()->id}}_1">
                              {{ csrf_field() }}
                              <div class="modal-body">>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Select Plan</label>
                                    <div class="col-sm-8">
                                      <input type="radio" name="plan_upgrade_{{$allPlan[4]->id}}" value="1" data-id="{{$allPlan[4]->id}}" checked="true" onclick="changeTotal(this,'upgrade')" required>
                                        Yearly:Rs <span id="yearly_upgrade_{{$allPlan[4]->id}}">{{ $allPlan[4]->amount}}</span>
                                      <input type="radio" name="plan_upgrade_{{$allPlan[4]->id}}" value="0" data-id="{{$allPlan[4]->id}}" onclick="changeTotal(this,'upgrade')" required>
                                        Monthly:Rs <span id="monthly_upgrade_{{$allPlan[4]->id}}">{{ $allPlan[4]->monthly_amount}}</span>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Select Month/Year</label>
                                    <div class="col-sm-8">
                                      <input type="number" id="duration_upgrade_{{$allPlan[4]->id}}" name="duration" value="1" min="1" data-id="{{$allPlan[4]->id}}" onchange="showTotal(this,'upgrade');" required>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Plan Total</label>
                                    <div class="col-sm-8">
                                      <input type="text" name="plan_total" id="total_upgrade_{{$allPlan[4]->id}}" value="{{$allPlan[4]->amount}}" readonly="true" required>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Existing Amount</label>
                                    <div class="col-sm-8">
                                      <input type="text" name="existing_plan_total" id="existing_upgrade_{{$allPlan[4]->id}}" value="{{ $existingAmount * -1}}" readonly="true" required>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Total Amount</label>
                                    <div class="col-sm-8">
                                      <input type="text" name="total" id="final_total_upgrade_{{$allPlan[4]->id}}" value="{{$allPlan[4]->amount  + $existingAmount }}" readonly="true" required>
                                    </div>
                                    <p id="final_condition_upgrade_{{$allPlan[4]->id}}" class="hide">* If fianl amount < 10 then you have to pay at least 10 Rs.</p>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                <input type="hidden" name="plan_id" value="{{$allPlan[4]->id}}">
                                <input type="hidden" name="plan_type" value="upgrade">
                                <button type="submit" class="btn btn-primary">Pay</button>
                              </div>
                              </form>
                            </div>
                          </div>
                        </div>
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
    content: '<li>If you deactivate current plan, then you will automatically converted in to free plan.</li> <li> After deactivation of current plan, Only first <b>10</b> users can access/login this website.</li>',
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

function showTotal(ele,changeType){
  var duration = $(ele).val();
  var plan = $(ele).data('id');
  var planType = $('input[name="plan_'+changeType+'_'+plan+'"]:checked').val();
  if(1 == planType){
    var price = document.getElementById('yearly_'+changeType+'_'+plan).innerHTML;
  } else {
    var price = document.getElementById('monthly_'+changeType+'_'+plan).innerHTML;
  }

  document.getElementById('total_'+changeType+'_'+plan).value = parseInt(price) * parseInt(duration);
  planTotal = document.getElementById('total_'+changeType+'_'+plan).value;
  if(document.getElementById('existing_'+changeType+'_'+plan)){
    existing = document.getElementById('existing_'+changeType+'_'+plan).value;
    document.getElementById('final_total_'+changeType+'_'+plan).value = parseInt(planTotal) - parseInt(existing);
    if(document.getElementById('final_total_'+changeType+'_'+plan).value <= 10){
      document.getElementById('final_total_'+changeType+'_'+plan).value = 10;
      $('#final_condition_'+changeType+'_'+plan).removeClass('hide');
    }  else {
      $('#final_condition_'+changeType+'_'+plan).addClass('hide');
    }
  }
}
function changeTotal(ele,changeType){
  var plan = $(ele).data('id');
  var planType = $(ele).val();
  if(1 == planType){
    var price = document.getElementById('yearly_'+changeType+'_'+plan).innerHTML;
  } else {
    var price = document.getElementById('monthly_'+changeType+'_'+plan).innerHTML;
  }
  var duration = document.getElementById('duration_'+changeType+'_'+plan).value;
  document.getElementById('total_'+changeType+'_'+plan).value = parseInt(price) * parseInt(duration);
  planTotal = document.getElementById('total_'+changeType+'_'+plan).value;
  if(document.getElementById('existing_'+changeType+'_'+plan)){
    existing = document.getElementById('existing_'+changeType+'_'+plan).value;
    document.getElementById('final_total_'+changeType+'_'+plan).value = parseInt(planTotal) - parseInt(existing);
    if(document.getElementById('final_total_'+changeType+'_'+plan).value <= 10){
      document.getElementById('final_total_'+changeType+'_'+plan).value = 10;
      $('#final_condition_'+changeType+'_'+plan).removeClass('hide');
    }  else {
      $('#final_condition_'+changeType+'_'+plan).addClass('hide');
    }
  }
}
</script>
@stop