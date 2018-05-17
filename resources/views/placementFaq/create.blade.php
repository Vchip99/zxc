@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Placement Process</h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-gift"></i> Placement </li>
      <li class="active"> Manage Placement Process</li>
    </ol>
  </section>
@stop
@section('admin_content')
  <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  &nbsp;
  <div class="container admin_div">
  @if(isset($placementFaq->id))
    <form action="{{url('admin/updatePlacementFaq')}}" method="POST" enctype="multipart/form-data">
      {{method_field('PUT')}}
      <input type="hidden" name="faq_id" value="{{$placementFaq->id}}">
  @else
      <form action="{{url('admin/createPlacementFaq')}}" method="POST" enctype="multipart/form-data">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('area')) has-error @endif">
    <label class="col-sm-2 col-form-label">Placement Area:</label>
    <div class="col-sm-3">
      <select class="form-control" name="area" required title="Placement Area" onClick="selectCompany(this);">
          <option value="">Select Placement Area</option>
          @if(count($placementAreas) > 0)
            @foreach($placementAreas as $placementArea)
              @if( $placementFaq->placement_area_id == $placementArea->id)
                <option value="{{$placementArea->id}}" selected="true">{{$placementArea->name}}</option>
              @else
                <option value="{{$placementArea->id}}">{{$placementArea->name}}</option>
              @endif
            @endforeach
          @endif
        </select>
        @if($errors->has('area')) <p class="help-block">{{ $errors->first('area') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('company')) has-error @endif">
    <label for="company" class="col-sm-2 col-form-label">Placement Company:</label>
    <div class="col-sm-3">
      <select class="form-control" id="company" name="company" required title="Placement Company" >
          <option value="">Select Placement Company</option>
          @if(count($placementCompanies) > 0)
            @foreach($placementCompanies as $placementCompany)
              @if( $placementFaq->placement_company_id == $placementCompany->id)
                <option value="{{$placementCompany->id}}" selected="true">{{$placementCompany->name}}</option>
              @else
                <option value="{{$placementCompany->id}}">{{$placementCompany->name}}</option>
              @endif
            @endforeach
          @endif
        </select>
        @if($errors->has('company')) <p class="help-block">{{ $errors->first('company') }}</p> @endif
    </div>
  </div>
  @if(isset($placementFaq->id))
    <div class="form-group row @if ($errors->has('question')) has-error @endif">
        <label for="question_1" class="col-sm-2 col-form-label">Question:</label>
        <div class="col-sm-5">
            <textarea class="form-control" id="question" name="question" required="true">{{$placementFaq->question}}</textarea>
          @if($errors->has('question')) <p class="help-block">{{ $errors->first('question') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('answer')) has-error @endif">
        <label for="answer_1" class="col-sm-2 col-form-label">Answer:</label>
        <div class="col-sm-5">
            <textarea class="form-control" id="answer" name="answer" required="true">{{$placementFaq->answer}}
            </textarea>
          @if($errors->has('answer')) <p class="help-block">{{ $errors->first('answer') }}</p> @endif
        </div>
      </div>
  @else
    <div id="faqDiv">
      <div id="div_question_1" class="form-group row @if ($errors->has('question_1')) has-error @endif">
        <label for="question_1" class="col-sm-2 col-form-label">Question:</label>
        <div class="col-sm-5">
            <textarea class="form-control" id="question_1" name="question_1" required="true"></textarea>
          @if($errors->has('question_1')) <p class="help-block">{{ $errors->first('question_1') }}</p> @endif
        </div>
      </div>
      <div id="div_answer_1" class="form-group row @if ($errors->has('answer_1')) has-error @endif">
        <label for="answer_1" class="col-sm-2 col-form-label">Answer:</label>
        <div class="col-sm-5">
            <textarea class="form-control" id="answer_1" name="answer_1" required="true"></textarea>
          @if($errors->has('answer_1')) <p class="help-block">{{ $errors->first('answer_1') }}</p> @endif
        </div>
      </div>
    </div>
  @endif
  <div class="form-group row">
    <div class="offset-sm-2 col-sm-3" title="Submit">
      <button type="submit" class="btn btn-primary">Submit</button>
      @if(empty($placementFaq->id))
      <button id="addQueAndAns" class="btn btn-primary">Add Question And Answer</button>
      @endif
    </div>
  </div>
  <input type="hidden" id="question_count" name="question_count" value="1">
  </div>
</form>
<script type="text/javascript">

  $('#addQueAndAns').click(function(){
    event.preventDefault();
    var faqDiv = document.getElementById('faqDiv');
    var count = parseInt(document.getElementById('question_count').value) + 1;

    var mainDivQue = document.createElement('div');
    mainDivQue.className = 'form-group row ';
    mainDivQue.setAttribute("id", "div_question_" + count);

    var eleLabel = document.createElement('label');
    eleLabel.className = 'col-sm-2 col-form-label';
    eleLabel.innerHTML = 'Question:';
    mainDivQue.appendChild(eleLabel);

    var firstDivQue = document.createElement('div');
    firstDivQue.className = 'col-sm-5';

    var textareaQue = document.createElement('textarea');
    textareaQue.className = 'form-control';
    textareaQue.setAttribute("id", "question_" + count);
    textareaQue.setAttribute("name", "question_" + count);
    firstDivQue.appendChild(textareaQue);
    mainDivQue.appendChild(firstDivQue);

    var eleImg = document.createElement("img");
    deleteImg = "{{asset('images/delete3.png')}}";
    eleImg.setAttribute("src", deleteImg);
    eleImg.className ="img-vsm";
    eleImg.setAttribute("onClick", "removeElement('faqDiv'," + count + ")");
    mainDivQue.appendChild(eleImg);

    faqDiv.appendChild(mainDivQue);

    var mainDivAns = document.createElement('div');
    mainDivAns.className = 'form-group row ';
    mainDivAns.setAttribute("id", "div_answer_" + count);

    var eleLabel = document.createElement('label');
    eleLabel.className = 'col-sm-2 col-form-label';
    eleLabel.innerHTML = 'Answer:';
    mainDivAns.appendChild(eleLabel);

    var firstDivAns = document.createElement('div');
    firstDivAns.className = 'col-sm-5';

    var textareaAns = document.createElement('textarea');
    textareaAns.className = 'form-control';
    textareaAns.setAttribute("id", "answer_" + count);
    textareaAns.setAttribute("name", "answer_" + count);
    firstDivAns.appendChild(textareaAns);
    mainDivAns.appendChild(firstDivAns);

    var eleImg = document.createElement("img");
    deleteImg = "{{asset('images/delete3.png')}}";
    eleImg.setAttribute("src", deleteImg);
    eleImg.className ="img-vsm";
    eleImg.setAttribute("onClick", "removeElement('faqDiv'," + count + ")");
    mainDivAns.appendChild(eleImg);

    faqDiv.appendChild(mainDivAns);
    document.getElementById('question_count').value = count;

  });

  function removeElement(parentDiv, childDiv){
      var childQue = document.getElementById('div_question_'+childDiv);
      var childAns = document.getElementById('div_answer_'+childDiv);
      var parent = document.getElementById(parentDiv);

      if(childQue){
        parent.removeChild(childQue);
      }
      if(childAns){
        parent.removeChild(childAns);
      }
  }

  function selectCompany(ele){
    id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('admin/getPlacementCompaniesByArea')}}",
          data: {id:id}
      })
      .done(function( msg ) {
        select = document.getElementById('company');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Placement Company';
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