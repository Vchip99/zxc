@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> All Test Results </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> All Test Results </li>
      <li class="active"> All Test Results </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="top mrgn_40_btm">
        <div class="container">
          <div class="row">
            <div class="col-md-3 mrgn_10_btm">
              <select class="form-control" id="college" name="college" onChange="allResults(this.value);">
                <option value="0"> Select College </option>
                @if(2 == Auth::user()->user_type && 'other' == Auth::user()->college_id)
                  <option value="other" selected="true">Other</option>
                @else
                  @if(count($colleges) > 0)
                    @foreach($colleges as $college)
                      @if(Auth::user()->college_id == $college->id)
                        <option value="{{$college->id}}"  selected="true">{{$college->name}}</option>
                      @endif
                    @endforeach
                  @endif
                @endif
              </select>
            </div>
            <div class="col-md-3 mrgn_10_btm">
             <select class="form-control" id="category" id="category" name="category" title="Category" onChange="selectSubcategory(this);">
              <option value="0">Select Category</option>
              @if(count($categories) > 0)
                @foreach($categories as $category)
                  <option value="{{$category->id}}">{{$category->name}}</option>
                @endforeach
              @endif
             </select>
            </div>
            <div class="col-md-3 mrgn_10_btm">
             <select class="form-control" id="subcategory" name="subcategory" title="Sub Category" onChange="selectSubject(this);">
              <option value="0">Select Sub Category</option>
             </select>
            </div>
            <div class="col-md-3 mrgn_10_btm">
             <select class="form-control" id="subject" name="subject" title="Subject" onChange="selectPaper(this);">
              <option value="0">Select Subject</option>
             </select>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-md-3 mrgn_10_btm">
            <select class="form-control" id="paper" name="paper" title="Paper" onChange="showResult();">
              <option value="0">Select Paper</option>
            </select>
          </div>
          <div class="col-lg-12" id="all-result">
            <div class="panel panel-info">
              <div class="panel-heading text-center">
                Test Results
              </div>
              <div class="panel-body">
                <table  class="" id="all_test_result">
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Name</th>
                      <th>College</th>
                      <th>Department</th>
                      <th>Marks</th>
                      <th>Rank</th>
                    </tr>
                  </thead>
                  <tbody id="students" class="">
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript">
  function allResults(college){
    document.getElementById('students').innerHTML = '';
  }
  function showResult(){
    var college = document.getElementById('college').value;
    var category = document.getElementById('category').value;
    var subcategory = document.getElementById('subcategory').value;
    var subject = document.getElementById('subject').value;
    var paper = document.getElementById('paper').value;
    $.ajax({
      method: "POST",
      url: "{{url('getAllTestResults')}}",
      data:{category:category,subcategory:subcategory,college:college,subject:subject,paper:paper}
    })
    .done(function( msg ) {
      body = document.getElementById('students');
      body.innerHTML = '';
      if( 0 < msg['scores'].length){
        $.each(msg['scores'], function(idx, obj) {
            var eleTr = document.createElement('tr');
            var eleIndex = document.createElement('td');
            eleIndex.innerHTML = idx + 1;
            eleTr.appendChild(eleIndex);

            var eleName = document.createElement('td');
            eleName.innerHTML = obj.user.name;
            eleTr.appendChild(eleName);

            var eleCollege = document.createElement('td');
            eleCollege.innerHTML = msg['colleges'][obj.id];
            eleTr.appendChild(eleCollege);

            var eleDepartment = document.createElement('td');
            eleDepartment.innerHTML = msg['departments'][obj.id];
            eleTr.appendChild(eleDepartment);

            var eleScore = document.createElement('td');
            eleScore.innerHTML = obj.test_score+'/'+msg['marks'][obj.id]['totalMarks'];
            eleTr.appendChild(eleScore);

            var eleRank = document.createElement('td');
            eleRank.innerHTML = msg['ranks'][obj.id];
            eleTr.appendChild(eleRank);
            body.appendChild(eleTr);
        });
      } else {
        var eleTr = document.createElement('tr');
        var eleIndex = document.createElement('td');
        eleIndex.innerHTML = 'No result!';
        eleIndex.setAttribute('colspan', '6');
        eleTr.appendChild(eleIndex);
        body.appendChild(eleTr);
      }
    });
  }

  function selectSubcategory(ele){
    document.getElementById('subject').value = 0;
    document.getElementById('paper').value = 0;
    document.getElementById('students').innerHTML = '';
    id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
              method: "POST",
              url: "{{url('getSubCategories')}}",
              data: {id:id}
          })
          .done(function( msg ) {
            select = document.getElementById('subcategory');
            select.innerHTML = '';
            var opt = document.createElement('option');
            opt.value = 0;
            opt.innerHTML = 'Select Sub Category';
            select.appendChild(opt);
            if( 0 < msg.length){
              $.each(msg, function(idx, obj) {
                  var opt = document.createElement('option');
                  opt.value = obj.id;
                  opt.innerHTML = obj.name;
                  select.appendChild(opt);
              });
            }
          });
    }
  }

  function selectSubject(ele){
    document.getElementById('paper').value = 0;
    document.getElementById('students').innerHTML = '';
    subcatId = parseInt($(ele).val());
    catId = parseInt(document.getElementById('category').value);
    if( 0 < catId && 0 < subcatId ){
      $.ajax({
              method: "POST",
              url: "{{url('getSubjectsByCatIdBySubcatId')}}",
              data: {catId:catId, subcatId:subcatId}
          })
          .done(function( msg ) {
            selectSub = document.getElementById('subject');
            selectSub.innerHTML = '';
            var opt = document.createElement('option');
            opt.value = 0;
            opt.innerHTML = 'Select Subject';
            selectSub.appendChild(opt);
            if( 0 < msg.length){
              $.each(msg, function(idx, obj) {
                  var opt = document.createElement('option');
                  opt.value = obj.id;
                  opt.innerHTML = obj.name;
                  selectSub.appendChild(opt);
              });
            }
        });
    }
  }

  function selectPaper(ele){
    subjectId = parseInt($(ele).val());
    document.getElementById('students').innerHTML = '';
    if( 0 < subjectId ){
      $.ajax({
          method: "POST",
            url: "{{url('getPapersBySubjectId')}}",
            data: {subjectId:subjectId}
      }).done(function( msg ) {
        select = document.getElementById('paper');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = 0;
        opt.innerHTML = 'Select Paper';
        select.appendChild(opt);
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              select.appendChild(opt);
          });
        }
      });
    }
  }
</script>
@stop