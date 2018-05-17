  @extends('admin.master')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Manage Motivational Speech Details </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-microphone"></i> Motivational Speech </li>
      <li class="active"> Manage Motivational Speech Details </li>
    </ol>
  </section>
@stop
@section('admin_content')
    <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  <div class="container admin_div">
  @if(isset($motivationalSpeechDetail->id))
    <form action="{{url('admin/updateMotivationalSpeechDetails')}}" method="POST" enctype="multipart/form-data" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" id="motivational_speech_id" name="motivational_speech_id" value="{{$motivationalSpeechDetail->id}}">
  @else
    <form action="{{url('admin/createMotivationalSpeechDetails')}}" method="POST" enctype="multipart/form-data" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('category')) has-error @endif">
      <label class="col-sm-2 col-form-label">Speaker Name:</label>
      <div class="col-sm-3">
        @if(count($motivationalSpeechCategories) > 0 && isset($motivationalSpeechDetail->id))
          @foreach($motivationalSpeechCategories as $motivationalSpeechCategory)
            @if( isset($motivationalSpeechDetail->id) && $motivationalSpeechDetail->motivational_speech_category_id == $motivationalSpeechCategory->id)
              <input type="text" class="form-control" name="category_text" value="{{$motivationalSpeechCategory->name}}" readonly="true">
              <input type="hidden" name="category" id="category" value="{{$motivationalSpeechCategory->id}}">
            @endif
          @endforeach
        @else
          <select id="category" class="form-control" name="category" title="Speaker" required >
          <option value="">Select Speaker</option>
            @foreach($motivationalSpeechCategories as $motivationalSpeechCategory)
              @if( isset($motivationalSpeechDetail->id) && $motivationalSpeechDetail->motivational_speech_category_id == $motivationalSpeechCategory->id)
                <option value="{{$motivationalSpeechCategory->id}}" selected="true">{{$motivationalSpeechCategory->name}}</option>
              @else
                <option value="{{$motivationalSpeechCategory->id}}">{{$motivationalSpeechCategory->name}}</option>
              @endif
            @endforeach
          </select>
        @endif
        @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
      </div>
    </div>
     <div class="form-group row @if ($errors->has('name')) has-error @endif">
      <label for="name" class="col-sm-2 col-form-label">Speech Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" name="name" id="motivational_speech" value="{{$motivationalSpeechDetail->name}}" required="true">
        @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
        <span class="hide" id="speechError" style="color: white;">Given name is already exist with selected speaker.Please enter another name.</span>
      </div>
    </div>
    <div class="form-group row @if ($errors->has('about')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">About Speaker:</label>
      <div class="col-sm-10">
        <textarea name="about" cols="60" rows="4" id="about" placeholder="About Motivational Speech" required>
          @if(isset($motivationalSpeechDetail->id))
            {!! $motivationalSpeechDetail->about !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'about', { enterMode: CKEDITOR.ENTER_BR } );
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
        @if($errors->has('about')) <p class="help-block">{{ $errors->first('about') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('about_image')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="about_image">Speaker Image:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control"  name="about_image" id="about_image" >
        @if($errors->has('about_image')) <p class="has-error">{{ $errors->first('about_image') }}</p> @endif
        @if(isset($motivationalSpeechDetail->about_image))
          <b><span>Existing Image: {!! basename($motivationalSpeechDetail->about_image) !!}</span></b>
        @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('topics')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Topics:</label>
      <div class="col-sm-10">
        <textarea name="topics" cols="60" rows="4" id="topics" required>
          @if(isset($motivationalSpeechDetail->id))
            {!! $motivationalSpeechDetail->topics !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'topics', { enterMode: CKEDITOR.ENTER_BR } );
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
        @if($errors->has('topics')) <p class="help-block">{{ $errors->first('topics') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('program_details')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Details About How Program Will Arrange:</label>
      <div class="col-sm-10">
        <textarea name="program_details" cols="60" rows="4" id="program_details" required>
          @if(isset($motivationalSpeechDetail->id))
            {!! $motivationalSpeechDetail->program_details !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'program_details', { enterMode: CKEDITOR.ENTER_BR } );
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
        @if($errors->has('program_details')) <p class="help-block">{{ $errors->first('program_details') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          <button type="button" class="btn btn-primary" onclick="searchSpeach();">Submit</button>
        </div>
      </div>
  </div>
</form>

<script type="text/javascript">
  function addComponent(){
    event.preventDefault();
    var pattern = document.getElementById('pattern');
    var count = parseInt(document.getElementById('component_count').value) + 1;

    var eleBr = document.createElement('Br');
    eleBr.setAttribute("id", "br_" + count);
    pattern.appendChild(eleBr);

    var firstTr = document.createElement('tr');
    firstTr.setAttribute("id", "tr_" + count);

    var firstTd = document.createElement('td');
    firstTd.className = 'col-sm-3';

    var firstInput = document.createElement('input');
    firstInput.className = 'form-control';
    firstInput.setAttribute("id", "component_" + count);
    firstInput.setAttribute("name", "component_" + count);
    firstInput.setAttribute("type", "text");
    firstTd.appendChild(firstInput);
    firstTr.appendChild(firstTd);

    var secondTd = document.createElement('td');
    secondTd.className = 'col-sm-3';

    var secondInput = document.createElement('input');
    secondInput.className = 'form-control';
    secondInput.setAttribute("id", "quantity_" + count);
    secondInput.setAttribute("name", "quantity_" + count);
    secondInput.setAttribute("type", "text");
    secondTd.appendChild(secondInput);
    firstTr.appendChild(secondTd);

    var fourthTd = document.createElement('td');

    var fourthButton = document.createElement('button');
    fourthButton.setAttribute("onClick", "addComponent();");
    fourthButton.innerHTML = '<i class="fa fa-plus-circle" aria-hidden="true"></i>';
    fourthTd.appendChild(fourthButton);
    firstTr.appendChild(fourthTd);

    var fifthTd = document.createElement('td');

    var fifthButton = document.createElement('button');
    fifthButton.setAttribute("onClick", "removeElement('pattern',"+count+");");
    fifthButton.innerHTML = '<i class="fa fa-minus-circle" aria-hidden="true"></i>';
    fifthTd.appendChild(fifthButton);
    if(document.getElementById('workshop_id')){
      var processId = document.getElementById('workshop_id').value;
      if(processId > 0){
        var hiddenInput = document.createElement('input');
        hiddenInput.className = 'form-control';
        hiddenInput.setAttribute("name", "new_" + count);
        hiddenInput.setAttribute("type", "hidden");
        hiddenInput.setAttribute("value", "new");
        fifthTd.appendChild(hiddenInput);
        firstTr.appendChild(fifthTd);
      }
    }
      firstTr.appendChild(fifthTd);

    pattern.appendChild(firstTr);

    document.getElementById('component_count').value = count;
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

  function searchSpeach(){
    var category = document.getElementById('category').value;
    var speech = document.getElementById('motivational_speech').value;

    if(document.getElementById('motivational_speech_id')){
      var speechId = document.getElementById('motivational_speech_id').value;
    } else {
      var speechId = 0;
    }
    if(category && speech){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isMotivationalSpeechExist')}}",
        data:{category:category,speech:speech,motivational_speech_id:speechId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('speechError').classList.remove('hide');
          document.getElementById('speechError').classList.add('has-error');
        } else {
          document.getElementById('speechError').classList.add('hide');
          document.getElementById('speechError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    } else if(!category){
      alert('please select speaker.');
    } else {
      alert('please enter name.');
    }
  }
</script>
@stop