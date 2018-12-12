@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Topic </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-file-pdf-o"></i> Study Material </li>
      <li class="active"> Manage Topic </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  <div class="container admin_div">
  @if(isset($topic->id))
    <form action="{{url('admin/updateStudyMaterialTopic')}}" method="POST" enctype="multipart/form-data" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" id="topic_id" name="topic_id" value="{{$topic->id}}">
  @else
    <form action="{{url('admin/createStudyMaterialTopic')}}" method="POST" enctype="multipart/form-data" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('category')) has-error @endif">
      <label class="col-sm-2 col-form-label">Category Name:</label>
      <div class="col-sm-3">
      @if(isset($topic->id))
        @if(count($courseCategories) > 0)
          @foreach($courseCategories as $courseCategory)
            @if( isset($topic->id) && $topic->course_category_id == $courseCategory->id)
              <input type="text" class="form-control" name="category_text" value="{{$courseCategory->name}}" readonly>
              <input type="hidden" name="category" id="category" value="{{$courseCategory->id}}">
            @endif
          @endforeach
        @endif
      @else
        <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
            <option value="0">Select Category</option>
            @if(count($courseCategories) > 0)
              @foreach($courseCategories as $courseCategory)
                <option value="{{$courseCategory->id}}">{{$courseCategory->name}}</option>
              @endforeach
            @endif
        </select>
      @endif
        @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
      <label class="col-sm-2 col-form-label">Sub Category Name:</label>
      <div class="col-sm-3">
        @if(isset($topic->id) && count($courseSubCategories) > 0)
            @foreach($courseSubCategories as $courseSubCategory)
              @if($topic->course_sub_category_id == $courseSubCategory->id)
                <input type="text" class="form-control" name="subcategory_text" value="{{$courseSubCategory->name}}" readonly>
                <input type="hidden" name="subcategory" id="subcategory" value="{{$courseSubCategory->id}}">
              @endif
            @endforeach
        @else
          <select id="subcategory" class="form-control" name="subcategory" required title="Sub Category" onChange="selectSubject(this);">
            <option value="0">Select Sub Category</option>
          </select>
        @endif
        @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('subject')) has-error @endif">
      <label class="col-sm-2 col-form-label">Subject Name:</label>
      <div class="col-sm-3">
        @if(isset($topic->id) && count($subjects) > 0)
            @foreach($subjects as $subject)
              @if($topic->study_material_subject_id == $subject->id)
                <input type="text" class="form-control" name="subject_text" value="{{$subject->name}}" readonly>
                <input type="hidden" name="subject" id="subject" value="{{$subject->id}}">
              @endif
            @endforeach
        @else
          <select id="subject" class="form-control" name="subject" required title="Subject">
            <option value="0">Select Subject</option>
          </select>
        @endif
        @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('topic')) has-error @endif">
      <label for="topic" class="col-sm-2 col-form-label">Topic Name:</label>
      <div class="col-sm-3">
        @if(isset($topic->id))
          <input type="text" class="form-control" name="topic" id="topic" value="{{$topic->name}}" required="true">
        @else
          <input type="text" class="form-control" name="topic" id="topic" value="" placeholder="Topic Name" required="true">
        @endif
        @if($errors->has('topic')) <p class="help-block">{{ $errors->first('topic') }}</p> @endif
        <span class="hide" id="topicError" style="color: white;">Given name is already exist with selected category and subcategory and subject.Please enter another name.</span>
      </div>
    </div>
    <div class="form-group row" id="show_common_data">
      <label class="col-sm-2 col-form-label">Content:</label>
      <div class="col-sm-10">
          <textarea name="content" cols="60" rows="4" id="content" placeholder="Enter content" required>
          @if(isset($topic->id))
            {!! $topic->content !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'content', { enterMode: CKEDITOR.ENTER_BR } );
          CKEDITOR.on('dialogDefinition', function (ev) {
                  var dialogName = ev.data.name,
                  dialogDefinition = ev.data.definition;
                  if (dialogName == 'image') {
                      var onOk = dialogDefinition.onOk;
                      dialogDefinition.onOk = function (e) {
                          var width = this.getContentElement('info', 'txtWidth');
                          width.setValue('100%');//Set Default Width
                          var height = this.getContentElement('info', 'txtHeight');
                          height.setValue('400');////Set Default height
                          onOk && onOk.apply(this, e);
                    };
                  }
              });
        </script>
      </div>
    </div>
    <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          @if(!empty($topic->id) && $topicSubject->admin_id == Auth::guard('admin')->user()->id)
            <button type="button" class="btn btn-primary" onclick="searchTopic();">Submit</button>
          @elseif(empty($topic->id) )
            <button type="button" class="btn btn-primary" onclick="searchTopic();">Submit</button>
          @else
            <a href="{{ url('admin/manageStudyMaterialTopic') }}" class="btn btn-primary">Back</a>
          @endif
        </div>
      </div>
  </form>
  </div>

<script type="text/javascript">

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

  function searchTopic(){
    var category = document.getElementById('category').value;
    var subcategory = document.getElementById('subcategory').value;
    var subject = document.getElementById('subject').value;
    var topic = document.getElementById('topic').value;
    if(document.getElementById('topic_id')){
      var topicId = document.getElementById('topic_id').value;
    } else {
      var topicId = 0;
    }
    if(category && subcategory && topic && subject){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isStudyMaterialTopicExist')}}",
        data:{category:category,subcategory:subcategory,subject:subject,topic:topic,topic_id:topicId}
      }).done(function( msg ) {
          if('true' == msg){
            document.getElementById('topicError').classList.remove('hide');
            document.getElementById('topicError').classList.add('has-error');
          } else {
            document.getElementById('topicError').classList.add('hide');
            document.getElementById('topicError').classList.remove('has-error');
            document.getElementById('submitForm').submit();
          }
      });
    } else if(!category){
      alert('please select category.');
    } else if(!subcategory){
      alert('please select subcategory.');
    } else if(!course){
      alert('please enter name.');
    }
  }
</script>
@stop