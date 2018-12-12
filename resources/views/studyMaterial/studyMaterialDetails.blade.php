@extends('layouts.master')
@section('header-title')
  <title>Vchip-edu – Be partner with Vchip Technology</title>
@stop
@section('header-css')
	@include('layouts.home-css')
<link id="cpswitch" href="{{ asset('css/hover.css?ver=1.0')}}" rel="stylesheet" />
<link href="{{ asset('css/v_career.css?ver=1.0')}}" rel="stylesheet"/>
<link rel="stylesheet" href="{{ asset('css/star-rating.css') }}" />
  <style type="text/css">
    .fa {
      font-size: medium !important;
    }
    .rating-container .filled-stars{
      color: #e7711b;
      border-color: #e7711b;
    }
    .rating-xs {
        font-size: 0em;
    }
  a.list-group-item:hover{
    color: #f4645f;
  }
  h1 {
    font-size: 48px;
    font-weight: 200;
  }
</style>
@stop
@section('header-js')
	@include('layouts.home-js')
@stop
@section('content')
@include('header.study_material_menu',compact('categories','subcategories'))
<div class="container_fluid" style="padding-top: 100px; padding-bottom: 50px;">
  <div class="row ">
    <div style="margin-left: 35px;">
      <div style="display: inline-block;">
        @if(isset($reviewData[$subcategoryId])) {{$reviewData[$subcategoryId]['avg']}} @else 0 @endif
      </div>
      <div style="display: inline-block;">
        <input id="rating_input{{$subcategoryId}}" name="input-{{$subcategoryId}}" class="rating rating-loading" value="@if(isset($reviewData[$subcategoryId])) {{$reviewData[$subcategoryId]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
      </div>
      <div style="display: inline-block;">
        <a data-toggle="modal" data-target="#review-model-{{$subcategoryId}}">
          @if(isset($reviewData[$subcategoryId]))
            {{count($reviewData[$subcategoryId]['rating'])}} <i class="fa fa-group"></i>
          @else
            0 <i class="fa fa-group"></i>
          @endif
        </a>
      </div>
    </div>
    <div id="review-model-{{$subcategoryId}}" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            &nbsp;&nbsp;&nbsp;
            <button class="close" data-dismiss="modal">×</button>
            <div class="form-group row ">
              <div  style="display: inline-block;">
                @if(isset($reviewData[$subcategoryId])) {{$reviewData[$subcategoryId]['avg']}} @else 0 @endif
              </div>
              <div  style="display: inline-block;">
                <input name="input-{{$subcategoryId}}" class="rating rating-loading" value="@if(isset($reviewData[$subcategoryId])) {{$reviewData[$subcategoryId]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
              </div>
              <div  style="display: inline-block;">
                @if(isset($reviewData[$subcategoryId]))
                  {{count($reviewData[$subcategoryId]['rating'])}} <i class="fa fa-group"></i>
                @else
                  0 <i class="fa fa-group"></i>
                @endif
              </div>
              @if(is_object(Auth::user()))
                <button class="pull-right" data-toggle="modal" data-target="#rating-model-{{$subcategoryId}}">
                @if(isset($reviewData[$subcategoryId]) && isset($reviewData[$subcategoryId]['rating'][Auth::user()->id]))
                  Edit Rating
                @else
                  Give Rating
                @endif
                </button>
              @else
                <button class="pull-right" onClick="checkLogin();">Give Rating</button>
              @endif
            </div>
          </div>
          <div class="modal-body row">
            <div class="form-group row" style="overflow: auto;">
              @if(isset($reviewData[$subcategoryId]))
                @foreach($reviewData[$subcategoryId]['rating'] as $userId => $review)
                  {{$userNames[$userId]}}:
                  <input id="rating_input-{{$subcategoryId}}-{{$userId}}" name="input-{{$subcategoryId}}" class="rating rating-loading" value="{{$review['rating']}}" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                  {{$review['review']}}
                  <hr>
                @endforeach
              @else
                Please give ratings
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="rating-model-{{$subcategoryId}}" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button class="close" data-dismiss="modal">×</button>
            Rate and Review
          </div>
          <div class="modal-body row">
            <form action="{{ url('giveRating')}}" method="POST">
              <div class="form-group row ">
                {{ csrf_field() }}
                @if(isset($reviewData[$subcategoryId]) && is_object(Auth::user()) && isset($reviewData[$subcategoryId]['rating'][Auth::user()->id]))
                  <input id="rating_input-{{$subcategoryId}}" name="input-{{$subcategoryId}}" class="rating rating-loading" value="{{$reviewData[$subcategoryId]['rating'][Auth::user()->id]['rating']}}" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                @else
                  <input id="rating_input-{{$subcategoryId}}" name="input-{{$subcategoryId}}" class="rating rating-loading" value="0" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                @endif
                Review:<input type="text" name="review-text" class="form-control" value="@if(isset($reviewData[$subcategoryId])  && is_object(Auth::user()) && isset($reviewData[$subcategoryId]['rating'][Auth::user()->id])) {{trim($reviewData[$subcategoryId]['rating'][Auth::user()->id]['review'])}} @endif">
                <br>
                <input type="hidden" name="module_id" value="{{$subcategoryId}}">
                <input type="hidden" name="module_type" value="4">
                <input type="hidden" name="rating_id" value="@if(isset($reviewData[$subcategoryId]) && is_object(Auth::user()) && isset($reviewData[$subcategoryId]['rating'][Auth::user()->id])) {{$reviewData[$subcategoryId]['rating'][Auth::user()->id]['review_id']}} @endif">
                <button type="submit" class="pull-right">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-2">
      <div id="MainMenu">
        <div class="list-group panel">
          @if(count($subjects) > 0)
            <b> {{$subcategoryName}}</b>
            @foreach($subjects as $subjectId => $subject)
              <a href="#{{$subjectId}}" class="list-group-item" data-toggle="collapse" data-parent="#MainMenu">{{$subject}}  <i class="fa fa-caret-down"></i></a>
              @if(count($topics) > 0)
                <div class="collapse" id="{{$subjectId}}">
                  @foreach($topics[$subjectId] as $topicId => $topic)
                    <a href="{{ url('study-material')}}/{{$subcategoryId}}/{{$subject}}/{{$topicId}}" class="list-group-item" style="color: #f4645f;">{{$topic}}</a>
                  @endforeach
                </div>
              @endif
            @endforeach
          @endif
        </div>
      </div>
    </div>
    <div class="col-md-10">
      <div align="center"><img id="adImage" width="100%" style="max-width: 600px;" src=""></div>
      <h1 align="center">{{$topicName}}</h1>
      <hr>
      {!! $topicContent !!}
    </div>
  </div>
</div>
<input type="hidden" id="images" value="{{$images}}">
<input type="hidden" id="user_id" value="@if(is_object(Auth::user())) {{Auth::user()->id}} @endif">
@stop
@section('footer')
  @include('footer.footer')
  <script src="{{ asset('js/star-rating.js') }}"></script>
  <script type="text/javascript">
  $(document).ready(function() {
      if($('#images').val()){
        // change image after 10 sec
        setInterval(imageCycle, 10000);
        var count = 0;
        var images = $('#images').val().split(',');
        var imagePath = "{{ url('') }}/";
        function imageCycle(){
          if((count + 1) == images.length){
            count = 0;
          } else {
            count = count + 1;
          }
          $('#adImage').prop('src',imagePath+images[count]);
        }
      }
  });
</script>
@stop