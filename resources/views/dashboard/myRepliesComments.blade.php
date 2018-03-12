@foreach($comments as $subcomment)
	<div class="item replySubComment-1">
	  	@if(!empty($subcomment->user->photo))
          <img class="img-circle" src="{{ asset($subcomment->user->photo) }}" alt="User Image" />
        @else
          <img class="img-circle" src="{{ asset('images/user1.png') }}" alt="User Image" />
        @endif
	  	<div class="message">
	    	<p><a id="{{$comment->id}}" class="SubCommentName" onClick="goToComment(this);" style="cursor: pointer;">{{ $user->find($subcomment->user_id)->name }}</a>
	    	<span class="more" id="editSubCommentHide_{{$subcomment->id}}">
	    	{!! $subcomment->body !!}
	    	</span></p>
	    	<form id="goToComment_{{$comment->id}}" action="{{ url('goToComment')}}" method="POST" style="display: none;" target="_blank">
              {{ csrf_field() }}
              <input type="hidden" name="comment_id" value="{{$comment->id}}">
            </form>
		</div>
	    <div class="comment-meta reply-1">
	      	<span id="sub_cmt_like_{{$subcomment->id}}" >
                @if( isset($subcommentLikesCount[$subcomment->id]) && isset($subcommentLikesCount[$subcomment->id]['user_id'][$currentUser]))
                     <i id="sub_comment_like_{{$subcomment->id}}" data-post_id="{{$comment->discussion_post_id}}" data-comment_id="{{$comment->id}}" data-sub_comment_id="{{$subcomment->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                     <span id="like1-bs3">{{count($subcommentLikesCount[$subcomment->id]['like_id'])}}</span>
                @else
                     <i id="sub_comment_like_{{$subcomment->id}}" data-post_id="{{$comment->discussion_post_id}}" data-comment_id="{{$comment->id}}"  data-sub_comment_id="{{$subcomment->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                     <span id="like1-bs3">@if( isset($subcommentLikesCount[$subcomment->id])) {{count($subcommentLikesCount[$subcomment->id]['like_id'])}} @endif</span>
                @endif
     	    </span>
	    	<span class="text-muted time-of-reply"><i class="fa fa-clock-o"></i> {{$subcomment->updated_at->diffForHumans()}}</span>
	  	</div>
	</div>
@endforeach