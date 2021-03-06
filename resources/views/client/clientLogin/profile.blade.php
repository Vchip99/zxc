@extends('client.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Profile </h1>
  </section>
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
    @media screen and (max-width: 320px) {
      .content,.col-sm-12,.v-container, .container, .col-md-7, .list-group .list-group-item,.col-xs-7{
        padding-left: 0px !important;
        padding-right: 0px !important;
      }
    }

    @media screen and (min-width: 350px) and (max-width: 415px){
      .content,.col-sm-12,.v-container, .container, .col-md-7, .list-group .list-group-item,.col-xs-7{
        padding-left: 0px !important;
        padding-right: 0px !important;
      }
    }
  </style>
@stop
@section('dashboard_content')
	<div class="content-wrapper v-container tab-content" >
    <div id="profile" class="tab-pane active">
      <div class="container">
        <div class="row">
          <div class="col-md-7 col-md-offset-2">
            <b>Total Sms Count: <a href="#showSmsCount" data-toggle="modal"> {{Auth::guard('client')->user()->academic_sms_count + Auth::guard('client')->user()->message_sms_count + Auth::guard('client')->user()->lecture_sms_count + Auth::guard('client')->user()->otp_sms_count}}</a></b>
            <b><span style="float: right;">Total Credit:
            @if(Auth::guard('client')->user()->debit_sms_count > Auth::guard('client')->user()->credit_sms_count)
              + {{Auth::guard('client')->user()->debit_sms_count - Auth::guard('client')->user()->credit_sms_count}}
            @else
              - {{Auth::guard('client')->user()->credit_sms_count - Auth::guard('client')->user()->debit_sms_count}}
            @endif
            </span></b>
            <br><br>
            <div id="showSmsCount" class="modal fade" role="dialog">
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <button class="close" data-dismiss="modal">×</button>
                    <h2  class="modal-title">Sms Count Details</h2>
                  </div>
                  <div class="modal-body">
                    <div class="">
                      <fieldset>
                        <div class="form-group">
                          <label>Academic Sms Count: {{Auth::guard('client')->user()->academic_sms_count }}</label>
                        </div>
                        <div class="form-group">
                          <label>Message Sms Count: {{Auth::guard('client')->user()->message_sms_count }}</label>
                        </div>
                        <div class="form-group">
                          <label>Lecture Sms Count: {{Auth::guard('client')->user()->lecture_sms_count }}</label>
                        </div>
                        <div class="form-group">
                          <label>Otp Sms Count: {{Auth::guard('client')->user()->otp_sms_count }}</label>
                        </div>
                      </fieldset>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel panel-default">
              <div class="panel-heading">
                @if(Session::has('message'))
                  <div class="alert alert-success" id="message">
                    <li>
                      {{ Session::get('message') }}
                    </li>
                  </div>
                @endif
                <a class="btn-top pull-right"  href="#edit-all" class="btn btn-primary btn-success pull-right" data-toggle="modal" style="position: absolute;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit All</a>
                  <div id="edit-all" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-sm">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button class="close" data-dismiss="modal">×</button>
                          <h2  class="modal-title">Edit Profile</h2>
                        </div>
                        <div class="modal-body">
                          <div class="">
                            <form action="{{url('updateClientProfile')}}" method="POST" enctype="multipart/form-data">
                              {{ method_field('PUT') }}
                              {{ csrf_field() }}
                              <fieldset>
                                <div class="form-group">
                                  <label>Phone:</label>
                                  <input class="form-control" name="phone" type="text" value="{{Auth::guard('client')->user()->phone}}" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}" required>
                                </div>
                                <div class="form-group">
                                  <label>Photo:</label>
                                  <input class="form-control" placeholder="photo" name="photo" type="file">
                                  <label>Existing Photo:</label> {{basename(Auth::guard('client')->user()->photo)}}
                                </div>
                                <button class="btn btn-info" type="submit">Submit</button>
                                <button data-dismiss="modal" class="btn btn-info" type="button">Cancel</button>
                              </fieldset>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div  align="center" style="background-image: url('{{ url('images/user/profile-bg.jpg')}}');"">
                    @if(!empty(Auth::guard('client')->user()->photo))
                      <img alt="User Pic" style="max-height: 200px !important;" src="{{ asset(Auth::guard('client')->user()->photo) }}" id="profile-image1" class="img-circle img-responsive">
                    @else
                      <img alt="User Pic"  src="{{ url('images/user/user1.png')}}" id="profile-image1" class="img-circle img-responsive">
                    @endif
                  </div>
              </div>
              <ul class="list-group">
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Name</b></div>
                        <div class="col-xs-7 pull-left">{{Auth::guard('client')->user()->name}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-2" data-toggle="detail-2">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>EMAIL</b></div>
                        <div class="col-xs-7 pull-left">{{Auth::guard('client')->user()->email}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-3" data-toggle="detail-3">
                    <div class="col-xs-12">
                      <div class="row">
                         <div class="col-xs-5 "><b>PHONE</b></div>
                         <div class="col-xs-7 pull-left">{{Auth::guard('client')->user()->phone}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item" style="cursor: pointer;">
                  <div class="row toggle" id="dropdown-detail-2" data-toggle="detail-5">
                      <div class="col-xs-10">
                        <div class="row">
                            <div class="col-xs-6 ">
                              <b><a href="#updatePassword" data-toggle="modal" >Update Password</a></b>
                            </div>
                        </div>
                      </div>
                      <div class="col-xs-2" title="edit"><a href="#updatePassword" data-toggle="modal" ><i class="fa fa-edit pull-right" ></i></a></div>
                  </div>
                  <div id="updatePassword" class="modal fade" role="dialog">
                    <div class="modal-dialog modal-sm">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button class="close" data-dismiss="modal">×</button>
                          <h2  class="modal-title">Update Password</h2>
                        </div>
                        <div class="modal-body">
                          <div class="">
                            <form action="{{url('updateClientPassword')}}" method="POST" enctype="multipart/form-data">
                              {{ method_field('PUT') }}
                              {{ csrf_field() }}
                              <fieldset>
                                <div class="form-group">
                                  <label>Old Password:</label>
                                  <input class="form-control" type="password" name="old_password" placeholder="Enter Old Password" required/>
                                </div>
                                <div class="form-group">
                                  <label>New Password:</label>
                                  <input class="form-control" type="password" name="password" placeholder="Enter New Password" required/>
                                </div>
                                <div class="form-group">
                                  <label>Confirm New Password:</label>
                                  <input class="form-control" type="password" name="password_confirmation" placeholder="Confirm New Password" required/>
                                </div>
                                <button class="btn btn-info" type="submit">Submit</button>
                                <button data-dismiss="modal" class="btn btn-info" type="button">Cancel</button>
                              </fieldset>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
       </div>
    </div>
  </div>
  @include('footer.clientLiveChat')
@stop