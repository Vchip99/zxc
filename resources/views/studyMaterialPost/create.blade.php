@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Post </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-file-pdf-o"></i> Study Material </li>
      <li class="active"> Manage Post </li>
    </ol>
  </section>
  <style type="text/css">
    .red-color{
      color: red;
    }
    ul#ul {
      /*width:1200px;*/
      list-style-type: none;
      margin-left: auto;
      margin-right: auto;
      padding: 0;
      overflow: hidden;
      background-color: #333;
    }

    ul#ul > li {
        float: left;
    }

    ul#ul > li > a {
        display: block;
        color: white;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
    }

    ul#ul > li > a:hover:not(.active) {
        background-color: #111;
    }

    .active {
        background-color: #4CAF50;
    }
  </style>
@stop
@section('admin_content')
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
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
  <div class="container admin_div">
  <ul id ="ul">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
      @if($prevPostId > 0)
        <li title="Prev Post"><a class="btn" id="prev_ques" href="{{url('admin/studyMaterialPost')}}/{{$prevPostId}}/edit">Prev Post</a></li>
      @else
        <li title="No Prev Post"><a class="btn" id="prev_ques">No Prev Post</a></li>
      @endif
      @if($nextPostId > 0)
        <li title="Next Post"><a class="btn" id="next_ques" href="{{url('admin/studyMaterialPost')}}/{{$nextPostId}}/edit">Next Post</a></li>
      @elseif(( $prevPostId > 0 || null == $prevPostId ) && null == $nextPostId )
        <li title="Add Post"><a class="btn" id="next_ques" href="{{url('admin/createStudyMaterialPost')}}">Add Post </a></li>
      @else
        <li title="No Next Post"><a class="btn" id="next_ques">No Next Post</a></li>
      @endif
  </ul>
  @if(isset($post->id))
    <form action="{{url('admin/updateStudyMaterialPost')}}" method="POST" enctype="multipart/form-data" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" id="post_id" name="post_id" value="{{$post->id}}">
  @else
    <form action="{{url('admin/createStudyMaterialPost')}}" method="POST" enctype="multipart/form-data" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('category')) has-error @endif">
      <label class="col-sm-2 col-form-label">Category:<span class="red-color">*</span></label>
      <div class="col-sm-3">
      @if(isset($post->id))
        @if(count($courseCategories) > 0)
          @foreach($courseCategories as $courseCategory)
            @if( isset($post->id) && $post->course_category_id == $courseCategory->id)
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
                @if($selectedCategoryId == $courseCategory->id)
                  <option value="{{$courseCategory->id}}" selected>{{$courseCategory->name}}</option>
                @else
                  <option value="{{$courseCategory->id}}">{{$courseCategory->name}}</option>
                @endif
              @endforeach
            @endif
        </select>
      @endif
        @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
      <label class="col-sm-2 col-form-label">Sub Category:<span class="red-color">*</span></label>
      <div class="col-sm-3">
        @if(isset($post->id) && count($courseSubCategories) > 0)
            @foreach($courseSubCategories as $courseSubCategory)
              @if($post->course_sub_category_id == $courseSubCategory->id)
                <input type="text" class="form-control" name="subcategory_text" value="{{$courseSubCategory->name}}" readonly>
                <input type="hidden" name="subcategory" id="subcategory" value="{{$courseSubCategory->id}}">
              @endif
            @endforeach
        @else
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
        @endif
        @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('subject')) has-error @endif">
      <label class="col-sm-2 col-form-label">Subject:<span class="red-color">*</span></label>
      <div class="col-sm-3">
        @if(isset($post->id) && count($subjects) > 0)
            @foreach($subjects as $subject)
              @if($post->study_material_subject_id == $subject->id)
                <input type="text" class="form-control" name="subject_text" value="{{$subject->name}}" readonly>
                <input type="hidden" name="subject" id="subject" value="{{$subject->id}}">
              @endif
            @endforeach
        @else
          <select id="subject" class="form-control" name="subject" required title="Subject" onChange="selectTopic(this);">
            <option value="0">Select Subject</option>
            @if(count($subjects) > 0)
              @foreach($subjects as $subject)
                @if($selectedSubjectId == $subject->id)
                  <option value="{{$subject->id}}" selected>{{$subject->name}}</option>
                @else
                  <option value="{{$subject->id}}" >{{$subject->name}}</option>
                @endif
              @endforeach
            @endif
          </select>
        @endif
        @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('topic')) has-error @endif">
      <label class="col-sm-2 col-form-label">Topic:<span class="red-color">*</span></label>
      <div class="col-sm-3">
        @if(isset($post->id) && count($topics) > 0)
          @foreach($topics as $topic)
            @if($post->study_material_topic_id == $topic->id)
              <input type="text" class="form-control" name="topic_text" value="{{$topic->name}}" readonly>
              <input type="hidden" name="topic" id="topic" value="{{$topic->id}}">
            @endif
          @endforeach
        @else
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
        @endif
        @if($errors->has('topic')) <p class="help-block">{{ $errors->first('topic') }}</p> @endif
      </div>
    </div>
    <div class="form-group row" id="show_common_data">
      <label class="col-sm-2 col-form-label">Post:<span class="red-color">*</span></label>
      <div class="col-sm-10">
        <textarea name="body" cols="60" rows="4" id="body" placeholder="Enter Post" required>
          @if(isset($post->id))
            {!! $post->body !!}
          @endif
        </textarea>
        <script type="text/javascript">
          var Full = [
                { name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
                { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
                { name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
                '/',
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
                { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
                { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                { name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe', 'Youtube', 'EqnEditor' ] },
                '/',
                { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
              ];
          CKEDITOR.replace( 'body', { enterMode: CKEDITOR.ENTER_BR, toolbar: Full});
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
      <label class="col-sm-2 col-form-label">Option 1:<span class="red-color">*</span></label>
      <div class="col-sm-3">
        <input type="text" name="answer1" id="answer1" value="{{$post->answer1}}" required>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Option 2:<span class="red-color">*</span></label>
      <div class="col-sm-3">
        <input type="text" name="answer2" id="answer2" value="{{$post->answer2}}" required>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Option 3:</label>
      <div class="col-sm-3">
        <input type="text" name="answer3" id="answer3" value="{{$post->answer3}}">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Option 4:</label>
      <div class="col-sm-3">
        <input type="text" name="answer4" id="answer4" value="{{$post->answer4}}">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Right Answer:<span class="red-color">*</span></label>
      <div class="col-sm-3">
        @if(isset($post->id))
          <input type="number" name="answer" id="answer" min="1" max="4" step="1" value="{{$post->answer}}" pattern="[1-4]{1}">
        @else
          <input type="number" name="answer" id="answer" min="1" max="4" step="1" value="1" pattern="[1-4]{1}">
        @endif
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Solution:<span class="red-color">*</span></label>
      <div class="col-sm-10">
        <textarea name="solution" id="solution" required cols="40" rows="5">
          @if(isset($post->id))
            {!! $post->solution !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'solution', { enterMode: CKEDITOR.ENTER_BR, toolbar: Full } );
          CKEDITOR.config.width="100%";
          CKEDITOR.config.height="auto";
          CKEDITOR.on('dialogDefinition', function (ev) {

              var dialogName = ev.data.name,
                  dialogDefinition = ev.data.definition;

              if (dialogName == 'image') {
                  var onOk = dialogDefinition.onOk;

                  dialogDefinition.onOk = function (e) {
                      var width = this.getContentElement('info', 'txtWidth');
                      width.setValue('100%');//Set Default Width

                      var height = this.getContentElement('info', 'txtHeight');
                      height.setValue('auto');////Set Default height

                      onOk && onOk.apply(this, e);
                  };
              }
          });
        </script>
      </div>
    </div>
    <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          @if(!empty($post->id) && $postSubject->admin_id == Auth::guard('admin')->user()->id)
            <button type="button" class="btn btn-primary" onclick="confirmSubmit();">Submit</button>
          @elseif(empty($post->id) )
            <button type="button" class="btn btn-primary" onclick="confirmSubmit();">Submit</button>
          @else
            <a href="{{ url('admin/manageStudyMaterialPost') }}" class="btn btn-primary">Back</a>
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

  function confirmSubmit(ele){
    var categoryId = parseInt(document.getElementById('category').value);
    var subcategoryId = parseInt(document.getElementById('subcategory').value);
    var subjectId = parseInt(document.getElementById('subject').value);
    var topicId = parseInt(document.getElementById('topic').value);
    var questionLength = CKEDITOR.instances.body.getData().length;
    var answer1 = document.getElementById('answer1').value;
    var answer2 = document.getElementById('answer2').value;
    var answer3 = document.getElementById('answer3').value;
    var answer4 = document.getElementById('answer4').value;
    var answer = document.getElementById('answer').value;
    var solutionLength = CKEDITOR.instances.solution.getData().length;

    if(isNaN(categoryId)) {
      $.alert({
        title: 'Alert!',
        content: 'Please select category.',
      });
      return false;
    }else if(isNaN(subcategoryId)) {
      $.alert({
        title: 'Alert!',
        content: 'Please select subcategory.',
      });
      return false;
    }else if(isNaN(subjectId)) {
      $.alert({
        title: 'Alert!',
        content: 'Please select subject.',
      });
      return false;
    }else if(isNaN(topicId)) {
      $.alert({
        title: 'Alert!',
        content: 'Please select topic.',
      });
      return false;
    } else if( 0 == questionLength){
      $.alert({
        title: 'Alert!',
        content: 'Please enter something in a question. ',
      });
      return false;
    } else if(!answer1){
      $.alert({
        title: 'Alert!',
        content: 'Please Enter Option 1.',
      });
      return false;
    } else if(!answer2){
      $.alert({
        title: 'Alert!',
        content: 'Please Enter Option 2.',
      });
      return false;
    } else if(!answer){
      $.alert({
        title: 'Alert!',
        content: 'Please Enter Right Answer.',
      });
      return false;
    } else if(0 == answer){
      $.alert({
        title: 'Alert!',
        content: 'Please enter right answer in between no of entered options.',
      });
      return false;
    } else if(0 == solutionLength){
      $.alert({
        title: 'Alert!',
        content: 'Please Enter Solution.',
      });
      return false;
    }
    var optionCount = 0;
    if(answer1){
      optionCount += 1;
    }
    if(answer2){
      optionCount += 1;
    }
    if(answer3){
      optionCount += 1;
    }
    if(answer4){
      optionCount += 1;
    }
    if(answer > optionCount || answer < 1){
      $.alert({
        title: 'Alert!',
        content: 'Please enter right answer in between no of entered options.',
      });
      return false;
    }
    if(categoryId > 0 && subcategoryId > 0 && subjectId > 0 && topicId > 0 && questionLength > 0 && answer1 && answer1 && answer && solutionLength > 0){
      document.getElementById('submitForm').submit();
    }
  }
</script>
@stop