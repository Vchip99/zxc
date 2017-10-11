@foreach($comments as $subcomment)
	<div class="item replySubComment-1">
	  	@if(!empty($subcomment->user->photo))
	        <img src="{{ asset($subcomment->user->photo)}} " class="img-circle" alt="User Image">
      	@else
	        <img src="{{ url('images/user1.png')}}" class="img-circle" alt="User Image">
      	@endif
	  	<div class="message" id="subcomment_{{$subcomment->id}}">
	  		@if( is_object(Auth::user()) && ( Auth::user()->id == $subcomment->user_id || Auth::user()->id == $comment->user_id ))
	    	<div class="dropdown pull-right">
	      		<button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
	        		<i class="fa fa-ellipsis-v" aria-hidden="true"></i>
		      	</button>
		      	<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
		      		@if(Auth::user()->id == $subcomment->user_id || Auth::user()->id == $comment->user_id)
				        <li><a id="{{$subcomment->vkit_project_comment_id}}_{{$subcomment->id}}" data-subcomment_id="{{$subcomment->id}}" data-comment_id="{{$subcomment->vkit_project_comment_id}}" data-project_id="{{$subcomment->vkit_project_id}}" onclick="confirmSubCommentDelete(this);">Delete</a></li>
				        </a>
		            @endif
		            @if(Auth::user()->id == $subcomment->user_id)
			        	<li><a id="{{$subcomment->id}}" onclick="editSubComment(this);">Edit</a></li>
			        @endif
		      	</ul>
	    	</div>
	    	@endif
		    	<p><a class="SubCommentName">{{ $user->find($subcomment->user_id)->name }}</a>
		    	<span class="more" id="editSubCommentHide_{{$subcomment->id}}">
		    	{!! $subcomment->body !!}
		    	</span></p>
                   	<div class="form-group hide" id="editSubCommentShow_{{$subcomment->id}}" >
                  		<textarea class="form-control" name="subcomment" id="updateSubComment_{{$subcomment->id}}" rows="3">@php $string = preg_replace ("/<b>(.*?)<\/b>/i", "", $subcomment->body); $string = preg_replace('/\s+/', ' ',$string); $string = preg_replace('/\t+/', ' ',$string); $string = trim($string); @endphp {!! $string !!}</textarea>
                  		<button class="btn btn-primary" data-subcomment_id="{{$subcomment->id}}" data-comment_id="{{$subcomment->vkit_project_comment_id}}" data-project_id="{{$subcomment->vkit_project_id}}"  onclick="updateSubComment(this);">Update</button>
                  		<button class="btn btn-default" id="{{$subcomment->id}}" onclick="cancleSubComment(this);">Cancle</button>
                	</div>
		</div>
	    <div class="comment-meta reply-1">
	      	<span id="sub_cmt_like_{{$subcomment->id}}" >
                @if( isset($subcommentLikesCount[$subcomment->id]) && isset($subcommentLikesCount[$subcomment->id]['user_id'][$currentUser]))
                     <i id="sub_comment_like_{{$subcomment->id}}" data-project_id="{{$projectId}}" data-comment_id="{{$subcomment->vkit_project_comment_id}}" data-sub_comment_id="{{$subcomment->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                     <span id="like1-bs3">{{count($subcommentLikesCount[$subcomment->id]['like_id'])}}</span>
                @else
                     <i id="sub_comment_like_{{$subcomment->id}}" data-project_id="{{$projectId}}" data-comment_id="{{$subcomment->vkit_project_comment_id}}"  data-sub_comment_id="{{$subcomment->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                     <span id="like1-bs3">@if( isset($subcommentLikesCount[$subcomment->id])) {{count($subcommentLikesCount[$subcomment->id]['like_id'])}} @endif</span>
                @endif
     	    </span>
	     	<span class="mrgn_5_left">
	      		<a class="" role="button" data-toggle="collapse" href="#replySubComment{{$subcomment->vkit_project_comment_id}}-{{$subcomment->id}}" aria-expanded="false" aria-controls="collapseExample">reply</a>
	    	</span>
	    	<span class="text-muted time-of-reply"><i class="fa fa-clock-o"></i> {{$subcomment->updated_at->diffForHumans()}}</span>
		    <div class="collapse replyComment" id="replySubComment{{$subcomment->vkit_project_comment_id}}-{{$subcomment->id}}">
                	<div class="form-group">
                  		<label for="subcomment">Your Sub Comment</label>
                  		@if(is_object(Auth::user()) && Auth::user()->id != $subcomment->user_id)
	                        <textarea name="subcomment" class="form-control" id="createSubComment_{{$subcomment->id}}" rows="3">@if(is_object($user->find($subcomment->user_id))) {{$user->find($subcomment->user_id)->name}} @endif</textarea>
                      	@else
	                        <textarea name="subcomment" class="form-control" id="createSubComment_{{$subcomment->id}}" rows="3"></textarea>
                      	@endif
                	</div>
	                <button class="btn btn-default" onclick="confirmSubmitReplytoSubComment(this);" data-subcomment_id="{{$subcomment->id}}" data-comment_id="{{$subcomment->vkit_project_comment_id}}" data-project_id="{{$subcomment->vkit_project_id}}"  >Send</button>
	                <button type="button" class="btn btn-default" data-id="replySubComment{{$subcomment->vkit_project_comment_id}}-{{$subcomment->id}}" onclick="cancleReply(this);">Cancle</button>
            </div>
	  	</div>
	</div>
@endforeach