@extends('dashboard.dashboard')
@section('dashboard_header')
  <link href="{{asset('css/exam.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
  .padding-body{
    padding: 15px !important;
  }
  @media screen and (max-width: 768px) {
   .col-md-6{
          width: 45%;
          float: left;
          margin-left: 20px;
     }
   }
   @media screen and (max-width: 320px) {
    .data-sm .container, .data-sm .container .fluid-row{
      padding-left: 0px !important;
    }
    .data-sm .btn3d{
      min-width: 150px !important;
      margin-left: -20px;
    }
   }
   @media screen and (max-width: 527px) {
    .col-md-6{
          width: 100%;
          float: left;
          margin-left: 20px;
     }
   }
   .label-primary{margin-right: 2px;margin-left: 2px;}
   .divider {
      border-color: grey;
      border-style: solid;
      border-width: 0 0 1px;
      height: 10px;
      line-height: 20px;
      text-align:center;
      overflow: visible;
    }
    .btn-primary{
      width: 100px;
    }
  </style>
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Test </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Online Test</li>
      <li class="active">My Test</li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <section class="v_container ">
   <div class="container">
    <div class="row">
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
    </div>
    <div class="row">
      <a href="{{ url('college/'.Session::get('college_user_url').'/myVchipTest')}}" class="btn btn-primary"> Vchip Tests</a> &nbsp;<a href="{{ url('college/'.Session::get('college_user_url').'/myCollegeTest')}}" class="btn btn-default"> College Tests</a>
    </div>
    <br>
    <div class="row label-primary">
      <div class="col-md-8  col-md-offset-2  ">
        <div class="row text-center">
          <div class="col-md-4 col-sm-4  col-xs-12 mrgn_10_top_btm  ">
            <select class="form-control" id="category" name="category" onChange="selectSubcategory(this);">
              <option>Select Category</option>
              @foreach($testCategories as $testCategory)
                <option value="{{$testCategory->id}}"> {{$testCategory->name}} </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4 col-sm-4  col-xs-12 mrgn_10_top_btm">
            <select class="form-control" id="subcategory" name="subcategory" onChange="selectPanel(this);">
                <option>Select Sub Category</option>
              </select>
          </div>
          <div class="col-md-4 col-sm-4 col-xs-12 mrgn_10_top_btm pay-now hide" id="priceDiv">
            <span id="price">Price: 0 Rs.</span>
            <a id="paidStatus" class="btn btn-default" style="min-width: 100px;"></a>
            <div id="pay-now-form"></div>
          </div>
        </div>
      </div>
    </div>
    <div>
      <h3 class="divider">
        <span></span>
      </h3>
      <div class="divider">
        <span></span>
      </div>
    </div>
   </div>
  </section>
  <section>
    <div class="container exam-panel" id="subjects">
      @if(count($testSubjects)>0)
        @foreach($testSubjects as $subCategoryId => $testSubjectArr)
          @if(isset($subcategories[$subCategoryId]) && count($testSubjectArr) > 0)
            <span style="color: Blue;"><h4>{{$subcategories[$subCategoryId]}}</h4></span>
            @foreach($testSubjectArr as $testSubject)
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" >
              <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingOne">
                  <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#subject{{$testSubject->id}}" aria-expanded="true" aria-controls="collapseOne" title="{{ $testSubject->name }}">
                      <i class="more-less glyphicon glyphicon-plus"></i>
                      {{ $testSubject->name }}
                    </a>
                  </h4>
                </div>
                <div id="subject{{$testSubject->id}}" class="panel-collapse collapse panel-lg in" role="tabpanel" aria-labelledby="headingOne" aria-expanded="true">
                  <div class="panel-body">
                    <table class="table data-lg">
                      <thead>
                        <tr>
                            <th>Test Number</th>
                            <th>Start Test</th>
                            <th>Result</th>
                            <th>Date to Active</th>
                            <th>Price</th>
                            <th>Purchase Test</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(isset($testSubjectPapers[$testSubject->id]))
                        @foreach($testSubjectPapers[$testSubject->id] as $testSubjectPaper)
                          @if(in_array($testSubjectPaper->id, $alreadyGivenPapers))
                            <tr style="background-color: #b3c2dc;overflow: auto;">
                          @else
                            <tr style="overflow: auto;">
                          @endif
                              <td class=" ">{{ $testSubjectPaper->name }}</td>
                              <td id="startTest_{{$testSubjectPaper->id}}">
                                @if($currentDate < $testSubjectPaper->date_to_active)
                                  <button disabled="true" title="Test will be enabled on date to active."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button>
                                @elseif(in_array($testSubjectPaper->id, $alreadyGivenPapers))
                                  <button disabled="true" title="Already test is given."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button>
                                @else
                                  @if(in_array($testSubjectPaper->id, $registeredPaperIds) || isset($purchasedSubCategories[$testSubjectPaper->test_sub_category_id]) || 0 == $testSubjectPaper->price)
                                    <button onClick="startTest(this);" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->test_category_id}}" data-subcategory="{{$testSubjectPaper->test_sub_category_id}}"  title="Start Test!"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                                  @else
                                    <button disabled="true" title="Please purchase to give test."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button>
                                  @endif
                                @endif
                              </td>
                              <td id="showUserResultBtn_{{$testSubjectPaper->id}}">
                                @if($currentDate < $testSubjectPaper->date_to_active)
                                    <button disabled="true" title="Result will enabled after test given"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
                                @elseif(in_array($testSubjectPaper->id, $alreadyGivenPapers))
                                  <form id="showUserTestResult_{{$testSubjectPaper->id}}" method="POST" action="{{ url('college/'.Session::get('college_user_url').'/showUserTestResult') }}" target="_blank">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="paper_id" value="{{$testSubjectPaper->id}}">
                                    <input type="hidden" name="category_id" value="{{$testSubjectPaper->test_category_id}}">
                                    <input type="hidden" name="subcategory_id" value="{{$testSubjectPaper->test_sub_category_id}}">
                                    <input type="hidden" name="subject_id" value="{{$testSubject->id}}">
                                  </form>
                                  <button onClick="showUserTestResult(this);" data-paper_id="{{$testSubjectPaper->id}}" title="Result!"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
                                @else
                                  <button disabled="true" title="Result will enabled after test given."><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
                                @endif
                              </td>
                              <td class=" ">{{ $testSubjectPaper->date_to_active }}</td>
                              <td class=""><i class="fa fa-inr"></i>{{ $testSubjectPaper->price }}</td>
                              @if($currentDate < $testSubjectPaper->date_to_active)
                                <td>
                                  <button class="btn-primary" disabled="true" title="Purchase test will be enabled on date to active">
                                  @if($testSubjectPaper->price > 0)
                                    Pay Now
                                  @else
                                    Free
                                  @endif
                                  </button>
                                </td>
                              @else
                                @if(in_array($testSubjectPaper->id, $registeredPaperIds) || isset($purchasedSubCategories[$testSubjectPaper->test_sub_category_id]) || 0 == $testSubjectPaper->price)
                                  @if($testSubjectPaper->price > 0)
                                    <td>Paid</td>
                                  @else
                                    <td>Free</td>
                                  @endif
                                @else
                                  @if($testSubjectPaper->price > 0)
                                    <td data-paper="{{$testSubjectPaper->id}}" onClick="purchaseTest(this);">
                                      <form id="purchaseTest_{{$testSubjectPaper->id}}" method="POST" action="{{ url('purchaseTest') }}">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="paper_id" value="{{$testSubjectPaper->id}}">
                                        <input type="hidden" name="category_id" value="{{$testSubjectPaper->test_category_id}}">
                                        <input type="hidden" name="subcategory_id" value="{{$testSubjectPaper->test_sub_category_id}}">
                                        <input type="hidden" name="subject_id" value="{{$testSubject->id}}">
                                      </form>
                                      <button class="btn-primary" title="Purchase Test">Pay Now</button>
                                    </td>
                                  @else
                                      <td>Free</td>
                                  @endif
                                @endif
                              @endif
                          </tr>
                        @endforeach
                        @endif
                      </tbody>
                    </table>
                    <div class="data-sm">
                      @if(isset($testSubjectPapers[$testSubject->id]))
                        @foreach($testSubjectPapers[$testSubject->id] as $testSubjectPaper)
                            <div class=" panel panel-info" >
                              @if(in_array($testSubjectPaper->id, $alreadyGivenPapers))
                                <div class="toggle panel-heading" data-toggle="paper{{$testSubjectPaper->id}}" style="background-color: #b3c2dc;">
                              @else
                                <div class="toggle panel-heading" data-toggle="paper{{$testSubjectPaper->id}}">
                              @endif
                                {{$testSubjectPaper->name}}<span class="col-xs-2 pull-right"><i class="fa fa-chevron-down pull-right"></i></span>
                                </div>
                                  <div id="paper{{$testSubjectPaper->id}}" class="panel-body" style="padding:2px 0px;">
                                    <div class="container">
                                        <div class="fluid-row">
                                          <ul class="">
                                            <li id="startTest_mobile_{{$testSubjectPaper->id}}">
                                              @if($currentDate < $testSubjectPaper->date_to_active)
                                                <button class="btn-magick btn-sm btn3d" disabled="true"  title="Test will be enabled on date to active."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Start</button>
                                              @elseif(in_array($testSubjectPaper->id, $alreadyGivenPapers))
                                                <button class="btn-magick btn-sm btn3d" disabled="true"  title="Already test is given."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Start</button>
                                              @else
                                                @if(in_array($testSubjectPaper->id, $registeredPaperIds)  || isset($purchasedSubCategories[$testSubjectPaper->test_sub_category_id]) || 0 == $testSubjectPaper->price)
                                                  <button class="btn-magick btn-sm btn3d" onClick="startTest(this);" data-paper="{{$testSubjectPaper->id}}" data-subject="{{$testSubject->id}}" data-category="{{$testSubjectPaper->test_category_id}}" data-subcategory="{{$testSubjectPaper->test_sub_category_id}}"   title="Start Test!"><span class="fa fa-arrow-circle-right" aria-hidden="true"></span>Start</button>
                                                @else
                                                  <button class="btn-magick btn-sm btn3d" disabled="true"  title="Please purchase to give test."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Start</button>
                                                @endif
                                              @endif
                                            </li>
                                            <li id="showUserResultMobileBtn_{{$testSubjectPaper->id}}">
                                              @if($currentDate < $testSubjectPaper->date_to_active)
                                                <button class="btn-magick btn-sm btn3d" disabled="true"  title="Result will enabled after test given"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
                                              @elseif(in_array($testSubjectPaper->id, $alreadyGivenPapers))
                                                <form id="showUserTestResult_{{$testSubjectPaper->id}}" method="POST" action="{{ url('college/'.Session::get('college_user_url').'/showUserTestResult') }}" target="_blank">
                                                  {{ csrf_field() }}
                                                  <input type="hidden" name="paper_id" value="{{$testSubjectPaper->id}}">
                                                  <input type="hidden" name="category_id" value="{{$testSubjectPaper->test_category_id}}">
                                                  <input type="hidden" name="subcategory_id" value="{{$testSubjectPaper->test_sub_category_id}}">
                                                  <input type="hidden" name="subject_id" value="{{$testSubject->id}}">
                                                </form>
                                                <button class="btn-magick btn-sm btn3d" onClick="showUserTestResult(this);" data-paper_id="{{$testSubjectPaper->id}}"  title="Result!"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
                                              @else
                                                <button class="btn-magick btn-sm btn3d" disabled="true"  title="Result will enabled after test given."><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>
                                              @endif
                                            </li>
                                            <li>
                                              <button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-calendar"></span> {{ $testSubjectPaper->date_to_active }}</button>
                                            </li>
                                            <li>
                                              <button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-inr"></span> {{ $testSubjectPaper->price }} </button>
                                            </li>
                                            @if($currentDate < $testSubjectPaper->date_to_active)
                                              <li><button class="btn-magick btn-sm btn3d" disabled="true" title="Purchase test will be enabled on date to active">
                                                @if($testSubjectPaper->price > 0)
                                                  Pay Now
                                                @else
                                                  Free
                                                @endif
                                              </button></li>
                                            @else
                                              @if(in_array($testSubjectPaper->id, $registeredPaperIds) || isset($purchasedSubCategories[$testSubjectPaper->test_sub_category_id]) || 0 == $testSubjectPaper->price)
                                                <li><button  disabled="true" class="btn-magick btn-sm btn3d">
                                                  @if($testSubjectPaper->price > 0)
                                                    Paid
                                                  @else
                                                    Free
                                                  @endif
                                                </button></li>
                                              @else
                                                @if($testSubjectPaper->price > 0)
                                                  <li data-paper="{{$testSubjectPaper->id}}" onClick="purchaseMobileTest(this);"><button class="btn-magick btn-sm btn3d" title="Purchase Test"> Pay Now</button></li>
                                                  <form id="purchaseTest_mobile_{{$testSubjectPaper->id}}" method="POST" action="{{ url('purchaseTest') }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="paper_id" value="{{$testSubjectPaper->id}}">
                                                    <input type="hidden" name="category_id" value="{{$testSubjectPaper->test_category_id}}">
                                                    <input type="hidden" name="subcategory_id" value="{{$testSubjectPaper->test_sub_category_id}}">
                                                    <input type="hidden" name="subject_id" value="{{$testSubject->id}}">
                                                  </form>
                                                @else
                                                  <li><button  disabled="true" class="btn-magick btn-sm btn3d">Free</button></li>
                                                @endif
                                              @endif
                                            @endif
                                          </ul>
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
            @endforeach
          @endif
        @endforeach
      @else
        No Tests are given.
      @endif
      </div>
  </section>
  <script type="text/javascript">
    $('li#onlineTest').addClass('active');

    function startTest(ele){
      var windowHeight = screen.height;
      var windowWidth = screen.width;
      var popup_window =window.open("", 'My Window', 'height='+windowHeight+'px !important,width='+windowWidth+'px !important');
      var paper = parseInt($(ele).data('paper'));
      var subject = parseInt($(ele).data('subject'));
      var category = parseInt($(ele).data('category'));
      var subcategory = parseInt($(ele).data('subcategory'));
      var userId = parseInt(document.getElementById('user_id').value);
      if(0 < userId){
        $.ajax({
                method: "POST",
                url: "{{url('setSessions')}}",
                data: {paper:paper, subject:subject, category:category, subcategory:subcategory}
            })
            .done(function( msg ) {
              if( msg ){
                popup_window.location = "{{ url('instructions')}}";
                popup_window.focus();
              }
            });
      } else {
        $.confirm({
          title: 'Confirmation',
          content: 'Please login first to start test. Click "Ok" button to login.',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      window.location="{{url('/home')}}";
                    }
                },
                Cancel: function () {
                }
            }
          });
      }
    }

    function checkIsTestGiven(paper,subject,category,subcategory,userId){
      $.ajax({
              method: "POST",
              url: "{{url('isTestGiven')}}",
              data: {paper:paper, subject:subject, category:category, subcategory:subcategory, userId:userId}
          })
          .done(function( msg ) {
            if( 'true' == msg ){
              var ele = document.getElementById('startTest_'+paper);
              ele.innerHTML = '<button disabled="true"  title="Already test is given."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i>';

              var startTestMobile = document.getElementById('startTest_mobile_'+paper);
              startTestMobile.innerHTML = '<button class="btn-magick btn-sm btn3d" disabled="true"  title="Already test is given."><span class="fa fa-arrow-circle-right" aria-hidden="true" >Start</span></button>';

              var showUserResultBtn = document.getElementById('showUserResultBtn_'+paper);
              var csrfToken = '{{ csrf_field() }}';
              var url = "{{ url('college/'.Session::get('college_user_url').'/showUserTestResult') }}";
              showUserResultBtnInnerHtml = '<form id="showUserTestResult_'+paper+'" method="POST" action="'+url+'">'+csrfToken;
              showUserResultBtnInnerHtml += '<input type="hidden" name="paper_id" value="'+paper+'"><input type="hidden" name="category_id" value="'+category+'"><input type="hidden" name="subcategory_id" value="'+subcategory+'"><input type="hidden" name="subject_id" value="'+subject+'"></form>';
          showUserResultBtnInnerHtml += '<button  onClick="showUserTestResult(this);" data-paper_id="'+paper+'"  title="Result!"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
          showUserResultBtn.innerHTML = showUserResultBtnInnerHtml;


              var showUserResultMobileBtn = document.getElementById('showUserResultMobileBtn_'+paper);
              showUserResultMobileBtn.innerHTML= '<button class="btn-magick btn-sm btn3d" onClick="showUserTestResult(this);" data-paper_id="'+paper+'"  title="Result!"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';

            }
          });
    }

    function showUserTestResult(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'Do you want to see result?',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var paperId = parseInt($(ele).data('paper_id'));
                      document.getElementById('showUserTestResult_'+paperId).submit();
                    }
                },
                Cancel: function () {
                }
            }
          });
    }

    function selectSubcategory(ele){
      id = parseInt($(ele).val());
      var userId = parseInt(document.getElementById('user_id').value);
      if( 0 < id && 0 < userId ){
        $.ajax({
            method: "POST",
            url: "{{url('getSubCategories')}}",
            data: {id:id, userId:userId}
          })
          .done(function( msg ) {
            select = document.getElementById('subcategory');
            select.innerHTML = '';
            var opt = document.createElement('option');
          opt.value = '';
          opt.innerHTML = 'Select Sub Category';
          select.appendChild(opt);
          if( 0 < msg.length){
            $.each(msg, function(idx, obj) {
                var opt = document.createElement('option');
                opt.value = obj.id;
                opt.innerHTML = obj.name;
                opt.setAttribute('data-price', obj.price);
                select.appendChild(opt);
            });
          }
        });
      }
    }

    function selectPanel(ele) {
      var cat = parseInt($('select#category').val());
      var subcat = parseInt($('select#subcategory').val());
      var userId = parseInt(document.getElementById('user_id').value);
      $('#priceDiv').removeClass('hide');

      if($(ele).find(':selected').data('price') > 0){
        $('#price').html('Price: '+$(ele).find(':selected').data('price')+' Rs.');
        if( userId > 0 ){
          document.getElementById('paidStatus').text = 'Pay Now';
          document.getElementById('paidStatus').setAttribute('onClick', 'purchaseSubCategory(this);');
          var payNowDiv = document.getElementById('pay-now-form');
          var payUrl = "{{ url('purchaseTestSubCategory') }}";
          var csrfField = '{{ csrf_field() }}';
          payNowDiv.innerHTML = '<form id="purchaseSubCategory" method="POST" action="'+payUrl+'">'+csrfField +'<input type="hidden" name="category_id" value="'+cat+'"><input type="hidden" name="subcategory_id" value="'+subcat+'"></form>';
        } else {
          document.getElementById('paidStatus').setAttribute('onClick', 'checkLogin(this);');
          document.getElementById('paidStatus').text = 'Pay Now';
          document.getElementById('pay-now-form').innerHTML = '';
        }
      } else {
        $('#price').html('Price: 0 Rs.');
        document.getElementById('paidStatus').removeAttribute('onClick');
        document.getElementById('paidStatus').text = 'Free';
        document.getElementById('pay-now-form').innerHTML = '';
      }

      if( 0 < cat && 0 < subcat ){
        $.ajax({
                method: "POST",
                url: "{{url('getDataByCatSubCat')}}",
                data: {cat:cat, subcat:subcat, user_id:userId}
            })
            .done(function( msg ) {
              divEle = document.getElementById('subjects');
              divEle.innerHTML = '';
              if(undefined !== msg['subjects'] && msg['subjects'].length > 0) {
                $.each(msg['subjects'], function(ind, obj){
                  var subId = obj.id;
                  var mainPanelDiv = document.createElement('div');
                  mainPanelDiv.className = "panel-group";
                  mainPanelDiv.setAttribute('role','tablist');
                  mainPanelDiv.setAttribute('aria-multiselectable','true');
                  mainPanelDiv.id = 'headingOne';

                  var defaultPanelDiv = document.createElement('div');
                  defaultPanelDiv.className = "panel panel-default";

                  var firstMainDiv = document.createElement('div');
                  firstMainDiv.className = "panel-heading";
                  firstMainDiv.setAttribute('role','tab');
                  firstMainDiv.id = 'headingOne';

                  var h4Ele = document.createElement('h4');
                  h4Ele.className = 'panel-title';
                  h4Ele.innerHTML = '<a role="button" data-toggle="collapse" data-parent="#accordion" href="#subject'+ subId +'" aria-expanded="true" aria-controls="collapseOne"><i class="more-less glyphicon glyphicon-plus"></i>'+ obj.name + '</a>';
                  firstMainDiv.appendChild(h4Ele);
                  defaultPanelDiv.appendChild(firstMainDiv);

                  var secondMainDiv = document.createElement('div');
                  secondMainDiv.id = 'subject'+ subId;
                  secondMainDiv.className = "panel-collapse collapse panel-lg in";
                  secondMainDiv.setAttribute('role','tabpanel');
                  secondMainDiv.setAttribute('aria-labelledby','headingOne');
                  secondMainDiv.setAttribute('aria-expandedy',true);

                  var tableDiv = document.createElement('div');
                  tableDiv.className = 'panel-body';

                  var tableEle = document.createElement('table');
                  tableEle.className = "table data-lg";
                  if (undefined !== msg['papers'][subId] && msg['papers'][subId].length) {
                    var tableHead = document.createElement('thead');
                    var tableTr = document.createElement('tr');
                    var trInnerhtml = '';
                    trInnerhtml += '<th>Test Name</th>';
                    trInnerhtml += '<th>Start Test</th>';
                    trInnerhtml += '<th>Result</th>';
                    trInnerhtml += '<th>Date to Active</th>';
                    trInnerhtml += '<th>Price</th>';
                    trInnerhtml += '<th>Purchase Test</th>';
                    tableTr.innerHTML = trInnerhtml;
                    tableHead.appendChild(tableTr);
                    tableEle.appendChild(tableHead);
                  }
                  var tableBody = document.createElement('tbody');
                  if (undefined !== msg['papers'][subId] && msg['papers'][subId].length) {
                    $.each(msg['papers'][subId], function(ind, obj){
                      var tbodyTr = document.createElement("tr");
                      if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
                        tbodyTr.setAttribute('style','background-color: #b3c2dc;');
                      }
                      var divInnerHtml = '';
                      divInnerHtml += '<td class=" ">'+ obj.name+'</td>';
                      if(msg['currentDate'] < obj.date_to_active){
                        divInnerHtml += '<td id="startTest_'+obj.id+'"><button disabled="true"  title="Test will be enabled on date to active."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
                      } else {
                        if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
                          divInnerHtml += '<td id="startTest_'+obj.id+'"><button disabled="true"  title="Already test is given."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
                        } else {
                          if(true == (msg['registeredPaperIds'][obj.id] > 0) || true == (msg['purchasedSubCategories'][obj.test_sub_category_id] > 0) || 0 == obj.price ){
                            divInnerHtml += '<td id="startTest_'+obj.id+'"><button onClick="startTest(this);" data-paper="'+ obj.id +'" data-subject="'+ obj.test_subject_id +'" data-category="'+ obj.test_category_id +'" data-subcategory="'+ obj.test_sub_category_id+'"  title="Start Test!"><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
                          } else {
                            divInnerHtml += '<td id="startTest_'+obj.id+'"><button disabled="true"  title="Please purchase to give test."><i class="fa fa-arrow-circle-right" aria-hidden="true" ></i></button></td>';
                          }
                        }
                      }
                      if(msg['currentDate'] < obj.date_to_active){
                        divInnerHtml += '<td id="showUserResultBtn_'+obj.id+'">';
                        divInnerHtml += '<button disabled="true"  title="Result will display after test given"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
                        divInnerHtml += '</td>';
                      } else if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
                        var testUrl = "{{ url('college/'.Session::get('college_user_url').'/showUserTestResult') }}";
                        var csrf_token = '{{ csrf_field() }}';
                        divInnerHtml += '<td id="showUserResultBtn_'+obj.id+'">';
                        divInnerHtml += '<form id="showUserTestResult_'+obj.id+'" method="POST" action="'+testUrl+'" target="_blank">';
                        divInnerHtml += csrf_token;
                        divInnerHtml +='<input type="hidden" name="paper_id" value="'+obj.id+'"><input type="hidden" name="category_id" value="'+ obj.test_category_id +'"><input type="hidden" name="subcategory_id" value="'+ obj.test_sub_category_id+'"><input type="hidden" name="subject_id" value="'+ obj.test_subject_id +'"></form>';
                        divInnerHtml += '<button onClick="showUserTestResult(this);" data-paper_id="'+obj.id+'"  title="Result!"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
                        divInnerHtml += '</td>';
                      } else {
                        divInnerHtml += '<td id="showUserResultBtn_'+obj.id+'">';
                        divInnerHtml += '<button disabled="true"  title="Result will display after test given"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>';
                        divInnerHtml += '</td>';
                      }
                      divInnerHtml += '<td class=" ">'+ obj.date_to_active +'</td>';
                      divInnerHtml += '<td class=""><i class="fa fa-inr"></i>'+ obj.price +'</td>';

                      if(msg['currentDate'] < obj.date_to_active){
                        if(obj.price > 1){
                          divInnerHtml += '<td><button class="btn-primary" disabled="true" title="Purchase test will be enabled on date to active">Pay Now</button></td>';
                        } else {
                          divInnerHtml += '<td><button disabled="true" title="Purchase test will be enabled on date to active">Free</button></td>';
                        }
                      } else {
                        if(true == (msg['registeredPaperIds'][obj.id] > 0) || true == (msg['purchasedSubCategories'][obj.test_sub_category_id] > 0) || 0 == obj.price){
                          if(obj.price > 1){
                            divInnerHtml += '<td>Paid</td>';
                          } else {
                            divInnerHtml += '<td>Free</td>';
                          }
                        } else {
                          if(obj.price > 1){
                            var purchaseTestUrl = "{{ url('purchaseTest') }}";
                            var csrf_token = '{{ csrf_field() }}';
                            divInnerHtml += '<td><form id="purchaseTest_'+obj.id+'" method="POST" action="'+purchaseTestUrl+'">'+csrf_token+'<input type="hidden" name="paper_id" value="'+obj.id+'"><input type="hidden" name="category_id" value="'+ obj.test_category_id +'"><input type="hidden" name="subcategory_id" value="'+ obj.test_sub_category_id +'"><input type="hidden" name="subject_id" value="'+ obj.test_subject_id +'"></form><button class="btn-primary" title="Purchase Test" data-paper="'+obj.id+'" onClick="purchaseTest(this);">Pay Now</button></td>';
                          } else {
                            divInnerHtml += '<td>Free</td>';
                          }
                        }
                      }
                      tbodyTr.innerHTML = divInnerHtml;
                      tableBody.appendChild(tbodyTr);
                      tableEle.appendChild(tableBody);
                    });
                  } else {
                    tableBody.innerHTML = 'No papers are available..';
                    tableEle.appendChild(tableBody);
                  }
                  tableDiv.appendChild(tableEle);

                  var mainSmallDiv = document.createElement('div');
                  mainSmallDiv.className = "data-sm";

                  if (undefined !== msg['papers'][subId] && msg['papers'][subId].length) {
                    $.each(msg['papers'][subId], function(ind, obj){
                      var panelDiv = document.createElement('div');
                      panelDiv.className = 'panel panel-info';

                      var panelHeadingDiv = document.createElement('div');
                      panelHeadingDiv.className = 'toggle panel-heading';
                      panelHeadingDiv.setAttribute('data-toggle','paper'+obj.id);
                      if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
                        panelHeadingDiv.setAttribute('style','background-color: #b3c2dc;');
                      }
                      panelHeadingDiv.innerHTML = obj.name;

                      var spanEle = document.createElement('span');
                      spanEle.className = 'col-xs-2 pull-right';
                      spanEle.innerHTML = '<i class="fa fa-chevron-down pull-right"></i>';
                      panelHeadingDiv.appendChild(spanEle);

                      panelDiv.appendChild(panelHeadingDiv);

                      var panelContentDiv = document.createElement('div');
                      panelContentDiv.id = 'paper'+obj.id;
                      panelContentDiv.className = 'panel-body';

                      var containerDiv = document.createElement('div');
                      containerDiv.className='container';

                      var rowDiv = document.createElement('div');
                      rowDiv.className = 'fluid-row';

                      var ulDiv = document.createElement('ul');
                      ulDivInnerHtml = '';
                      if(msg['currentDate'] < obj.date_to_active){
                        ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" disabled="true"  title="Test will be enabled on date to active."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
                      } else {
                        if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
                          ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" disabled="true"  title="Already test is given."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
                        } else {
                          if(true == (msg['registeredPaperIds'][obj.id] > 0) || true == (msg['purchasedSubCategories'][obj.test_sub_category_id] > 0) || 0 == obj.price ){
                            ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" onClick="startTest(this);" data-paper="'+ obj.id +'" data-subject="'+ obj.test_subject_id +'" data-category="'+ obj.test_category_id +'" data-subcategory="'+ obj.test_sub_category_id+'"  title="Start Test!"><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
                          } else {
                            ulDivInnerHtml += '<li id="startTest_mobile_'+obj.id+'"><button class="btn-magick btn-sm btn3d" disabled="true"  title="Please purchase to give test."><span class="fa fa-arrow-circle-right" aria-hidden="true" ></span>Strat</button></li>';
                          }
                        }
                      }
                      if(msg['currentDate'] < obj.date_to_active){
                          ulDivInnerHtml += '<li id="showUserResultMobileBtn_'+obj.id+'">';
                          ulDivInnerHtml += '<button class="btn-magick btn-sm btn3d" disabled="true"  title="Result will display after test given"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
                          ulDivInnerHtml += '</li>';
                      } else if(msg['alreadyGivenPapers'].length > 0 && true == msg['alreadyGivenPapers'].indexOf(obj.id) > -1){
                          var testUrl = "{{ url('college/'.Session::get('college_user_url').'/showUserTestResult') }}";
                          var csrf_token = '{{ csrf_field() }}';
                          ulDivInnerHtml += '<li id="showUserResultMobileBtn_'+obj.id+'">';
                          ulDivInnerHtml += '<form id="showUserTestResult_'+obj.id+'" method="POST" action="'+testUrl+'" target="_blank">';
                          ulDivInnerHtml += csrf_token;
                          ulDivInnerHtml +='<input type="hidden" name="paper_id" value="'+obj.id+'"><input type="hidden" name="category_id" value="'+ obj.test_category_id +'"><input type="hidden" name="subcategory_id" value="'+ obj.test_sub_category_id+'"><input type="hidden" name="subject_id" value="'+ obj.test_subject_id +'"></form>';
                          ulDivInnerHtml += '<button class="btn-magick btn-sm btn3d" onClick="showUserTestResult(this);" data-paper_id="'+obj.id+'"  title="Result!"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
                          ulDivInnerHtml += '</li>';
                      } else {
                        ulDivInnerHtml += '<li id="showUserResultMobileBtn_'+obj.id+'">';
                        ulDivInnerHtml += '<button class="btn-magick btn-sm btn3d" disabled="true"  title="Result will display after test given"><span class="fa fa-bar-chart" aria-hidden="true"></span>Result</button>';
                        ulDivInnerHtml += '</li>';
                      }
                      ulDivInnerHtml += '<li class=" "><button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-calendar"></span>'+ obj.date_to_active +'</button></li>';

                      ulDivInnerHtml += '<li class=""><button type="button" class="btn-magick btn-sm btn3d" disabled="true"><span class="fa fa-inr"></span>'+ obj.price +'</button></li>';

                      if(msg['currentDate'] < obj.date_to_active){
                        if(obj.price > 1){
                          ulDivInnerHtml += '<li><button class="btn-magick btn-sm btn3d" disabled="true" title="Purchase test will be enabled on date to active">Pay Now</button></li>';
                        } else {
                          ulDivInnerHtml += '<li><button class="btn-magick btn-sm btn3d" disabled="true" title="Purchase test will be enabled on date to active">Free</button></li>';
                        }
                      } else {
                        if(true == (msg['registeredPaperIds'][obj.id] > 0) || true == (msg['purchasedSubCategories'][obj.test_sub_category_id] > 0) || 0 == obj.price ){
                          if(obj.price > 1){
                            ulDivInnerHtml += '<li><button class="btn-magick btn-sm btn3d" disabled="true">Paid</button></li>';
                          } else {
                            ulDivInnerHtml += '<li><button class="btn-magick btn-sm btn3d" disabled="true">Free</button></li>';
                          }
                        } else {
                          if(obj.price > 1){
                            var purchaseTestUrl = "{{ url('purchaseTest') }}";
                            var csrf_token = '{{ csrf_field() }}';
                            ulDivInnerHtml += '<li data-paper="'+obj.id+'" onClick="purchaseTest(this);"><button class="btn-magick btn-sm btn3d"  title="Purchase Test">Pay Now</button></li><form id="purchaseTest_'+obj.id+'" method="POST" action="'+purchaseTestUrl+'">'+csrf_token+'<input type="hidden" name="paper_id" value="'+obj.id+'"><input type="hidden" name="category_id" value="'+ obj.test_category_id +'"><input type="hidden" name="subcategory_id" value="'+ obj.test_sub_category_id +'"><input type="hidden" name="subject_id" value="'+ obj.test_subject_id +'"></form>';
                          } else {
                            ulDivInnerHtml += '<li><button disabled="true" class="btn-magick btn-sm btn3d" >Free</button></li>';
                          }
                        }
                      }

                      ulDiv.innerHTML = ulDivInnerHtml;
                      rowDiv.appendChild(ulDiv);
                      containerDiv.appendChild(rowDiv);
                      panelContentDiv.appendChild(containerDiv);
                      panelDiv.appendChild(panelContentDiv);
                      mainSmallDiv.appendChild(panelDiv);
                    });
                  }
                  tableDiv.appendChild(mainSmallDiv);
                  secondMainDiv.appendChild(tableDiv);
                  defaultPanelDiv.appendChild(secondMainDiv);
                  mainPanelDiv.appendChild(defaultPanelDiv);
                  divEle.appendChild(mainPanelDiv);
                });
                if(true == msg['isSubCategoryPurchased']){
                  document.getElementById('paidStatus').text = 'Paid';
                  document.getElementById('paidStatus').removeAttribute('onClick');
                  document.getElementById('pay-now-form').innerHTML = '';
                }
              } else {
                  var mainPanelDiv = document.createElement('div');
                  mainPanelDiv.className = "panel-group";
                  mainPanelDiv.setAttribute('role','tablist');
                  mainPanelDiv.setAttribute('aria-multiselectable','true');
                  mainPanelDiv.id = 'headingOne';

                  var defaultPanelDiv = document.createElement('div');
                  defaultPanelDiv.className = "panel panel-default";

                  var firstMainDiv = document.createElement('div');
                  firstMainDiv.className = "panel-heading";
                  firstMainDiv.setAttribute('role','tab');
                  firstMainDiv.id = 'headingOne'

                  var h4Ele = document.createElement('h4');
                  h4Ele.className = 'panel-title';
                  h4Ele.innerHTML = '<a role="button" data-toggle="collapse" data-parent="#accordion" aria-expanded="true" aria-controls="collapseOne"><i class="more-less glyphicon glyphicon-plus"></i>No subjects are available.</a>';
                  firstMainDiv.appendChild(h4Ele);
                  defaultPanelDiv.appendChild(firstMainDiv);
                  mainPanelDiv.appendChild(defaultPanelDiv);
                  divEle.appendChild(mainPanelDiv);
              }
            $('[id^=paper]').hide();
            $('.toggle').click(function() {
              $input = $( this );
              $target = $('#'+$input.attr('data-toggle'));
              $target.slideToggle();
              if($input.find('.col-xs-2 i').attr('class')=="fa fa-chevron-down pull-right")
              {
               $input.find('.col-xs-2 i').removeClass('fa-chevron-down');
               $input.find('.col-xs-2 i').addClass('fa-chevron-up');
             }
             else if($input.find('.col-xs-2 i').attr('class')=="fa pull-right fa-chevron-up")
             {
               $input.find('.col-xs-2 i').removeClass('fa-chevron-up');
               $input.find('.col-xs-2 i').addClass('fa-chevron-down');
             }
             else if($input.find('.col-xs-2 i').attr('class')=="fa pull-right fa-chevron-down")
             {
               $input.find('.col-xs-2 i').removeClass('fa-chevron-down');
               $input.find('.col-xs-2 i').addClass('fa-chevron-up');
             }
           });
        });
      } else {
          divEle = document.getElementById('subjects');
          divEle.innerHTML = '';

          var mainPanelDiv = document.createElement('div');
          mainPanelDiv.className = "panel-group";
          mainPanelDiv.setAttribute('role','tablist');
          mainPanelDiv.setAttribute('aria-multiselectable','true');
          mainPanelDiv.id = 'headingOne';

          var defaultPanelDiv = document.createElement('div');
          defaultPanelDiv.className = "panel panel-default";

          var firstMainDiv = document.createElement('div');
          firstMainDiv.className = "panel-heading";
          firstMainDiv.setAttribute('role','tab');
          firstMainDiv.id = 'headingOne'

          var h4Ele = document.createElement('h4');
          h4Ele.className = 'panel-title';
          h4Ele.innerHTML = '<a role="button" data-toggle="collapse" data-parent="#accordion" aria-expanded="true" aria-controls="collapseOne"><i class="more-less glyphicon glyphicon-plus"></i>No subjects are available.</a>';
          firstMainDiv.appendChild(h4Ele);
          defaultPanelDiv.appendChild(firstMainDiv);
          mainPanelDiv.appendChild(defaultPanelDiv);
          divEle.appendChild(mainPanelDiv);
      }
    }

    function purchaseTest(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'Do you want to purchase this test?',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                var paperId = parseInt($(ele).data('paper'));
                document.getElementById('purchaseTest_'+paperId).submit();
            }
              },
              Cancel: function () {
              }
          }
      });
    }

    function purchaseMobileTest(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'Do you want to purchase this test?',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                var paperId = parseInt($(ele).data('paper'));
                document.getElementById('purchaseTest_mobile_'+paperId).submit();
            }
              },
              Cancel: function () {
              }
          }
      });
    }

    function purchaseSubCategory(ele){
        $.confirm({
          title: 'Confirmation',
          content: 'Do you want to purchase this sub category?',
          type: 'red',
          typeAnimated: true,
          buttons: {
            Ok: {
              text: 'Ok',
              btnClass: 'btn-red',
              action: function(){
                document.getElementById('purchaseSubCategory').submit();
              }
            },
            Cancel: function () {
            }
          }
        });
    }
  </script>
<script >
  function toggleIcon(e) {
      $(e.target)
      .prev('.panel-heading')
      .find(".more-less")
      .toggleClass('glyphicon-plus glyphicon-minus');
  }
  $('.panel-group').on('hidden.bs.collapse', toggleIcon);
  $('.panel-group').on('shown.bs.collapse', toggleIcon);

  $(document).ready(function() {
    $('[id^=paper]').hide();
    $('.toggle').click(function() {
      $input = $( this );
      $target = $('#'+$input.attr('data-toggle'));
      $target.slideToggle();
      if($input.find('.col-xs-2 i').attr('class')=="fa fa-chevron-down pull-right")
      {
       $input.find('.col-xs-2 i').removeClass('fa-chevron-down');
       $input.find('.col-xs-2 i').addClass('fa-chevron-up');
     }
     else if($input.find('.col-xs-2 i').attr('class')=="fa pull-right fa-chevron-up")
     {
       $input.find('.col-xs-2 i').removeClass('fa-chevron-up');
       $input.find('.col-xs-2 i').addClass('fa-chevron-down');
     }
     else if($input.find('.col-xs-2 i').attr('class')=="fa pull-right fa-chevron-down")
     {
       $input.find('.col-xs-2 i').removeClass('fa-chevron-down');
       $input.find('.col-xs-2 i').addClass('fa-chevron-up');
     }

   });
  });
</script>
@stop