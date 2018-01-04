  @extends('admin.master')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Virtual Placement Drive </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-handshake-o"></i> Virtual Placement Drive </li>
      <li class="active"> Virtual Placement Drive </li>
    </ol>
  </section>
@stop
@section('admin_content')
    <script src="{{asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  <div class="container admin_div">
  @if(isset($virtualPlacementDrive->id))
    <form action="{{url('admin/updateVirtualPlacementDrive')}}" method="POST" enctype="multipart/form-data">
    {{method_field('PUT')}}
    <input type="hidden" id="placement_id" name="placement_id" value="{{$virtualPlacementDrive->id}}">
  @else
    <form action="{{url('admin/createVirtualPlacementDrive')}}" method="POST" enctype="multipart/form-data">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('name')) has-error @endif">
      <label for="name" class="col-sm-2 col-form-label">Virtual Placement Drive Name:</label>
      <div class="col-sm-3">
        @if(isset($virtualPlacementDrive->id))
          <input type="text" class="form-control" name="name" value="{{$virtualPlacementDrive->name}}" readonly="true">
        @else
          <input type="text" class="form-control" name="name" value="" placeholder="Name" required="true">
        @endif
        @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('about')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">About Virtual Placement Drive:</label>
      <div class="col-sm-10">
        <textarea name="about" cols="60" rows="4" id="about" placeholder="About Virtual Placement Drive" required>
          @if(isset($virtualPlacementDrive->id))
            {!! $virtualPlacementDrive->about !!}
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
      <label class="col-sm-2 col-form-label" for="about_image">About Virtual Placement Drive Image:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control"  name="about_image" id="about_image" >
        @if($errors->has('about_image')) <p class="has-error">{{ $errors->first('about_image') }}</p> @endif
        @if(isset($virtualPlacementDrive->about_image))
          <b><span>Existing Image: {!! basename($virtualPlacementDrive->about_image) !!}</span></b>
        @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('online_test')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Online Test:</label>
      <div class="col-sm-10">
        <textarea name="online_test" cols="60" rows="4" id="online_test" required>
          @if(isset($virtualPlacementDrive->id))
            {!! $virtualPlacementDrive->online_test !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'online_test', { enterMode: CKEDITOR.ENTER_BR } );
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
        @if($errors->has('online_test')) <p class="help-block">{{ $errors->first('online_test') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('gd')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">GD:</label>
      <div class="col-sm-10">
        <textarea name="gd" cols="60" rows="4" id="gd" required>
          @if(isset($virtualPlacementDrive->id))
            {!! $virtualPlacementDrive->gd !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'gd', { enterMode: CKEDITOR.ENTER_BR } );
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
        @if($errors->has('gd')) <p class="help-block">{{ $errors->first('gd') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('pi')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">TI/PI:</label>
      <div class="col-sm-10">
        <textarea name="pi" cols="60" rows="4" id="pi" required>
          @if(isset($virtualPlacementDrive->id))
            {!! $virtualPlacementDrive->pi !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'pi', { enterMode: CKEDITOR.ENTER_BR } );
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
        @if($errors->has('pi')) <p class="help-block">{{ $errors->first('pi') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('hr')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">HR:</label>
      <div class="col-sm-10">
        <textarea name="hr" cols="60" rows="4" id="hr" required>
          @if(isset($virtualPlacementDrive->id))
            {!! $virtualPlacementDrive->hr !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'hr', { enterMode: CKEDITOR.ENTER_BR } );
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
        @if($errors->has('hr')) <p class="help-block">{{ $errors->first('hr') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('suggestions')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Suggestions:</label>
      <div class="col-sm-10">
        <textarea name="suggestions" cols="60" rows="4" id="suggestions" required>
          @if(isset($virtualPlacementDrive->id))
            {!! $virtualPlacementDrive->suggestions !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'suggestions', { enterMode: CKEDITOR.ENTER_BR } );
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
        @if($errors->has('suggestions')) <p class="help-block">{{ $errors->first('suggestions') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('program_arrangement')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Program Arrangement:</label>
      <div class="col-sm-10">
        <textarea name="program_arrangement" cols="60" rows="4" id="program_arrangement" required>
          @if(isset($virtualPlacementDrive->id))
            {!! $virtualPlacementDrive->program_arrangement !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'program_arrangement', { enterMode: CKEDITOR.ENTER_BR } );
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
        @if($errors->has('program_arrangement')) <p class="help-block">{{ $errors->first('program_arrangement') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('program_arrangement_image')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="program_arrangement_image">Program Arrangement Image:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control"  name="program_arrangement_image" id="program_arrangement_image" >
        @if($errors->has('program_arrangement_image')) <p class="has-error">{{ $errors->first('program_arrangement_image') }}</p> @endif
        @if(isset($virtualPlacementDrive->program_arrangement_image))
          <b><span>Existing Image: {!! basename($virtualPlacementDrive->program_arrangement_image) !!}</span></b>
        @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('advantages')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Features and Advantages:</label>
      <div class="col-sm-10">
        <textarea name="advantages" cols="60" rows="4" id="advantages" required>
          @if(isset($virtualPlacementDrive->id))
            {!! $virtualPlacementDrive->advantages !!}
          @endif
        </textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'advantages', { enterMode: CKEDITOR.ENTER_BR } );
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
        @if($errors->has('advantages')) <p class="help-block">{{ $errors->first('advantages') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          <button type="submit" class="btn btn-primary">Submit</button>
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
</script>
@stop