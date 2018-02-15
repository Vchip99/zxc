@extends('dashboard.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Profile </h1>
  </section>
@stop
@section('dashboard_content')
	<div class="content-wrapper v-container tab-content" >
    <div id="profile" class="tab-pane active">
      <div class="container">
        <div class="row">
          <div class="col-md-7 col-md-offset-2">
            <div class="panel panel-default">
              <div class="panel-heading">
                @if(Session::has('message'))
                  <div class="alert alert-success" id="message">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      {{ Session::get('message') }}
                  </div>
                @endif
                @if(count($errors) > 0)
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
                @endif
                <a class="btn-top pull-right"  href="#edit-all" class="btn btn-primary btn-success pull-right" data-toggle="modal" style="position: absolute;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit All</a>
                  <div id="edit-all" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button class="close" data-dismiss="modal">×</button>
                          <h2  class="modal-title">Edit Profile</h2>
                        </div>
                        <div class="modal-body">
                          <div class="">
                            <form action="{{url('updateProfile')}}" method="POST" enctype="multipart/form-data">
                              {{ method_field('PUT') }}
                              {{ csrf_field() }}
                              <fieldset>
                                <div class="form-group row">
                                  <label>Name:</label>
                                  <input class="form-control" placeholder="name" name="name" type="text" value="{{Auth::user()->name}}">
                                </div>
                                <div class="form-group">
                                  <label>Email:</label>
                                  <input class="form-control" placeholder="yourmail@example.com" name="email" type="text" value="{{Auth::user()->email}}">
                                </div>
                                <div class="form-group">
                                  <label>Phone:</label>
                                  <input class="form-control" placeholder="Mobile No." name="phone" type="text" value="{{Auth::user()->phone}}">
                                </div>
                                <div class="form-group @if ($errors->has('user_type')) has-error @endif">
                                  <label>Designation:</label>
                                    <select class="form-control slt mrgn_20_top" id="user" name="user_type" onChange="toggleOptions(this);" required="true">
                                      <option value="0">Select User</option>
                                      <option value="2" @if(2 == Auth::user()->user_type) selected @endif >Student</option>
                                      <option value="3" @if(3 == Auth::user()->user_type) selected @endif >Lecturer</option>
                                      <option value="4" @if(4 == Auth::user()->user_type) selected @endif >HOD</option>
                                      <option value="5" @if(5 == Auth::user()->user_type) selected @endif >Principal / Director</option>
                                      <option value="6" @if(6 == Auth::user()->user_type) selected @endif >TNP officer</option>
                                    </select>
                                    <p class="help-block hide" id="user_error" style="color: red;">Please select user.</p>
                                </div>
                                <div class="form-group @if ($errors->has('college')) has-error @endif">
                                  <label>College:</label>
                                    <select class="form-control  slt mrgn_20_top" id="clg" name="college" onChange="getDepartment(this);">
                                      <option value="0">Select College Name</option>
                                      <option value="other" id="other" @if('other' == Auth::user()->college_id) selected @endif >Other</option>
                                      @if(count($colleges) > 0)
                                        @foreach($colleges as $college)
                                          <option value="{{$college->id}}" @if($college->id == Auth::user()->college_id) selected @endif >{{$college->name}}</option>
                                        @endforeach
                                      @endif
                                    </select>
                                    <p class="help-block hide" id="college_error" style="color: red;">Please select college.</p>
                                </div>
                                <div class="form-group @if ($errors->has('department')) has-error @endif @if(5 == Auth::user()->user_type || 6 == Auth::user()->user_type) hide @endif" id="deptDiv" >
                                  <label>Department:</label>
                                  <select class="form-control  slt mrgn_20_top" name="department" id="dept" >
                                    <option value="0">Select Department</option>
                                    @if(count($collegeDepts) > 0)
                                      @foreach($collegeDepts as $collegeDept)
                                        <option value="{{$collegeDept->id}}" @if($collegeDept->id == Auth::user()->college_dept_id) selected @endif >{{$collegeDept->name}}</option>
                                      @endforeach
                                    @endif
                                  </select>
                                  <p class="help-block hide" id="department_error" style="color: red;">Please select department.</p>
                                </div>
                                  <div class="form-group @if ($errors->has('year')) has-error @endif @if(2 != Auth::user()->user_type) hide @endif" id="year" >
                                    <label>Year:</label>
                                    <select class="form-control  slt mrgn_20_top" name="year">
                                      <option value="0">Select Year</option>
                                      <option value="1" @if(1 == Auth::user()->year) selected @endif >First Year</option>
                                      <option value="2" @if(2 == Auth::user()->year) selected @endif >Second Year </option>
                                      <option value="3" @if(3 == Auth::user()->year) selected @endif >Third Year</option>
                                      <option value="4" @if(4 == Auth::user()->year) selected @endif >Final Year</option>
                                    </select>
                                    <p class="help-block hide" id="year_error" style="color: red;">Please select year.</p>
                                  </div>
                                  <div class="form-group mrgn_20_top @if ($errors->has('rollno')) has-error @endif @if(2 != Auth::user()->user_type) hide @endif" id="rollNo">
                                    <label>Roll No:</label>
                                    <input type="number" class="form-control" name="roll_no" id="roll" value="{{Auth::user()->roll_no}}" placeholder="Roll No." />
                                    <span class="help-block"></span>
                                    <p class="help-block hide" id="roll_error" style="color: red;">Please select roll no.</p>
                                  </div>
                                  <div class="hide form-group mrgn_20_top" id="other_source">
                                    <label>Other Source:</label>
                                    <input type="text" class="form-control" name="other_source" id="other_source_input" value="" placeholder="college/company name" />
                                    <p class="help-block hide" id="other_source_error" style="color: red;">Please enter college/company name.</p>
                                  </div>
                                <div class="form-group">
                                  <label>Photo:</label>
                                  <input class="form-control" placeholder="Mobile No." name="photo" type="file">
                                  <label>Existing Photo:</label> {{basename(Auth::user()->photo)}}
                                </div>
                                <div class="form-group @if(2 != Auth::user()->user_type) hide @endif">
                                  <label>Resume:</label>
                                  <input class="form-control" placeholder="Mobile No." name="resume" type="file">
                                  <label>Existing Resume:</label> {{basename(Auth::user()->resume)}}
                                </div>
                                <button data-dismiss="modal" class="btn btn-info" type="button">Cancel</button>
                                <button class="btn btn-info" type="submit">Submit</button>
                              </fieldset>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div  align="center" style="background-image: url('{{ url('images/user/profile-bg.jpg')}}');"">
                    @if(is_file(Auth::user()->photo) || (!empty(Auth::user()->photo) && false == preg_match('/userStorage/',Auth::user()->photo)))
                      <img alt="User Pic" style="max-height: 200px !important;" src="{{Auth::user()->photo}}" id="profile-image1" class="user-prof img-responsive">
                    @else
                      <img alt="User Pic" src="{{ url('images/user/user1.png')}}" id="profile-image1" class="img-circle img-responsive">
                    @endif
                  </div>
              </div>
              <ul class="list-group">
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-10">
                      <div class="row">
                        <div class="col-xs-5 "><b>Name</b></div>
                        <div class="col-xs-7 pull-left">{{Auth::user()->name}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-2" data-toggle="detail-2">
                    <div class="col-xs-10">
                      <div class="row">
                        <div class="col-xs-5 "><b>Email</b></div>
                        <div class="col-xs-7 pull-left">{{Auth::user()->email}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-3" data-toggle="detail-3">
                    <div class="col-xs-10">
                      <div class="row">
                         <div class="col-xs-5 "><b>Phone</b></div>
                         <div class="col-xs-7 pull-left">{{Auth::user()->phone}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                    <div class="col-xs-10">
                      <div class="row">
                         <div class="col-xs-5 "><b> Designation</b></div>
                         <div class="col-xs-7 ">{{$users[Auth::user()->user_type]}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                @if(Auth::user()->college_id > 0)
                  <li class="list-group-item">
                    <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                      <div class="col-xs-10">
                        <div class="row">
                           <div class="col-xs-5 "><b> College</b></div>
                           <div class="col-xs-7 ">{{Auth::user()->college->name}}</div>
                        </div>
                      </div>
                    </div>
                  </li>
                  @if(Auth::user()->college_dept_id > 0)
                    <li class="list-group-item">
                      <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                        <div class="col-xs-10">
                          <div class="row">
                             <div class="col-xs-5 "><b> Department</b></div>
                             <div class="col-xs-7 ">{{Auth::user()->department->name}}</div>
                          </div>
                        </div>
                      </div>
                    </li>
                  @endif
                @elseif('other' == Auth::user()->college_id)
                  <li class="list-group-item">
                    <div class="row toggle" id="dropdown-detail-4" data-toggle="detail-4">
                      <div class="col-xs-10">
                        <div class="row">
                           <div class="col-xs-5 "><b> Other</b></div>
                           <div class="col-xs-7 ">{{Auth::user()->other_source}}</div>
                        </div>
                      </div>
                    </div>
                  </li>
                @endif
                @if(2 == Auth::user()->user_type)
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-3" data-toggle="detail-3">
                    <div class="col-xs-10">
                      <div class="row">
                         <div class="col-xs-5 "><b>Year</b></div>
                         <div class="col-xs-7 pull-left">{{Auth::user()->year}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-3" data-toggle="detail-3">
                    <div class="col-xs-10">
                      <div class="row">
                         <div class="col-xs-5 "><b>Roll No</b></div>
                         <div class="col-xs-7 pull-left">{{Auth::user()->roll_no}}</div>
                      </div>
                    </div>
                  </div>
                </li>
                @endif

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
                            <form action="{{url('updatePassword')}}" method="POST" enctype="multipart/form-data">
                              {{ method_field('PUT') }}
                              {{ csrf_field() }}
                              <fieldset>
                                <div class="form-group">
                                  <label>Old Password:</label>
                                  <input class="form-control" type="password" name="old_password" placeholder="Enter Old Password" />
                                </div>
                                <div class="form-group">
                                  <label>New Password:</label>
                                  <input class="form-control" type="password" name="password" placeholder="Enter New Password" />
                                </div>
                                <div class="form-group">
                                  <label>Confirm New Password:</label>
                                  <input class="form-control" type="password" name="password_confirmation" placeholder="Confirm New Password" />
                                </div>
                                <button data-dismiss="modal" class="btn btn-info" type="button">Cancel</button>
                                <button class="btn btn-info" type="submit">Submit</button>
                              </fieldset>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                @if(2 == Auth::user()->user_type)
                <li class="list-group-item" style="cursor: pointer;">
                  <div class="row toggle" id="dropdown-detail-2" data-toggle="detail-5">
                      <div class="col-xs-10">
                        <div class="row">
                            <div class="col-xs-12 ">
                              <b><a href="#student_resume" data-toggle="modal" >Show Resume</a></b>
                            </div>
                        </div>
                      </div>
                      <div class="col-xs-2" title="edit"><a href="#student_resume" data-toggle="modal" ><i class="fa fa-edit pull-right" ></i></a></div>
                  </div>
                  <div id="student_resume" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button class="close" data-dismiss="modal">×</button>
                          <h2  class="modal-title">Resume</h2>
                        </div>
                        <div class="modal-body">
                          <div class="iframe-container">
                            @if(Auth::user()->resume)
                              <iframe src="{{asset(Auth::user()->resume)}}" frameborder="0"></iframe>
                            @else
                              Resume of Student is not uploaded
                            @endif
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                @endif
              </ul>
            </div>
          </div>
        </div>
       </div>
    </div>
  </div>
<script type="text/javascript">
  function toggleOptions(ele){
    var selectedVal = $(ele).val();
    if(3 == selectedVal || 4 == selectedVal){
      document.getElementById('year').classList.add("hide");
      document.getElementById('rollNo').classList.add("hide");
      document.getElementById('deptDiv').classList.add("show");
      document.getElementById('clg').classList.remove("hide");

      document.getElementById('deptDiv').classList.remove("hide");
      document.getElementById('year').classList.remove("show");
      document.getElementById('rollNo').classList.remove("show");
      document.getElementById('clg').classList.add("show");
    } else if(2 == selectedVal){
      document.getElementById('year').classList.add("show");
      document.getElementById('year').classList.remove("hide");

      document.getElementById('rollNo').classList.add("show");
      document.getElementById('rollNo').classList.remove("hide");
      document.getElementById('deptDiv').classList.add("show");
      document.getElementById('clg').classList.remove("hide");

      document.getElementById('deptDiv').classList.remove("hide");
      document.getElementById('clg').classList.add("show");
    } else if(5 == selectedVal || 6 == selectedVal){
      document.getElementById('year').classList.remove("show");
      document.getElementById('year').classList.add("hide");

      document.getElementById('rollNo').classList.remove("show");
      document.getElementById('rollNo').classList.add("hide");
      document.getElementById('deptDiv').classList.remove("show");
      document.getElementById('clg').classList.remove("hide");

      document.getElementById('deptDiv').classList.add("hide");
      document.getElementById('clg').classList.add("show");
    }
  }

  function getDepartment(ele){
    var college = $(ele).val();
    var userType = document.getElementById('user').value;
    if((2 == userType || 3 == userType || 4 == userType) && college > 0){
      $.ajax({
          method: "POST",
          url: "{{url('getDepartments')}}",
          data: {college:college}
      })
      .done(function( msg ) {
        if( msg ){
          showDeptYearRoll();
          document.getElementById('other_source').classList.add("hide");
          document.getElementById('other_source').classList.remove("show");
          select = document.getElementById('dept');
          select.innerHTML = '';
          var opt = document.createElement('option');
          opt.value = '';
          opt.innerHTML = 'Select Department';
          select.appendChild(opt);
          if( 0 < msg.length){
            $.each(msg, function(idx, obj) {
                var opt = document.createElement('option');
                opt.value = obj.id;
                opt.innerHTML = obj.name;
                select.appendChild(opt);
            });
          }
        }
      });
    } else if('other' == college){
      hideDeptYearRoll();
      document.getElementById('other_source').classList.add("show");
      document.getElementById('other_source').classList.remove("hide");
    } else {
      document.getElementById('other_source').classList.add("hide");
      document.getElementById('other_source').classList.remove("show");
    }
  }

  function showDeptYearRoll(){
    var userType = document.getElementById('user').value;
    if(3 == userType || 4 == userType){
      document.getElementById('year').classList.remove("show");
      document.getElementById('rollNo').classList.remove("show");
      document.getElementById('deptDiv').classList.add("show");

      document.getElementById('year').classList.add("hide");
      document.getElementById('rollNo').classList.add("hide");
      document.getElementById('deptDiv').classList.remove("hide");
    } else {
      document.getElementById('deptDiv').classList.add("show");
      document.getElementById('year').classList.add("show");
      document.getElementById('rollNo').classList.add("show");

      document.getElementById('deptDiv').classList.remove("hide");
      document.getElementById('year').classList.remove("hide");
      document.getElementById('rollNo').classList.remove("hide");
    }
  }

  function hideDeptYearRoll(){
    document.getElementById('deptDiv').classList.add("hide");
    document.getElementById('year').classList.add("hide");
    document.getElementById('rollNo').classList.add("hide");

    document.getElementById('deptDiv').classList.remove("show");
    document.getElementById('year').classList.remove("show");
    document.getElementById('rollNo').classList.remove("show");
  }
</script>
@stop