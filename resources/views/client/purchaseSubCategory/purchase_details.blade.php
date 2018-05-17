@extends('client.dashboard')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Purchased Sub Category  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-shopping-cart"></i> Market Place </li>
      <li class="active"> Purchased Sub Category </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
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
     @media screen and (max-width: 527px) {
     .col-md-6{
            width: 100%;.
            float: left;
             margin-left: 0px;
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
    .pay-now span#price{
      color: #fff;
      font-weight: bold;
    }
    .black{
      color: black;
    }
  </style>
  <section class="v_container">
    <div class="container">
      <h2 class="v_h2_title text-center"> Exam</h2>
      <hr class="section-dash-dark"/>
      <div class="row label-primary">
        <div class="col-md-8  col-md-offset-2  ">
          <div class="row text-center">
            <div class="col-md-4 col-sm-4  col-xs-12 mrgn_10_top_btm">
              <label>Sub Category: </label><label>{{$selectedSubCategory->name}}</label>
            </div>
            <div class="col-md-8 col-sm-8 mrgn_10_top_btm " style="display: inline-block !important; ">
              <div class="pay-now " >
                <span id="price">Price: {{$selectedSubCategory->price}} Rs/year &nbsp; OR &nbsp;
                Price: {{$selectedSubCategory->monthly_price}} Rs/month</span>
                <a class="btn btn-default" title="Pay Now" style="min-width: 100px;">Paid </a>
              </div>
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
        @foreach($testSubjects as $index => $testSubject)
          <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" >
              <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingOne">
                  <h4 class="panel-title" title="{{ $testSubject->name }}">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#subject{{$testSubject->id}}" aria-expanded="true" aria-controls="collapseOne" class="">
                      <i class="more-less glyphicon glyphicon-minus"></i>
                      {{ $testSubject->name }}
                    </a>
                  </h4>
                </div>
                  <div id="subject{{$testSubject->id}}" class="panel-collapse panel-lg collapse in" role="tabpanel" aria-labelledby="headingOne">
                  <div class="panel-body">
                    <table class="table data-lg">
                        <thead>
                          <tr>
                              <th>Test Name</th>
                              <th>Question Count</th>
                          </tr>
                        </thead>
                        <tbody>
                          @if(isset($testPapers[$testSubject->id]))
                            @foreach($testPapers[$testSubject->id] as $index => $testSubjectPaper)
                              <tr>
                                <td class=" ">{{ $testSubjectPaper->name }}</td>
                                <td class=" ">{{ $paperQuestionCount[$testSubjectPaper->id] }}</td>
                              </tr>
                            @endforeach
                          @else
                            <tr><td class=" ">No Papers.</td></tr>
                          @endif
                        </tbody>
                    </table>
                    <div class="data-sm">
                        @if(isset($testPapers[$testSubject->id]))
                        @foreach($testPapers[$testSubject->id] as $testSubjectPaper)
                              <div class=" panel panel-info" >
                                <div class="toggle panel-heading" data-toggle="paper{{$testSubjectPaper->id}}">{{$testSubjectPaper->name}}<span class="col-xs-2 pull-right"><i class="fa fa-chevron-down pull-right"></i></span>
                                </div>
                                  <div id="paper{{$testSubjectPaper->id}}" class="panel-body" style="padding:2px 0px;">
                                    <div class="container">
                                        <div class="fluid-row">
                                          <ul class="">
                                            <li id="startTest_mobile_{{$testSubjectPaper->id}}"><button class="btn-magick btn-sm btn3d" disabled="true" data-toggle="tooltip" title="Question Count">Question Count:{{ $paperQuestionCount[$testSubjectPaper->id] }} </button></li>
                                          </ul>
                                        </div>
                                    </div>
                                  </div>
                              </div>
                            @endforeach
                          @else
                        No Papers.
                      @endif
                      </div>
                  </div>
                </div>
              </div>
          </div>
        @endforeach
      @else
        No subjects are available.
      @endif
    </div>
  </section>
  <script type="text/javascript">
    function showTotal(ele){
      var duration = $(ele).val();
      var subcat = $(ele).data('id');
      var price = $('input[name="plan_'+subcat+'"]:checked').val();
      document.getElementById('total_'+subcat).value = price * duration;
    }
    function changeTotal(ele){
      var price = $(ele).val();
      var subcat = $(ele).data('id');
      var duration = document.getElementById('duration_'+subcat).value;
      document.getElementById('total_'+subcat).value = price * duration;
    }
  </script>
  <script type="text/javascript">
    function toggleIcon(e) {
      $(e.target)
      .prev('.panel-heading')
      .find(".more-less")
      .toggleClass('glyphicon-plus glyphicon-minus');
    }
    $('.panel-group').on('hidden.bs.collapse', toggleIcon);
    $('.panel-group').on('shown.bs.collapse', toggleIcon);

  </script>
@stop