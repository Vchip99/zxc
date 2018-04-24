@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Users Info </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> All Users </li>
      <li class="active"> Users Info </li>
    </ol>
  </section>
  <style type="text/css">
    .modal-body {
      overflow-x: auto;
    }
  </style>
@stop
@section('dashboard_content')

  <div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="top mrgn_40_btm">
        <div class="container">
          <div class="row">
            <div class="col-md-3 " id="search">
              <div class="input-group">
                <input type="text" id="search_student" name="student" class="form-control" placeholder="Search..." onkeyup="searchUsers(this.value);">
                  <span class="input-group-btn">
                    <button type="button" name="search" id="search-btn" class="btn btn-flat" ><i class="fa fa-search"></i>
                    </button>
                  </span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-lg-12" id="all-result">
            <div class="panel panel-info">
              <div class="panel-heading text-center">
                User Records
              </div>
              <div class="panel-body">
                <table  class="" id="client_users">
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Name</th>
                      <th>Client Approve</th>
                      <th>Courses</th>
                      <th>Test Series</th>
                      <th>Delete</th>
                    </tr>
                  </thead>
                  <tbody id="mobile_client_users" class="">
                    @if(count($clientusers) > 0)
                      @foreach($clientusers as  $index => $clientuser)
                        <tr>
                          <td> {{ $index + 1 }} </td>
                          <td>  <a href="#studentModal_{{$clientuser->id}}" data-toggle="modal">{{ $clientuser->name }}</a></td>
                          <td>
                            @if(1 == $clientuser->client_approve)
                              <input type="checkbox" value="" data-client_user_id="{{ $clientuser->id }}" data-client_id="{{ $clientuser->client_id }}" onclick="changeApproveStatus(this);" checked="checked">
                            @else
                              <input type="checkbox" value="" data-client_user_id="{{ $clientuser->id }}" data-client_id="{{ $clientuser->client_id }}" onclick="changeApproveStatus(this);">
                            @endif
                          </td>
                          <td><a href="#courseModal_{{$clientuser->id}}" data-toggle="modal">Approve/Unapprove Courses</a></td>
                          <td><a href="#subcategoryModal_{{$clientuser->id}}" data-toggle="modal">Approve/Unapprove Test Sub Categories</a></td>
                          <td><button class="btn btn-danger btn-xs delet-bt delet-btn" data-title="Delete" data-toggle="modal" data-target="#delete" data-client_user_id="{{ $clientuser->id }}" data-client_id="{{ $clientuser->client_id }}" onclick="deleteStudent(this);" ><span class="fa fa-trash-o" data-placement="top" data-toggle="tooltip" title="Delete"></span></button></td>

                          <div class="modal" id="studentModal_{{ $clientuser->id }}" role="dialog" style="display: none;">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal">×</button>
                                  <h4 class="modal-title">Student Details</h4>
                                  <div class="form-group">
                                    <div class="form-group"><label>Email:</label> {{ $clientuser->email }}</div>
                                    <div class="form-group"><label>Phone:</label> {{ $clientuser->phone }}</div>
                                    <div class="form-group"><a href="{{url('userTestResults')}}/{{ $clientuser->id }}">Test Result</a></div>
                                    <div class="form-group"><a href="{{url('userCourses')}}/{{ $clientuser->id }}">Course</a></div>
                                    <div class="form-group"><a href="{{url('userPlacement')}}/{{ $clientuser->id }}">Placement</a></div>
                                    <div class="form-group"><a href="{{url('userVideo')}}/{{ $clientuser->id }}">Student Video Url</a></div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </tr>
                      @endforeach
                    @endif
                    </tbody>
                </table>
              </div>
              @php
                $index = 1;
              @endphp
              <div id="courses_tests">
                @if(count($clientusers) > 0)
                  @foreach($clientusers as  $clientuser)
                    <div class="modal" id="courseModal_{{ $clientuser->id }}" role="dialog" style="display: none;">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">×</button>
                            <h4 class="modal-title">Status of Courses for {{ $clientuser->name }} </h4>
                          </div>
                          <div class="modal-body">
                            <table class="" id="client_user_{{ $clientuser->id }}">
                              <thead>
                                <tr>
                                  <th>Sr. No.</th>
                                  <th>Course</th>
                                  <th>Approve Status</th>
                                </tr>
                              </thead>
                              <tbody id="" class="">
                                @if(count($courses) > 0)
                                  @foreach($courses as  $index => $course)
                                    <tr>
                                      <td> {{ $index + 1 }} </td>
                                      <td>{{ $course->name }}</td>
                                      <td>
                                        @if(isset($userPurchasedCourses[$clientuser->id]) && in_array($course->id, $userPurchasedCourses[$clientuser->id]))
                                          <input type="checkbox" value="" data-client_user_id="{{ $clientuser->id }}" data-client_id="{{ $clientuser->client_id }}" data-course_id="{{$course->id}}" onclick="changeCourseStatus(this);" checked="checked">
                                        @else
                                          <input type="checkbox" value="" data-client_user_id="{{ $clientuser->id }}" data-client_id="{{ $clientuser->client_id }}" data-course_id="{{$course->id}}" onclick="changeCourseStatus(this);">
                                        @endif
                                      </td>
                                    </tr>
                                  @endforeach
                                @endif
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal" id="subcategoryModal_{{ $clientuser->id }}" role="dialog" style="display: none;">
                      <div class="modal-dialog ">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">×</button>
                            <h4 class="modal-title">Status of Test Sub Category for {{ $clientuser->name }} </h4>
                          </div>
                          <div class="modal-body">
                            <table class="" id="client_user_{{ $clientuser->id }}">
                              <thead>
                                <tr>
                                  <th>Sr. No.</th>
                                  <th>Sub Category</th>
                                  <th>Approve Status</th>
                                </tr>
                              </thead>
                              <tbody id="" class="">
                                @if(count($testSubCategories) > 0)
                                  @foreach($testSubCategories as  $index => $testSubCategory)
                                    <tr>
                                      <td> {{ $index++ }} </td>
                                      <td>{{ $testSubCategory->name }}</td>
                                      <td>
                                        @if(isset($userPurchasedTestSubCategories[$clientuser->id]) && in_array($testSubCategory->id, $userPurchasedTestSubCategories[$clientuser->id]))
                                          <input type="checkbox" value="" data-client_user_id="{{ $clientuser->id }}" data-client_id="{{ $clientuser->client_id }}" data-test_category_id="{{$testSubCategory->category_id}}" data-test_sub_category_id="{{$testSubCategory->id}}" onclick="changeTestSubCategoryStatus(this);" checked="checked">
                                        @else
                                          <input type="checkbox" value="" data-client_user_id="{{ $clientuser->id }}" data-client_id="{{ $clientuser->client_id }}" data-test_category_id="{{$testSubCategory->category_id}}" data-test_sub_category_id="{{$testSubCategory->id}}" onclick="changeTestSubCategoryStatus(this);">
                                        @endif
                                      </td>
                                    </tr>
                                  @endforeach
                                @endif
                                @if(count($clientPurchasedSubCategories) > 0)
                                  @foreach($clientPurchasedSubCategories as  $index => $clientPurchasedSubCategory)
                                    <tr>
                                      <td> {{ $index++ }} </td>
                                      <td>{{ $clientPurchasedSubCategory->name }}</td>
                                      <td>
                                        @if(isset($userPurchasedTestSubCategories[$clientuser->id]) && in_array($clientPurchasedSubCategory->id, $userPurchasedTestSubCategories[$clientuser->id]))
                                          <input type="checkbox" value="" data-client_user_id="{{ $clientuser->id }}" data-client_id="{{ $clientuser->client_id }}" data-test_category_id="{{$purchasedPayableSubCategories[$clientPurchasedSubCategory->id]->category_id}}" data-test_sub_category_id="{{$clientPurchasedSubCategory->id}}" onclick="changeTestSubCategoryStatus(this);" checked="checked">
                                        @else
                                          <input type="checkbox" value="" data-client_user_id="{{ $clientuser->id }}" data-client_id="{{ $clientuser->client_id }}" data-test_category_id="{{$purchasedPayableSubCategories[$clientPurchasedSubCategory->id]->category_id}}" data-test_sub_category_id="{{$clientPurchasedSubCategory->id}}" onclick="changeTestSubCategoryStatus(this);">
                                        @endif
                                      </td>
                                    </tr>
                                  @endforeach
                                @endif
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript">
  function searchUsers(student){
    if(student.length > 0){
      $.ajax({
          method: "POST",
          url: "{{url('searchUsers')}}",
          data:{student:student}
        })
        .done(function( msg ) {
          body = document.getElementById('mobile_client_users');
          body.innerHTML = '';
          renderRecords(msg, body);
        });
    }
  }

  function renderRecords(msg, body){

    if( 0 < msg['users'].length){
      $.each(msg['users'], function(idx, obj) {
        var eleTr = document.createElement('tr');
        var eleIndex = document.createElement('td');
        eleIndex.innerHTML = idx + 1;
        eleTr.appendChild(eleIndex);

        var eleName = document.createElement('td');
        eleName.innerHTML = '<a href="#studentModal_'+obj.id+'" data-toggle="modal">'+obj.name+'</a>';
        eleTr.appendChild(eleName);

        var eleApprove = document.createElement('td');
        approveInnerHTML = '<input type="checkbox" value="" data-client_user_id="'+ obj.id +'" data-client_id="'+ obj.client_id +'" onclick="changeApproveStatus(this);"';
        if( 1 == obj.client_approve){
          approveInnerHTML += 'checked = checked';
        }
        approveInnerHTML += '>';
        eleApprove.innerHTML = approveInnerHTML;
        eleTr.appendChild(eleApprove);

        var eleCourses = document.createElement('td');
        eleCourses.innerHTML = '<a href="#courseModal_'+ obj.id +'" data-toggle="modal">Approve/Unapprove Courses</a>';
        eleTr.appendChild(eleCourses);

        var eleTests = document.createElement('td');
        eleTests.innerHTML = '<a href="#subcategoryModal_'+ obj.id +'" data-toggle="modal">Approve/Unapprove Test Sub Categories</a>';
        eleTr.appendChild(eleTests);

        var eleDelete = document.createElement('td');
        eleDelete.innerHTML = '<button class="btn btn-danger btn-xs delet-bt delet-btn" data-title="Delete" data-toggle="modal" data-target="#delete" data-client_user_id="'+ obj.id +'" data-client_id="'+ obj.client_id +'" onclick="deleteStudent(this);" ><span class="fa fa-trash-o" data-placement="top" data-toggle="tooltip" title="Delete"></span></button>';
        eleTr.appendChild(eleDelete);

        var eleModel = document.createElement('div');
        eleModel.className = 'modal';
        eleModel.id = 'studentModal_'+obj.id;
        eleModel.setAttribute('role', 'dialog');
        var urlStudentTest = "{{url('userTestResults')}}/"+obj.id;
        var urlStudentCourse = "{{url('userCourses')}}/"+obj.id;
        var urlStudentPlacement = "{{url('userPlacement')}}/"+obj.id;
        var urlStudentVideo = "{{url('userVideo')}}/"+obj.id;
        var modelInnerHTML = '';
        modelInnerHTML='<div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button>';
        modelInnerHTML +='<h4 class="modal-title">Student Details</h4>';

        modelInnerHTML +='<div class="form-group"><div class="form-group"><label>Email:</label> '+obj.email+'</div><div class="form-group"><label>Phone:</label> '+obj.phone+'</div><div class="form-group"><a href="'+urlStudentTest+'">Test Result</a></div><div class="form-group"><a href="'+urlStudentCourse+'">Course</a></div>';
        modelInnerHTML +='<div class="form-group"><a href="'+urlStudentPlacement+'">Placement</a></div>';
        modelInnerHTML +='<div class="form-group"><a href="'+urlStudentVideo+'">Student Video Url</a></div>';

        modelInnerHTML +='</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div></div></div>';
        eleModel.innerHTML = modelInnerHTML;
        eleTr.appendChild(eleModel);
        body.appendChild(eleTr);
      });

      var coursesTests = document.getElementById('courses_tests');
      coursesTests.innerHTML = '';
      $.each(msg['users'], function(idx, obj) {
        var eleModel = document.createElement('div');
        eleModel.className = 'modal';
        eleModel.id = 'courseModal_'+obj.id;
        eleModel.setAttribute('role', 'dialog');
        var modelInnerHTML = '';
        var userId = obj.id;
        modelInnerHTML='<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Status of Courses for '+obj.name+'</h4></div>';
        modelInnerHTML +='<div class="modal-body"><table class="" id="client_user_'+obj.id+'"><thead><tr><th>Sr. No.</th><th>Course</th><th>Approve Status</th></tr></thead><tbody id="" class="">';
        if( 0 < msg['courses'].length){
          $.each(msg['courses'], function(idx, obj) {
            var index = idx + 1;
            modelInnerHTML +='<tr><td>'+ index +'</td><td>'+obj.name+'</td><td>';
            if(undefined !== msg['userPurchasedCourses'][userId] && msg['userPurchasedCourses'][userId].length > 0 && true == msg['userPurchasedCourses'][userId].indexOf(obj.id) > -1){
              modelInnerHTML +='<input type="checkbox" value="" data-client_user_id="'+ userId +'" data-client_id="'+obj.client_id+'" data-course_id="'+obj.id+'" onclick="changeCourseStatus(this);" checked="checked">';
            } else {
              modelInnerHTML +='<input type="checkbox" value="" data-client_user_id="'+ userId +'" data-client_id="'+obj.client_id+'" data-course_id="'+obj.id+'" onclick="changeCourseStatus(this);">';
            }
            modelInnerHTML +='</td></tr>';
          });
        }
        modelInnerHTML +='</tbody></table></div></div></div>';
        eleModel.innerHTML = modelInnerHTML;
        coursesTests.appendChild(eleModel);
        // for subcategory
        var eleSubcategoryModal = document.createElement('div');
        eleSubcategoryModal.className = 'modal';
        eleSubcategoryModal.id = 'subcategoryModal_'+obj.id;
        eleSubcategoryModal.setAttribute('role', 'dialog');
        var subcategoryModelInnerHTML = '';
        var userId = obj.id;
        subcategoryModelInnerHTML='<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Status of Test Sub Categories for '+obj.name+'</h4></div>';
        subcategoryModelInnerHTML +='<div class="modal-body"><table class="" id="client_user_'+obj.id+'"><thead><tr><th>Sr. No.</th><th>Sub Category</th><th>Approve Status</th></tr></thead><tbody id="" class="">';
        if( 0 < msg['testSubCategories'].length){
          $.each(msg['testSubCategories'], function(idx, obj) {
            var index = idx + 1;
            subcategoryModelInnerHTML +='<tr><td>'+ index +'</td><td>'+obj.name+'</td><td>';
            if(undefined !== msg['userPurchasedTestSubCategories'][userId] && msg['userPurchasedTestSubCategories'][userId].length > 0 && true == msg['userPurchasedTestSubCategories'][userId].indexOf(obj.id) > -1){
              subcategoryModelInnerHTML +='<input type="checkbox" value="" data-client_user_id="'+ userId +'" data-client_id="'+obj.client_id+'" data-test_category_id="'+obj.category_id+'" data-test_sub_category_id="'+obj.id+'" onclick="changeTestSubCategoryStatus(this);" checked="checked">';
            } else {
              subcategoryModelInnerHTML +='<input type="checkbox" value="" data-client_user_id="'+ userId +'" data-client_id="'+obj.client_id+'" data-test_category_id="'+obj.category_id+'" data-test_sub_category_id="'+obj.id+'" onclick="changeTestSubCategoryStatus(this);">';
            }
            subcategoryModelInnerHTML +='</td></tr>';
          });
        }
        if( 0 < msg['clientPurchasedSubCategories'].length){
          $.each(msg['clientPurchasedSubCategories'], function(idx, obj) {
            var index = idx + 1;
            subcategoryModelInnerHTML +='<tr><td>'+ index +'</td><td>'+obj.name+'</td><td>';
            if(undefined !== msg['userPurchasedTestSubCategories'][userId] && msg['userPurchasedTestSubCategories'][userId].length > 0 && true == msg['userPurchasedTestSubCategories'][userId].indexOf(obj.id) > -1){
              subcategoryModelInnerHTML +='<input type="checkbox" value="" data-client_user_id="'+ userId +'" data-client_id="'+msg['purchasedPayableSubCategories'][obj.id].client_id+'" data-test_category_id="'+msg['purchasedPayableSubCategories'][obj.id].category_id+'" data-test_sub_category_id="'+obj.id+'" onclick="changeTestSubCategoryStatus(this);" checked="checked">';
            } else {
              subcategoryModelInnerHTML +='<input type="checkbox" value="" data-client_user_id="'+ userId +'" data-client_id="'+msg['purchasedPayableSubCategories'][obj.id].client_id+'" data-test_category_id="'+msg['purchasedPayableSubCategories'][obj.id].category_id+'" data-test_sub_category_id="'+obj.id+'" onclick="changeTestSubCategoryStatus(this);">';
            }
            subcategoryModelInnerHTML +='</td></tr>';
          });
        }
        subcategoryModelInnerHTML +='</tbody></table></div></div></div>';
        eleSubcategoryModal.innerHTML = subcategoryModelInnerHTML;
        coursesTests.appendChild(eleSubcategoryModal);
      });


    } else {
      var eleTr = document.createElement('tr');
      var eleIndex = document.createElement('td');
      eleIndex.innerHTML = 'No result!';
      eleIndex.setAttribute('colspan', '6');
      eleTr.appendChild(eleIndex);
      body.appendChild(eleTr);
    }
  }

  function changeCourseStatus(ele){
    var client_user_id = $(ele).data('client_user_id');
    var client_id = $(ele).data('client_id');
    var course_id = $(ele).data('course_id');
    if(client_id > 0 && client_user_id > 0 && course_id > 0){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure. you want to change course approval?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    $.ajax({
                      method: "POST",
                      url: "{{url('changeClientUserCourseStatus')}}",
                      data: {client_id:client_id,client_user_id:client_user_id,course_id:course_id}
                    })
                    .done(function( msg ) {
                      if('false' == msg){
                        if('checked' == $(ele).attr('checked')){
                          $(ele).prop('checked', 'checked');
                        } else {
                          $(ele).prop('checked', '');
                        }
                      }
                    });
                  }
              },
              Cancle: function () {
                if('checked' == $(ele).attr('checked')){
                  $(ele).prop('checked', 'checked');
                } else {
                  $(ele).prop('checked', '');
                }
              }
          }
        });

    }
  }

  function changeTestSubCategoryStatus(ele){
    var client_user_id = $(ele).data('client_user_id');
    var client_id = $(ele).data('client_id');
    var test_category_id = $(ele).data('test_category_id');
    var test_sub_category_id = $(ele).data('test_sub_category_id');
    if(client_id > 0 && client_user_id > 0 && test_category_id > 0 && test_sub_category_id > 0){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure. you want to change test sub category approval?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    $.ajax({
                      method: "POST",
                      url: "{{url('changeClientUserTestSubCategoryStatus')}}",
                      data: {client_id:client_id,client_user_id:client_user_id,test_category_id:test_category_id,test_sub_category_id:test_sub_category_id}
                    })
                    .done(function( msg ) {
                      if('false' == msg){
                        if('checked' == $(ele).attr('checked')){
                          $(ele).prop('checked', 'checked');
                        } else {
                          $(ele).prop('checked', '');
                        }
                      }
                    });
                  }
              },
              Cancle: function () {
                if('checked' == $(ele).attr('checked')){
                  $(ele).prop('checked', 'checked');
                } else {
                  $(ele).prop('checked', '');
                }
              }
          }
        });

    }
  }

  function deleteStudent(ele){;
    var client_user_id = $(ele).data('client_user_id');
    var client_id = $(ele).data('client_id');
    var student = document.getElementById('search_student').value;
    if(client_user_id > 0 && client_id > 0){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure. you want to permanently delete this student?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    $.ajax({
                      method: "POST",
                      url: "{{url('deleteStudent')}}",
                      data:{client_id:client_id,client_user_id:client_user_id,student:student}
                    })
                    .done(function( msg ) {
                      body = document.getElementById('mobile_client_users');
                      body.innerHTML = '';
                      renderRecords(msg, body);
                    });
                  }
              },
              Cancle: function () {
              }
          }
        });
    }
  }

  function changeApproveStatus(ele){
    var client_user_id = $(ele).data('client_user_id');
    var client_id = $(ele).data('client_id');
    if(client_id > 0 && client_user_id > 0 ){
      $.confirm({
        title: 'Confirmation',
        content: 'Are you sure. you want to change user approval?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    $.ajax({
                      method: "POST",
                      url: "{{url('changeClientUserApproveStatus')}}",
                      data: {client_id:client_id,client_user_id:client_user_id}
                    })
                    .done(function( msg ) {
                      if('false' == msg){
                        if('checked' == $(ele).attr('checked')){
                          $(ele).prop('checked', 'checked');
                        } else {
                          $(ele).prop('checked', '');
                        }
                      }
                    });
                  }
              },
              Cancle: function () {
                if('checked' == $(ele).attr('checked')){
                  $(ele).prop('checked', 'checked');
                } else {
                  $(ele).prop('checked', '');
                }
              }
          }
        });

    }
  }
</script>
@stop