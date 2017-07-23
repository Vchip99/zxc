<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">

      <title>Vchip</title>
      <link href="{{asset('css/bootstrap.min.css?ver=1.0')}}" rel="stylesheet">
      <link href="{{asset('css/main.css?ver=1.0')}}" rel="stylesheet">
      <!-- <link href="{{asset('css/style.css?ver=1.0')}}" rel="stylesheet"> -->
      <!-- Fonts -->
      <!-- <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css"> -->
      <script src="{{asset('js/jquery.min.js?ver=1.0')}}"></script>
      <script src="{{asset('js/bootstrap.min.js?ver=1.0')}}"></script>
<style type="text/css">
input, p{display: inline;  }
input{padding:  10px !important;}
p img{margin-top: 30px;
width: 100% !important;
}
.numpad{margin-top: 30px;}
</style>
  </head>
  <body>
    <div class="row">

  @if( !empty($questions[0]) && count($questions[0]) > 0)
    <a class="btn btn-primary" style="width:100px;" title="Technical">Technical</a>
    @foreach($questions[0] as $index => $question)
      <div class="panel-body">
        <div >
          <p class="questions" >
            <span class="btn btn-sq-xs btn-info">{{$index+1}}.</span>
            {!! $question->name !!}
          </p>
          <p>
          @if( 1 == $question->question_type )
            <div class="row">A.<input type="radio" value="1" class="radio1 radio1_{{$question->id}}" id="radio1_{{$question->id}}" name="{{$question->id}}" readonly="true"/>
              {!! $question->answer1 !!}
            </div>
            <div class="row">B.<input type="radio" value="2" class="radio1 radio1_{{$question->id}}" id="radio2_{{$question->id}}" name="{{$question->id}}" readonly="true"/>
              {!! $question->answer2 !!}
            </div>
            <div class="row">C.<input type="radio" value="3" class="radio1 radio1_{{$question->id}}" id="radio3_{{$question->id}}" name="{{$question->id}}" readonly="true"/>
              {!! $question->answer3 !!}
            </div>
            <div class="row">D.<input type="radio" value="4" class="radio1 radio1_{{$question->id}}" id="radio4_{{$question->id}}" name="{{$question->id}}" readonly="true"/>
              {!! $question->answer4 !!}
            </div>
          @else<br/>
          <div class="panel panel-default">
          <div class="panel-body">Enter your answer</div>
          </div>
          @endif
          </p>
        </div>
      </div>
    @endforeach
  @endif
  @if( !empty($questions[1]) && count($questions[1]) > 0)
    <a class="btn btn-primary" style="width:100px;" title="Aptitude">Aptitude</a>
    @foreach($questions[1] as $index => $question)
      <div class="panel-body">
        <div >
          <p class="questions" >
            <span class="btn btn-sq-xs btn-info">{{$index+1}}.</span>
            {!! $question->name !!}
          </p>
          <p>
          @if( 1 == $question->question_type )
            <div class="row">A.<input type="radio" value="1" class="radio1 radio1_{{$question->id}}" id="radio1_{{$question->id}}" name="{{$question->id}}" readonly="true"/>
              {!! $question->answer1 !!}
            </div>
            <div class="row">B.<input type="radio" value="2" class="radio1 radio1_{{$question->id}}" id="radio2_{{$question->id}}" name="{{$question->id}}" readonly="true"/>
              {!! $question->answer2 !!}
            </div>
            <div class="row">C.<input type="radio" value="3" class="radio1 radio1_{{$question->id}}" id="radio3_{{$question->id}}" name="{{$question->id}}" readonly="true"/>
              {!! $question->answer3 !!}
            </div>
            <div class="row">D.<input type="radio" value="4" class="radio1 radio1_{{$question->id}}" id="radio4_{{$question->id}}" name="{{$question->id}}" readonly="true"/>
              {!! $question->answer4 !!}
            </div>
          @else<br/>
          <div class="panel panel-default">
            <div class="panel-body">Enter your answer</div>
          </div>
          @endif
          </p>
        </div>
      </div>
    @endforeach
  @endif
</div>
    </body>
</html>
