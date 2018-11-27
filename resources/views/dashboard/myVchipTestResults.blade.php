@extends('dashboard.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Test Results </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Students Dashboard</li>
      <li class="active">Test Results </li>
    </ol>
  </section>
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
@stop
@section('dashboard_content')
	<div class="content-wrapper v-container tab-content" >
    <div class="">
      <div class="container">
      <div class="row">
          <a href="{{ url('college/'.Session::get('college_user_url').'/myVchipTestResults')}}" class="btn btn-primary" style="width: 140px;"> Vchip Test Results</a>&nbsp;<a href="{{ url('college/'.Session::get('college_user_url').'/myCollegeTestResults')}}" class="btn btn-default"> College Test Results</a>
        </div>
        <br>
        <div class="row">
          <div class="mrgn_20_btm">
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
              <div class="col-md-3 ">
               <select class="form-control" id="subcategory" name="subcategory" title="Sub Category" onChange="showResult(this);">
                <option value="0">Select Sub Category</option>
               </select>
              </div>
          </div>
          <div class="col-lg-12" id="all-result">
            <div class="panel panel-info">
              <div class="panel-heading text-center">
               RESULT
              </div>
              <div class="panel-body">
                <table  class="" id="dataTables-example">
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Subject</th>
                      <th>Paper</th>
                      <th>Marks</th>
                      <th>Rank</th>
                    </tr>
                  </thead>
                  <tbody  id="test-result">
                    @if(count($results) > 0)
                      @foreach($results as $index => $result)
                        <tr class="" style="overflow: auto;">
                          <td>{{$index + 1}}</td>
                          <td>{{$result->subject}}</td>
                          <td>{{$result->paper}}</td>
                          <td class="center">{{$result->test_score}} / {{$result->totalMarks()['totalMarks']}}</td>
                          <td class="center">{{$result->rank(Auth::user()->college_id)}}</td>
                        </tr>
                      @endforeach
                    @elseif(0 == count($results))
                      <tr class="">
                        <td colspan="5">No result.</td>
                      </tr>
                    @endif
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

  function showResult(ele){
    var subcategory = parseInt(document.getElementById('subcategory').value);
    var category = parseInt(document.getElementById('category').value);
    var student = parseInt(document.getElementById('user_id').value);
    $.ajax({
            method: "POST",
            url: "{{url('showUserTestResultsByCatBySubCat')}}",
            data: {category:category,subcategory:subcategory,user:student}
        })
        .done(function( msg ) {
          body = document.getElementById('test-result');
          body.innerHTML = '';
          if( 0 < msg['scores'].length){
            $.each(msg['scores'], function(idx, obj) {
                var eleTr = document.createElement('tr');
                var eleIndex = document.createElement('td');
                eleIndex.innerHTML = idx + 1;
                eleTr.appendChild(eleIndex);

                var eleSubject = document.createElement('td');
                eleSubject.innerHTML = obj.subject;
                eleTr.appendChild(eleSubject);

                var elePaper = document.createElement('td');
                elePaper.innerHTML = obj.paper;
                eleTr.appendChild(elePaper);

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
            eleIndex.setAttribute('colspan', '5');
            eleTr.appendChild(eleIndex);
            body.appendChild(eleTr);
          }
    });
  }

  function selectSubcategory(ele){
    document.getElementById('subcategory').value = 0;
    document.getElementById('test-result').innerHTML = '';
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
          opt.value = '0';
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

</script>
@stop