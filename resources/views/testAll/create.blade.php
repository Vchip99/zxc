@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage All </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Online Test </li>
      <li class="active"> Manage All </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
  <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
  <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  <div class="container">
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
  </div>
  <div class="container admin_div">
   <form action="{{url('admin/createAllTestCategory')}}" method="POST" id="submitCategoryForm">
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('category')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="category">Category Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="category" name="category" value="" required="true">
        @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
        <span class="hide" id="categoryError" style="color: white;">Given name is already exist.Please enter another name.</span>
      </div>
    </div>
    <input type="hidden" name="category_for" value="1">
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="button" class="btn btn-primary" onclick="searchCategory();">Submit</button>
      </div>
    </div>
    </form>
  </div>
  <br>
  <div class="container admin_div">
    <form action="{{url('admin/createAllTestSubCategory')}}" method="POST" enctype="multipart/form-data" id="submitSubCategoryForm">
      {{ csrf_field() }}
      <div class="form-group row @if ($errors->has('category')) has-error @endif">
        <label class="col-sm-2 col-form-label">Category Name:</label>
        <div class="col-sm-3">
          <select class="form-control" name="category" id="select_category" required title="Category">
              <option value="">Select Category</option>
              @if(count($testCategories) > 0)
                @foreach($testCategories as $testCategory)
                  <option value="{{$testCategory->id}}">{{$testCategory->name}}</option>
                @endforeach
              @endif
            </select>
            @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('name')) has-error @endif">
        <label for="name" class="col-sm-2 col-form-label">Sub Category Name:</label>
        <div class="col-sm-3">
            <input type="text" class="form-control" name="name" id="subcategory" value="" required="true">
          @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
          <span class="hide" id="subcategoryError" style="color: white;">Given name is already exist with selected category.Please enter another name.</span>
        </div>
      </div>
      <div class="form-group row @if ($errors->has('image_path')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="image_path">Sub Category Image:</label>
        <div class="col-sm-3">
          <input type="file" class="form-control"  name="image_path" id="image_path" >
          @if($errors->has('image_path')) <p class="has-error">{{ $errors->first('image_path') }}</p> @endif
        </div>
      </div>
      <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          <button type="button" class="btn btn-primary" onclick="searchSubCategory();">Submit</button>
        </div>
      </div>
    </form>
  </div>
  <br>
  <div class="container admin_div">
    <form action="{{url('admin/createAllTestSubject')}}" method="POST" id="submitSubjectForm">
      {{ csrf_field() }}
      <div class="form-group row @if ($errors->has('category')) has-error @endif">
        <label class="col-sm-2 col-form-label">Category Name:</label>
        <div class="col-sm-3">
          <select id="subject_category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
              <option value="">Select Category</option>
              @if(count($testCategories) > 0)
                @foreach($testCategories as $testCategory)
                  <option value="{{$testCategory->id}}">{{$testCategory->name}}</option>
                @endforeach
              @endif
          </select>
          @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
        <label class="col-sm-2 col-form-label">Sub Category Name:</label>
        <div class="col-sm-3">
          <select id="subject_subcategory" class="form-control" name="subcategory" required title="Sub Category">
            <option value="">Select Sub Category</option>
          </select>
          @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('name')) has-error @endif">
        <label for="name" class="col-sm-2 col-form-label">Subject Name:</label>
        <div class="col-sm-3">
            <input type="text" class="form-control" name="name" id="subject" value="" placeholder="Subject Name" required="true">
          @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
          <span class="hide" id="subjectError" style="color: white;">Given name is already exist with selected category and sub category.Please enter another name.</span>
        </div>
      </div>
      <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="button" class="btn btn-primary" onclick="searchSubject();">Submit</button>
      </div>
      </div>
    </form>
  </div>
  <br>
  <div class="container admin_div">
    <form action="{{url('admin/createAllTestPaper')}}" method="POST" id="submitPaperForm">
      {{ csrf_field() }}
      <div class="form-group row @if ($errors->has('category')) has-error @endif">
        <label class="col-sm-2 col-form-label">Category Name:</label>
        <div class="col-sm-3">
          <select id="paper_category" class="form-control" name="category" onChange="selectPaperSubcategory(this);" required title="Category">
            <option value="">Select Category</option>
            @foreach($testCategories as $testCategory)
                <option value="{{$testCategory->id}}">{{$testCategory->name}}</option>
            @endforeach
          </select>
          @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
        <label class="col-sm-2 col-form-label">Sub Category Name:</label>
        <div class="col-sm-3">
          <select id="paper_subcategory" class="form-control" name="subcategory" onChange="selectPaperSubject(this);" required title="Sub Category">
            <option value="">Select Sub Category</option>
          </select>
          @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('subject')) has-error @endif">
        <label class="col-sm-2 col-form-label">Subject Name:</label>
        <div class="col-sm-3">
          <select id="paper_subject" class="form-control" name="subject" required title="Subject">
            <option value="">Select Subject</option>
          </select>
          @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('name')) has-error @endif">
        <label for="name" class="col-sm-2 col-form-label">Paper Name:</label>
        <div class="col-sm-3">
            <input type="text" class="form-control" name="name" id="paper" value="" placeholder="paper name" required="true">
          @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
          <span class="hide" id="paperError" style="color: white;">Given name is already exist with selected category, sub category and subject.Please enter another name.</span>
        </div>
      </div>
      <div class="form-group row @if ($errors->has('price')) has-error @endif">
        <label for="price" class="col-sm-2 col-form-label">Price:</label>
        <div class="col-sm-3">
            <input type="text" class="form-control" name="price" value="" placeholder="price" required="true">
          @if($errors->has('price')) <p class="help-block">{{ $errors->first('price') }}</p> @endif
        </div>
      </div>
      <div class="form-group row">
        <label for="date_to_active" class="col-sm-2 col-form-label">Date To Active:</label>
        <div class="col-sm-3">
          <input type="text"  class="form-control" name="date_to_active" id="date_to_active" required="true" placeholder="date" required="true">
        </div>
        <script type="text/javascript">
            var currentDate = "{{ date('Y-m-d H:i:s')}}";
            $(function () {
                $('#date_to_active').datetimepicker({defaultDate: currentDate,format: 'YYYY-MM-DD  HH:mm'});
            });
        </script>
      </div>
      <div class="form-group row">
        <label for="date_to_active" class="col-sm-2 col-form-label">Date To Inactive:</label>
        <div class="col-sm-3">
          <input type="text"  class="form-control" name="date_to_inactive" id="date_to_inactive" required="true" placeholder="date" required="true">
        </div>
        <script type="text/javascript">
            $(function () {
                $('#date_to_inactive').datetimepicker({defaultDate: "2050-01-01 12:00",format: 'YYYY-MM-DD  HH:mm'});
            });
        </script>
      </div>
      <div class="form-group row @if ($errors->has('paper_pattern')) has-error @endif ">
        <label for="paper" class="col-sm-2 col-form-label">Paper Pattern:</label>
        <div class="col-sm-3">
            <label class="radio-inline"><input type="radio" name="paper_pattern" value="0"  onClick="togglePapperPattern(this)" checked> General Pattern</label>
            <label class="radio-inline"><input type="radio" name="paper_pattern" value="1"   onClick="togglePapperPattern(this)" > IIT JEE Pattern</label>
        </div>
      </div>
      <div class="form-group row @if ($errors->has('time_out_by')) has-error @endif " id="paperTimeOut">
        <label for="paper" class="col-sm-2 col-form-label">Total Time Out By:</label>
        <div class="col-sm-3">
            <label class="radio-inline"><input type="radio" name="time_out_by" value="1" onClick="selectedSession(this);" checked> Paper Wise</label>
            <label class="radio-inline"><input type="radio" name="time_out_by" value="0" onClick="selectedSession(this);"> Session Wise</label>
          @if($errors->has('time_out_by')) <p class="help-block">{{ $errors->first('time_out_by') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('time')) has-error @endif paper_duration">
        <label for="name" class="col-sm-2 col-form-label">Paper Time:</label>
        <div class="col-sm-3">
            <input type="text" class="form-control" name="time" id="time" value="" placeholder="time in seconds">
          @if($errors->has('time')) <p class="help-block">{{ $errors->first('time') }}</p> @endif
        </div>
      </div>
      <div class="form-group row">
        <label for="" class="col-sm-2 col-form-label">Session And Its Time:</label>
        <table class="">
          <thead class="">
            <tr>
              <th class="col-sm-4">Session Name</th>
              <th class="col-sm-4 duration">Duration(In Sec.)</th>
              <th class="col-sm">Add</th>
              <th class="col-sm">Delete</th>
            </tr>
          </thead>
          <tbody id="all_session">
              <tr id="tr_1">
                <td class="col-sm-3"><input type="text" class="form-control" name="session_1" value="" required="true"></td>
                <td class="col-sm-3 duration"><input type="text" class="form-control " name="duration_1" value=""></td>
                <td class=""><button type="button" onClick="addSessions(event);"> <i class="fa fa-plus-circle" aria-hidden="true"></i></button></td>
                <td class=""></td>
              </tr>
          </tbody>
        </table>
          <input type="hidden" id="all_session_count" name="all_session_count" value="1">
      </div>
      <div class="form-group row @if ($errors->has('show_calculator')) has-error @endif">
        <label for="paper" class="col-sm-2 col-form-label">Show Calculator:</label>
        <div class="col-sm-3">
          <label class="radio-inline"><input type="radio" name="show_calculator" value="1" checked> Yes</label>
          <label class="radio-inline"><input type="radio" name="show_calculator" value="0"> No</label>
          @if($errors->has('show_calculator')) <p class="help-block">{{ $errors->first('show_calculator') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('show_solution')) has-error @endif">
        <label for="paper" class="col-sm-2 col-form-label">Show Solution:</label>
        <div class="col-sm-3">
          <label class="radio-inline"><input type="radio" name="show_solution" value="1" checked> Yes</label>
          <label class="radio-inline"><input type="radio" name="show_solution" value="0"> No</label>
          @if($errors->has('show_solution')) <p class="help-block">{{ $errors->first('show_solution') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('option_count')) has-error @endif">
        <label for="paper" class="col-sm-2 col-form-label">Option Count:</label>
        <div class="col-sm-3">
            <label class="radio-inline"><input type="radio" name="option_count" value="4" checked> 4</label>
            <label class="radio-inline"><input type="radio" name="option_count" value="5"> 5</label>
        @if($errors->has('option_count')) <p class="help-block">{{ $errors->first('option_count') }}</p> @endif
        </div>
      </div>
      <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          <button type="button" class="btn btn-primary" onclick="searchPaper();">Submit</button>
        </div>
      </div>
    </form>
  </div>
<input type="hidden" id="selected_time_out" name="selected_time_out" value="0">
<script type="text/javascript">
  function searchCategory(){
    var category = document.getElementById('category').value;
    var categoryId = 0;
    if(category){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isTestCategoryExist')}}",
        data:{category:category,category_id:categoryId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('categoryError').classList.remove('hide');
          document.getElementById('categoryError').classList.add('has-error');
        } else {
          document.getElementById('categoryError').classList.add('hide');
          document.getElementById('categoryError').classList.remove('has-error');
          document.getElementById('submitCategoryForm').submit();
        }
      });
    } else {
      alert('please enter category name.');
    }
  }
  function searchSubCategory(){
    var category = document.getElementById('select_category').value;
    var subcategory = document.getElementById('subcategory').value;
    var subcategoryId = 0;
    if(category && subcategory){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isTestSubCategoryExist')}}",
        data:{category:category,subcategory:subcategory,subcategory_id:subcategoryId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('subcategoryError').classList.remove('hide');
          document.getElementById('subcategoryError').classList.add('has-error');
        } else {
          document.getElementById('subcategoryError').classList.add('hide');
          document.getElementById('subcategoryError').classList.remove('has-error');
          document.getElementById('submitSubCategoryForm').submit();
        }
      });
    } else if(!category){
      alert('please select category.');
    } else if(!subcategory){
      alert('please enter categoty name.');
    }
  }
  function selectSubcategory(ele){
    id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('admin/getSubCategories')}}",
          data: {id:id}
      })
      .done(function( msg ) {
        select = document.getElementById('subject_subcategory');
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
            select.appendChild(opt);
          });
        }
      });
    }
  }
  function searchSubject(){
    var category = document.getElementById('subject_category').value;
    var subcategory = document.getElementById('subject_subcategory').value;
    var subject = document.getElementById('subject').value;
    var subjectId = 0;
    if(category && subcategory && subject){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isTestSubjectExist')}}",
        data:{category:category,subcategory:subcategory,subject:subject,subject_id:subjectId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('subjectError').classList.remove('hide');
          document.getElementById('subjectError').classList.add('has-error');
        } else {
          document.getElementById('subjectError').classList.add('hide');
          document.getElementById('subjectError').classList.remove('has-error');
          document.getElementById('submitSubjectForm').submit();
        }
      });
    } else if(!category){
      alert('please select category.');
    } else if(!subcategory){
      alert('please select subcategory.');
    } else if(!subject){
      alert('please enter subject name.');
    }
  }
  function addSessions(event){
      event.preventDefault();
      var allSession = document.getElementById('all_session');
      var count = parseInt(document.getElementById('all_session_count').value) + 1;

      var eleBr = document.createElement('Br');
      eleBr.setAttribute("id", "br_" + count);
      allSession.appendChild(eleBr);

      var firstTr = document.createElement('tr');
      firstTr.setAttribute("id", "tr_" + count);

      var firstTd = document.createElement('td');
      firstTd.className = 'col-sm-3';

      var firstInput = document.createElement('input');
      firstInput.className = 'form-control';
      firstInput.setAttribute("id", "session_" + count);
      firstInput.setAttribute("name", "session_" + count);
      firstInput.setAttribute("type", "text");
      firstTd.appendChild(firstInput);
      firstTr.appendChild(firstTd);

      var secondTd = document.createElement('td');
      if(1 == document.getElementById('selected_time_out').value){
        secondTd.className = 'col-sm-3 duration hide';
      } else {
        secondTd.className = 'col-sm-3 duration';
      }

      var secondInput = document.createElement('input');
      secondInput.className = 'form-control';
      secondInput.setAttribute("id", "duration_" + count);
      secondInput.setAttribute("name", "duration_" + count);
      secondInput.setAttribute("type", "text");

      secondTd.appendChild(secondInput);
      firstTr.appendChild(secondTd);

      var thirdTd = document.createElement('td');

      var thirdButton = document.createElement('button');
      thirdButton.setAttribute("onClick", "addSessions(event);");
      thirdButton.innerHTML = '<i class="fa fa-plus-circle" aria-hidden="true"></i>';
      thirdTd.appendChild(thirdButton);
      firstTr.appendChild(thirdTd);

      var fifthTd = document.createElement('td');

      var fifthButton = document.createElement('button');
      fifthButton.setAttribute("onClick", "removeElement('all_session',"+count+");");
      fifthButton.innerHTML = '<i class="fa fa-minus-circle" aria-hidden="true"></i>';
      fifthTd.appendChild(fifthButton);
      firstTr.appendChild(fifthTd);

      allSession.appendChild(firstTr);

      document.getElementById('all_session_count').value = count;
  }
  function removeElement(parent,childId){
    event.preventDefault();
    var childTr = document.getElementById('tr_'+childId);
    var childBr = document.getElementById('br_'+childId);
    var parent = document.getElementById(parent);
    if(childTr){
      parent.removeChild(childTr);
    }
    if(childBr){
      parent.removeChild(childBr);
    }
  }
  function selectedSession(ele){
    if(1 == $(ele).val()){
      document.getElementById('selected_time_out').value = $(ele).val();
      $('.duration').addClass('hide');
      $('.paper_duration').removeClass('hide');
      $.each($('.duration > input'), function(idx, obj){ $(obj).prop('required', false); });
      $('#time').prop('required', true);
    } else {
      document.getElementById('selected_time_out').value = $(ele).val();
      $('.duration').removeClass('hide');
      $('.paper_duration').addClass('hide');
      $.each($('.duration > input'), function(idx, obj){ $(obj).prop('required', true); });
      $('#time').prop('required', false);
    }
  }
  function selectPaperSubcategory(ele){
    id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('admin/getSubCategories')}}",
          data: {id:id}
      })
      .done(function( msg ) {
        select = document.getElementById('paper_subcategory');
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
              select.appendChild(opt);
          });
        }
      });
    }
  }
  function selectPaperSubject(ele){
    subcatId = parseInt($(ele).val());
    catId = parseInt(document.getElementById('paper_category').value);
    if( 0 < catId && 0 < subcatId ){
      $.ajax({
          method: "POST",
          url: "{{url('admin/getSubjectsByCatIdBySubcatId')}}",
          data: {catId:catId, subcatId:subcatId}
      })
      .done(function( msg ) {
        selectSub = document.getElementById('paper_subject');
        selectSub.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
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
  $( document ).ready(function() {
    var selectedOption = $("input:radio[name=time_out_by]:checked").val()
    if(1 == selectedOption){
      $('.duration').addClass('hide');
      $('.paper_duration').removeClass('hide');
      $.each($('.duration > input'), function(idx, obj){ $(obj).prop('required', false); });
      $('#time').prop('required', true);
      document.getElementById('selected_time_out').value = 1;
    } else {
      $('.duration').removeClass('hide');
      $('.paper_duration').addClass('hide');
      $.each($('.duration > input'), function(idx, obj){ $(obj).prop('required', true); });
      $('#time').prop('required', false);
      document.getElementById('selected_time_out').value = 0;
    }
  });
  function searchPaper(){
    var category = document.getElementById('paper_category').value;
    var subcategory = document.getElementById('paper_subcategory').value;
    var subject = document.getElementById('paper_subject').value;
    var paper = document.getElementById('paper').value;
    var paperId = 0;
    if(category && subcategory && subject && paper){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isTestPaperExist')}}",
        data:{category:category,subcategory:subcategory,subject:subject,paper:paper,paper_id:paperId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('paperError').classList.remove('hide');
          document.getElementById('paperError').classList.add('has-error');
        } else {
          document.getElementById('paperError').classList.add('hide');
          document.getElementById('paperError').classList.remove('has-error');
          document.getElementById('submitPaperForm').submit();
        }
      });
    } else if(!category){
      alert('please select category.');
    } else if(!subcategory){
      alert('please select subcategory.');
    } else if(!subject){
      alert('please select subject.');
    } else if(!paper){
      alert('please enter paper name.');
    }
  }
  function togglePapperPattern(ele){
    if(1 == $(ele).val()){
      $('#paperTimeOut').addClass('hide');
      // hide session code
      document.getElementById('selected_time_out').value = $(ele).val();
      $('.duration').addClass('hide');
      $('.paper_duration').removeClass('hide');
      $.each($('.duration > input'), function(idx, obj){ $(obj).prop('required', false); });
      $('#time').prop('required', true);
    } else {
      $('#paperTimeOut').removeClass('hide');
      $('#time').prop('required', false);
    }
  }
</script>
@stop