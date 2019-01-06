@extends('admin.master')
@section('admin_content')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Manage Post </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-file-pdf-o"></i> Study Material </li>
      <li class="active"> Manage Post </li>
    </ol>
  </section>
@stop
  <div class="container ">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    @php
    if(Session::has('selected_post_category')){
      $selectedCategoryId = Session::get('selected_post_category');
    } else {
      $selectedCategoryId = 0;
    }
    if(Session::has('selected_post_subcategory')){
      $selectedSubCategoryId = Session::get('selected_post_subcategory');
    } else {
      $selectedSubCategoryId = 0;
    }
    if(Session::has('selected_post_subject')){
      $selectedSubjectId = Session::get('selected_post_subject');
    } else {
      $selectedSubjectId = 0;
    }
    if(Session::has('selected_post_topic')){
      $selectedTopicId = Session::get('selected_post_topic');
    } else {
      $selectedTopicId = 0;
    }
  @endphp
  <div>
  <div  class="admin_div">
    <form id="questionForm" action="{{url('admin/showPosts')}}" method="POST">
      {{csrf_field()}}
      <div class="form-group row ">
          <label class="col-sm-2 col-form-label">Category Name:</label>
          <div class="col-sm-3">
            <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
              <option value="0">Select Category</option>
              @if(count($courseCategories) > 0)
                @foreach($courseCategories as $courseCategory)
                  @if($selectedCategoryId == $courseCategory->id)
                    <option value="{{$courseCategory->id}}" selected>{{$courseCategory->name}}</option>
                  @else
                    <option value="{{$courseCategory->id}}">{{$courseCategory->name}}</option>
                  @endif
                @endforeach
              @endif
            </select>
          </div>
        </div>
        <div class="form-group row ">
          <label class="col-sm-2 col-form-label">Sub Category Name:</label>
          <div class="col-sm-3">
            <select id="subcategory" class="form-control" name="subcategory" required title="Sub Category" onChange="selectSubject(this);">
              <option value="0">Select Sub Category</option>
              @if(count($courseSubCategories) > 0)
                @foreach($courseSubCategories as $courseSubCategory)
                  @if($selectedSubCategoryId == $courseSubCategory->id)
                    <option value="{{$courseSubCategory->id}}" selected>{{$courseSubCategory->name}}</option>
                  @else
                    <option value="{{$courseSubCategory->id}}" >{{$courseSubCategory->name}}</option>
                  @endif
                @endforeach
              @endif
            </select>
          </div>
        </div>
      <div class="form-group row ">
        <label class="col-sm-2 col-form-label">Subject Name:</label>
          <div class="col-sm-3">
              <select id="subject" class="form-control" name="subject" required title="Subject" onChange="selectTopic(this);">
                <option value="0">Select Sub Category</option>
                @if(count($courseSubCategories) > 0)
                  @foreach($courseSubCategories as $courseSubCategory)
                    @if($selectedSubCategoryId == $courseSubCategory->id)
                      <option value="{{$courseSubCategory->id}}" selected>{{$courseSubCategory->name}}</option>
                    @else
                      <option value="{{$courseSubCategory->id}}" >{{$courseSubCategory->name}}</option>
                    @endif
                  @endforeach
                @endif
              </select>
          </div>
        </div>
        <div class="form-group row ">
          <label class="col-sm-2 col-form-label">Topic:</label>
          <div class="col-sm-3">
            <select id="topic" class="form-control" name="topic" required title="Topic">
              <option value="0">Select Topic</option>
              @if(count($topics) > 0)
                @foreach($topics as $topic)
                  @if($selectedTopicId == $topic->id)
                    <option value="{{$topic->id}}" selected>{{$topic->name}}</option>
                  @else
                    <option value="{{$topic->id}}" >{{$topic->name}}</option>
                  @endif
                @endforeach
              @endif
            </select>
          </div>
        </div>
        <div class="form-group row">
          <div class="offset-sm-2 col-sm-10" title="Submit">
            <button id="submitButton" type="submit" class="btn btn-primary">Submit</button>
          </div>
        </div>
    </form>
  </div>
  <div class="form-group row">
      <div >
        <a href="{{url('admin/createStudyMaterialPost')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Post"> Add New Post</a>&nbsp;&nbsp;&nbsp;&nbsp;
      </div>
    </div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Post</th>
          <th>Category </th>
          <th>Sub Category </th>
          <th>Subject </th>
          <th>Topic</th>
          <th>Created_by</th>
          <th>Edit </th>
          <th>Delete </th>
        </tr>
      </thead>
      <tbody>
        @if(count($posts) > 0)
          @foreach($posts as $index => $post)
          <tr style="overflow: auto;">
            <th scope="row">{{$index + 1}}</th>
            <td>{!! mb_strimwidth($post->body, 0, 400, "...") !!}</td>
            <td>{{$post->category}}</td>
            <td>{{$post->subcategory}}</td>
            <td>{{$post->subject}}</td>
            <td>{{$post->topic}}</td>
            <td>{{$adminNames[$post->admin_id]}}</td>
            <td>
              <a href="{{url('admin/studyMaterialPost')}}/{{$post->id}}/edit"
                    ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$post->name}}" />
                </a>
            </td>
            <td>
                <a id="{{$post->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$post->name}}" />
                </a>
                <form id="deleteStudyMaterialPost_{{$post->id}}" action="{{url('admin/deleteStudyMaterialPost')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="post_id" value="{{$post->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
            <tr><td colspan="6">No Post is created.</td></tr>
        @endif
      </tbody>
    </table>
  </div>
  </div>
<script type="text/javascript">
  function confirmDelete(ele){
     $.confirm({
      title: 'Confirmation',
      content: 'Are you sure, you want to delete this post.',
      type: 'red',
      typeAnimated: true,
      buttons: {
            Ok: {
                text: 'Ok',
                btnClass: 'btn-red',
                action: function(){
                  var id = $(ele).attr('id');
                  formId = 'deleteStudyMaterialPost_'+id;
                  document.getElementById(formId).submit();
                }
            },
            Cancel: function () {
            }
        }
      });
  }
  function selectSubcategory(ele){
    var id = parseInt($(ele).val());
    if(id > 0){
      $.ajax({
              method: "POST",
              url: "{{url('admin/getCourseSubCategories')}}",
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

  function selectSubject(ele){
    var category = document.getElementById('category').value;
    var subcategory = parseInt($(ele).val());
    if(category > 0 && subcategory > 0){
      $.ajax({
              method: "POST",
              url: "{{url('admin/getStudyMaterialSubjectsByCategoryIdBySubCategoryId')}}",
              data: {category:category,subcategory:subcategory}
          })
          .done(function( msg ) {
            select = document.getElementById('subject');
            select.innerHTML = '';
            var opt = document.createElement('option');
            opt.value = '0';
            opt.innerHTML = 'Select Subject';
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

  function selectTopic(ele){
    var category = document.getElementById('category').value;
    var subcategory = document.getElementById('subcategory').value;
    var subject = parseInt($(ele).val());
    if(category > 0 && subcategory > 0 && subject > 0){
      $.ajax({
              method: "POST",
              url: "{{url('admin/getStudyMaterialTopicsByCategoryIdBySubCategoryIdBySubjectId')}}",
              data: {category:category,subcategory:subcategory,subject:subject}
          })
          .done(function( msg ) {
            select = document.getElementById('topic');
            select.innerHTML = '';
            var opt = document.createElement('option');
            opt.value = '0';
            opt.innerHTML = 'Select Topic';
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