@extends('clientuser.dashboard.dashboard')
@section('dashboard_header')
  <link href="{{asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
@stop
@section('module_title')
  <section class="content-header">
    <h1> Course Results </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Courses</li>
      <li class="active">Course Results </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
	<div class="content-wrapper v-container tab-content" >
    <div class="">
      <div class="container">
        <div class="row">
          <div class="mrgn_20_btm">
              <div class="col-sm-4 mrgn_10_btm">
                <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" title="Category">
                  <option value="">Select Category ...</option>
                  @if(count($categories) > 0)
                    @foreach($categories as $category)
                      <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                  @endif
                </select>
              </div>
              <div class="col-sm-4">
                <select id="subcategory" class="form-control" name="subcategory" onChange="showResult(this);" title="Sub Category">
                  <option value="">Select Sub Category ...</option>
                </select>
              </div>
          </div>
          <div class="col-lg-12" id="all-result">
            <div class="panel panel-info">
              <div class="panel-heading text-center">
                Courses
              </div>
              <div class="panel-body">
                <table  class="" id="dataTables-example">
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Courses</th>
                      <th>Grade</th>
                      <th>Certificate</th>
                    </tr>
                  </thead>
                  <tbody  id="course-result">
                  @if(count($courses) > 0)
                    @foreach($courses as $index => $course)
                      <tr class="">
                        <td>{{ $index + 1 }}</td>
                        <td>{{$course->name}}</td>
                        @if(!empty($course->grade))
                          <td>{{$course->grade}}</td>
                        @else
                          <td>Certificate exam is not given.</td>
                        @endif
                        <td class="center">Certified</td>
                      </tr>
                    @endforeach
                  @else
                    <tr class="">
                      <td colspan="5">No Courses are registered.</td>
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
    var subcatId = parseInt(document.getElementById('subcategory').value);
    var catId = parseInt(document.getElementById('category').value);
    var userId = parseInt(document.getElementById('user_id').value);
    $.ajax({
        method: "POST",
        url: "{{url('getCourseByCatIdBySubCatIdByUserId')}}",
        data: {catId:catId, subcatId:subcatId, userId:userId}
    })
    .done(function( msg ) {
      body = document.getElementById('course-result');
      body.innerHTML = '';
      if( 0 < msg['courses'].length){
        $.each(msg['courses'], function(idx, obj) {
            var eleTr = document.createElement('tr');
            var eleIndex = document.createElement('td');
            eleIndex.innerHTML = idx + 1;
            eleTr.appendChild(eleIndex);

            var eleName = document.createElement('td');
            eleName.innerHTML = obj.name;
            eleTr.appendChild(eleName);

            var eleGrade = document.createElement('td');
            if(obj.grade){
              eleGrade.innerHTML = obj.grade;
            } else {
              eleGrade.innerHTML = 'Certificate exam is not given.';
            }
            eleTr.appendChild(eleGrade);

            var eleCertified = document.createElement('td');
            if(1 == obj.certified){
              eleCertified.innerHTML = 'Certified';
            } else {
              eleCertified.innerHTML = 'Non-Certified';
            }
            eleTr.appendChild(eleCertified);
            body.appendChild(eleTr);
        });
      } else {
        var eleTr = document.createElement('tr');
        var eleIndex = document.createElement('td');
        eleIndex.innerHTML = 'No Courses are registered.';
        eleIndex.setAttribute('colspan', 4);
        eleTr.appendChild(eleIndex);
        body.appendChild(eleTr);
      }
    });
  }

  function selectSubcategory(ele){
    var id = parseInt($(ele).val());
    var userId = parseInt(document.getElementById('user_id').value);
    document.getElementById('course-result').innerHTML = '';

    if( 0 < id && 0 < userId){
      $.ajax({
          method: "POST",
          url: "{{url('getOnlineSubCategories')}}",
          data: {id:id, userId:userId}
      })
      .done(function( msg ) {
        select = document.getElementById('subcategory');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Sub Category ...';
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