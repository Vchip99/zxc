@foreach($comments as $subcomment)
	<div class="item replySubComment-1">
	    @if(0 != $subcomment->clientuser_id && (is_file($subcomment->getUser($subcomment->clientuser_id)->photo) || (!empty($subcomment->getUser($subcomment->clientuser_id)->photo) && false == preg_match('/clientUserStorage/',$subcomment->getUser($subcomment->clientuser_id)->photo))))
          <img src="{{ asset($subcomment->getUser($subcomment->clientuser_id)->photo)}} " class="img-circle" alt="User Image">
        @elseif(0 == $subcomment->clientuser_id && (is_file($subcomment->getClient($subcomment->client_id)->photo) || (!empty($subcomment->getClient($subcomment->client_id)->photo) && false == preg_match('/client_images/',$subcomment->getClient($subcomment->client_id)->photo))))
          <img src="{{ asset($subcomment->getClient($subcomment->client_id)->photo)}} " class="img-circle" alt="User Image">
        @else
          <img src="{{ url('images/user1.png')}}" class="img-circle" alt="User Image">
        @endif
	  	<div class="message" id="subcomment_{{$subcomment->id}}">
	  			@if(is_object($currentUser))
                    @if(1 == $currentUser->admin_approve)
                    	<div class="dropdown pull-right">
				      		<button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				        		<i class="fa fa-ellipsis-v" aria-hidden="true"></i>
					      	</button>
					      	<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
						        <li><a id="{{$subcomment->client_discussion_comment_id}}_{{$subcomment->id}}" data-subcomment_id="{{$subcomment->id}}" onclick="confirmSubCommentDelete(this);">Delete</a></li>
						        </a>
					        	<li><a id="{{$subcomment->id}}" onclick="editSubComment(this);">Edit</a></li>
					      	</ul>
				    	</div>
                    @else
                    	@if(0 != $subcomment->clientuser_id && $currentUser->id == $subcomment->clientuser_id)
					    	<div class="dropdown pull-right">
					      		<button class="btn dropdown-toggle btn-box-tool "  id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					        		<i class="fa fa-ellipsis-v" aria-hidden="true"></i>
						      	</button>
						      	<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
						      		@if($currentUser->id == $subcomment->clientuser_id || $currentUser->id == $post->clientuser_id)
								        <li><a id="{{$subcomment->client_discussion_comment_id}}_{{$subcomment->id}}" data-subcomment_id="{{$subcomment->id}}" onclick="confirmSubCommentDelete(this);">Delete</a></li>
								        </a>
						            @endif
						            @if($currentUser->id == $subcomment->clientuser_id)
							        	<li><a id="{{$subcomment->id}}" onclick="editSubComment(this);">Edit</a></li>
							        @endif
						      	</ul>
					    	</div>
				    	@endif
                    @endif
                @endif
		    	<p>
		    		<a class="SubCommentName">
			    		@if(0 != $subcomment->clientuser_id)
	                    	{{ $subcomment->getUser($subcomment->clientuser_id)->name }}
	                  	@else
	                    	{{ $subcomment->getClient($subcomment->client_id)->name }}
	                  	@endif
	                </a>
			    	<span class="more" id="editSubCommentHide_{{$subcomment->id}}">
			    	{!! $subcomment->body !!}
			    	</span>
		    	</p>
                	<div class="form-group hide" id="editSubCommentShow_{{$subcomment->id}}" >
                  		<textarea class="form-control" name="subcomment" id="update_subcomment_{{$subcomment->client_discussion_comment_id}}_{{$subcomment->id}}" rows="3">@php $string = preg_replace ("/<b>(.*?)<\/b>/i", "", $subcomment->body); $string = preg_replace('/\s+/', ' ',$string); $string = preg_replace('/\t+/', ' ',$string); $string = trim($string); @endphp {!! $string !!}</textarea>
                  		<button class="btn btn-primary" data-post_id="{{$subcomment->client_discussion_post_id}}" data-comment_id="{{$subcomment->client_discussion_comment_id}}" data-subcomment_id="{{$subcomment->id}}" onclick="updateSubComment(this);">Update</button>
                  		<button type="button" class="btn btn-default" id="{{$subcomment->id}}" onclick="cancleSubComment(this);">Cancle</button>
                	</div>
		</div>
	    <div class="comment-meta reply-1">
	      	<span id="sub_cmt_like_{{$subcomment->id}}" >
                @if( isset($subcommentLikesCount[$subcomment->id]) &&  is_object($currentUser))
                    @if(1 == $currentUser->admin_approve)
                     	@if(isset($subcommentLikesCount[$subcomment->id]['user_id'][$isClient][$currentUser->id]))
                        	<i id="sub_comment_like_{{$subcomment->id}}" data-post_id="{{$comment->client_discussion_post_id}}" data-comment_id="{{$comment->id}}" data-sub_comment_id="{{$subcomment->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                      	@else
                        	<i id="sub_comment_like_{{$subcomment->id}}" data-post_id="{{$comment->client_discussion_post_id}}" data-comment_id="{{$comment->id}}"  data-sub_comment_id="{{$subcomment->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                      	@endif
                    @else
                      	@if(isset($subcommentLikesCount[$subcomment->id]['user_id'][$isClient][$currentUser->id]))
                        	<i id="sub_comment_like_{{$subcomment->id}}" data-post_id="{{$comment->client_discussion_post_id}}" data-comment_id="{{$comment->id}}" data-sub_comment_id="{{$subcomment->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                      	@else
                        	<i id="sub_comment_like_{{$subcomment->id}}" data-post_id="{{$comment->client_discussion_post_id}}" data-comment_id="{{$comment->id}}"  data-sub_comment_id="{{$subcomment->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                      	@endif
                    @endif
                    	<span id="like1-bs3">{{count($subcommentLikesCount[$subcomment->id]['like_id'])}}</span>
                @else
                     <i id="sub_comment_like_{{$subcomment->id}}" data-post_id="{{$comment->client_discussion_post_id}}" data-comment_id="{{$comment->id}}"  data-sub_comment_id="{{$subcomment->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                     <span id="like1-bs3">@if( isset($subcommentLikesCount[$subcomment->id])) {{count($subcommentLikesCount[$subcomment->id]['like_id'])}} @endif</span>
                @endif
     	    </span>
	     	<span class="mrgn_5_left"><b>|</b>
	      		@if(is_object($currentUser))
	      			<a class="" role="button" data-toggle="collapse" href="#replySubComment{{$subcomment->client_discussion_comment_id}}-{{$subcomment->id}}" aria-expanded="false" aria-controls="collapseExample">reply</a>
                @else
                	<a role="button" data-toggle="modal" data-placement="bottom" href="#loginUserModel">reply</a>
                @endif
                <b>|</b>
	    	</span>
	    	<span class="text-muted time-of-reply"><i class="fa fa-clock-o"></i> {{$subcomment->updated_at->diffForHumans()}}</span>
		    <div class="collapse replyComment" id="replySubComment{{$subcomment->client_discussion_comment_id}}-{{$subcomment->id}}">
                	<div class="form-group">
                  		<label for="subcomment">Your Sub Comment</label>
                  		@if(is_object(Auth::guard('client')->user()) && ((Auth::guard('client')->user()->id == $subcomment->client_id && 1 == Auth::guard('client')->user()->admin_approve)))
                  			@if(0 != $subcomment->clientuser_id)
                  				<textarea name="subcomment" id="create_subcomment_{{$subcomment->id}}" class="form-control" rows="3">{{ $subcomment->getUser($subcomment->clientuser_id)->name }}</textarea>
                  			@else
                  				<textarea name="subcomment" id="create_subcomment_{{$subcomment->id}}" class="form-control" rows="3"></textarea>
                  			@endif
                  		@elseif(is_object(Auth::guard('clientuser')->user()) && Auth::guard('clientuser')->user()->client_id == $subcomment->client_id)
                  			@if(Auth::guard('clientuser')->user()->id == $subcomment->client_id && Auth::guard('clientuser')->user()->id != $subcomment->clientuser_id)
                  				@if(0 != $subcomment->clientuser_id)
                  					<textarea name="subcomment" id="create_subcomment_{{$subcomment->id}}" class="form-control" rows="3">{{ $subcomment->getUser($subcomment->clientuser_id)->name }}</textarea>
                  				@else
		                        	<textarea name="subcomment" id="create_subcomment_{{$subcomment->id}}" class="form-control" rows="3">{{ $subcomment->getClient($subcomment->client_id)->name }} </textarea>
		                        @endif
                  			@elseif(Auth::guard('clientuser')->user()->id != $subcomment->client_id && Auth::guard('clientuser')->user()->id != $subcomment->clientuser_id && 0 != $subcomment->clientuser_id)
	                        	<textarea name="subcomment" id="create_subcomment_{{$subcomment->id}}" class="form-control" rows="3">{{ $subcomment->getUser($subcomment->clientuser_id)->name }}</textarea>
	                        @else
	                        	<textarea name="subcomment" id="create_subcomment_{{$subcomment->id}}" class="form-control" rows="3"></textarea>
	                        @endif
                      	@else
	                        <textarea name="subcomment" id="create_subcomment_{{$subcomment->id}}" class="form-control" rows="3"></textarea>
                      	@endif
                	</div>
	                <button class="btn btn-default" data-post_id="{{$subcomment->client_discussion_post_id}}" data-comment_id="{{$subcomment->client_discussion_comment_id}}" data-parent_id="{{$subcomment->id}}" onclick="confirmSubmitReplytoSubComment(this);">Send</button>
	                <button type="button" class="btn btn-default" data-id="replySubComment{{$subcomment->client_discussion_comment_id}}-{{$subcomment->id}}" onclick="cancleReply(this);">Cancle</button>
            </div>
	  	</div>
	</div>
@endforeach