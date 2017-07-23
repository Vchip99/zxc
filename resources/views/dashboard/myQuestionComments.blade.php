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
             	 	<span>
		                <a href="#">
		                  <i id="like1" class="fa fa-thumbs-o-up" aria-hidden="true"></i>
		                </a>
		                <span id="like1-bs3"></span>
		              </span>
	              	<span>
		                <a href="#">
		                  <i id="dislike1"  class="fa fa-thumbs-o-down" aria-hidden="true"></i>
		                </a>
		                <span id="dislike1-bs3"></span>
	              	</span>
	              <span class="mrgn_5_left">
	                <a class="" role="button" data-toggle="collapse" href="#replyToComment{{$comment->id}}" aria-expanded="false" aria-controls="collapseExample">reply</a>
	              </span>
	              <span class="sharecmt mrgn_5_left">share
	                <a class="social-icon facebook" target="blank" data-tooltip="Facebook" href="#">
	                  <i class="fa fa-facebook"></i>
	                </a>

	                <a class="social-icon twitter" target="blank" data-tooltip="Twitter" href="#">
	                  <i class="fa fa-twitter"></i>
	                </a>

	                <a class="social-icon linkedin" target="blank" data-tooltip="LinkedIn" href="#">
	                  <i class="fa fa-linkedin"></i>
	                </a>

	                <a class="social-icon google-plus" target="blank" data-tooltip="Google +" href="#">
	                  <i class="fa fa-google-plus"></i>
	                </a>

	                <a class="social-icon email" target="blank" data-tooltip="Contact e-Mail" href="#">
	                  <i class="fa fa-envelope-o"></i>
	                </a>
	              </span>
	              <div class="collapse replyComment" id="replyToComment{{$comment->id}}">
	                <form action="{{ url('createChildComment')}}" method="POST" id="formReplyToComment{{$comment->id}}">
		                 {{csrf_field()}}
		                <div class="form-group">
		                  <label for="comment">Your Comment</label>
		                  <textarea name="comment" class="form-control" rows="3"></textarea>
		                </div>
		                <input type="hidden" name="comment_id" value="{{$comment->id}}">
		                <input type="hidden" name="discussion_post_id" value="{{$post->id}}">
		                <input type="hidden" name="all_post_module_id" value="{{$allPostModuleId}}" id="all_post_module_id">
		                <button type="button" class="btn btn-default" onclick="confirmSubmitReply(this);" id="formReplyToComment{{$comment->id}}">Send</button>
	              	</form>
	              </div>
	            </div>
	         	@if(count( $comment->children) > 0)
		        	@include('dashboard.myQuestionComments', ['comments' => $comment->children, 'parent' => (int) $comment->id, 'user' => $user])
	          	@endif
	      </div>
	    </div>
	    </div>
    @endif
@endforeach