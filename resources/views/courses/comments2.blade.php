@foreach($comments as $comment)
    @if($parent === (int) $comment->parent_id)
    	<div class="media">
	      <div class="media-heading">
	        <button class="btn btn-default btn-collapse btn-xs cmt_plus_minus_symbol" type="button" data-toggle="collapse" data-target="#collapseComment{{$comment->id}}" aria-expanded="false" aria-controls="collapseExample"></button>
	        <span class="label label-info">{{$post->title}}</span> <i class="fa fa-clock-o" aria-hidden="true"></i> {{ $user->find($comment->user_id)->name }} {{$comment->updated_at->diffForHumans()}}
	      </div>
	      <div class="cmt_child panel-collapse collapse in" id="collapseComment{{$comment->id}}">
         	<div class="media-body">
	         	<p>{{ $comment->body }}</p>
	         	<div class="comment-meta">
             	 	<span id="cmt_like_{{$comment->id}}" >
                      @if( isset($commentLikesCount[$comment->id]) && isset($commentLikesCount[$comment->id]['user_id'][$currentUser]))
                           <i id="comment_like_{{$comment->id}}" data-post_id="{{$comment->all_post_id}}" data-comment_id="{{$comment->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                           <span id="like1-bs3">{{count($commentLikesCount[$comment->id]['like_id'])}}</span>
                      @else
                           <i id="comment_like_{{$comment->id}}" data-post_id="{{$comment->all_post_id}}" data-comment_id="{{$comment->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                           <span id="like1-bs3">@if( isset($commentLikesCount[$comment->id])) {{count($commentLikesCount[$comment->id]['like_id'])}} @endif</span>
                      @endif
                  	</span>
	              <span class="mrgn_5_left">
	                <a class="" role="button" data-toggle="collapse" href="#replyToComment{{$comment->id}}" aria-expanded="false" aria-controls="collapseExample">reply</a>
	              </span>
	              <div class="collapse replyComment" id="replyToComment{{$comment->id}}">
	                <form action="{{ url('createAllChildComment')}}" method="POST" id="formReplyToComment{{$comment->id}}">
		                 {{csrf_field()}}
		                <div class="form-group">
		                  <label for="comment">Your Comment</label>
		                  <textarea name="comment" class="form-control" rows="3"></textarea>
		                </div>
		                <input type="hidden" name="comment_id" value="{{$comment->id}}">
		                <input type="hidden" name="all_post_module_id" value="{{$allPostModule}}">
		                <input type="hidden" name="all_post_id" value="{{$post->id}}">
		                <input type="hidden" name="episode_id" value="{{$episodeId}}" >
		                <input type="hidden" name="project_id" value="0" >
		                <button type="button" class="btn btn-default" onclick="confirmSubmit(this);" id="formReplyToComment{{$comment->id}}">Send</button>
	              	</form>
	              </div>
	            </div>
	         	@if(count( $comment->children) > 0)
		        	@include('courses.comments', ['comments' => $comment->children, 'parent' => (int) $comment->id, 'user' => $user, 'episodeId' => $video->id])
	          	@endif
	      </div>
	    </div>
	    </div>
    @endif
@endforeach