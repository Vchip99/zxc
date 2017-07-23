@foreach($comments as $comment)
    @if($parent === (int) $comment->parent_id)
      <div class="media">
        <div class="media-heading">
          <button class="btn btn-default btn-collapse btn-xs cmt_plus_minus_symbol" type="button" data-toggle="collapse" data-target="#collapseComment{{$comment->id}}" aria-expanded="false" aria-controls="collapseExample"></button>
          <span class="label label-info">{{$blog->title}}</span> {{ $user->find($comment->user_id)->name }} {{$comment->updated_at->diffForHumans()}}
        </div>
        <div class="cmt_child panel-collapse collapse in" id="collapseComment{{$comment->id}}">
          <div class="media-body">
            <p>{{ $comment->body }}</p>
            <div class="comment-meta">
                <div class="comment-meta">
                      <span id="like_{{$comment->id}}" >
                        @if( isset($likesCount[$comment->id]) && isset($likesCount[$comment->id]['user_id'][$currentUser]))
                             <i id="blog_like_{{$comment->id}}" data-blog_id="{{$comment->blog_id}}" data-comment_id="{{$comment->id}}" data-dislike='1' class="fa fa-thumbs-up" aria-hidden="true" data-placement="bottom" title="remove like"></i>
                             <span id="like1-bs3">{{count($likesCount[$comment->id]['like_id'])}}</span>
                        @else
                             <i id="blog_like_{{$comment->id}}" data-blog_id="{{$comment->blog_id}}" data-comment_id="{{$comment->id}}" data-dislike='0' class="fa fa-thumbs-o-up" aria-hidden="true" data-placement="bottom" title="add like"></i>
                             <span id="like1-bs3">@if( isset($likesCount[$comment->id])) {{count($likesCount[$comment->id]['like_id'])}} @endif</span>
                        @endif
                      </span>
                <span class="mrgn_5_left">
                  <a class="" role="button" data-toggle="collapse" href="#replyToComment{{$comment->id}}" aria-expanded="false" aria-controls="collapseExample">reply</a>
                </span>
                <div class="collapse" id="replyToComment{{$comment->id}}">
                  <form action="{{ url('createBlogChildComment')}}" method="POST" id="createBlogChildComment{{$comment->id}}">
                     {{csrf_field()}}
                    <div class="form-group">
                      <label for="comment">Your Comment</label>
                      <textarea name="comment" class="form-control" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="blog_id" value="{{$blog->id}}">
                      <input type="hidden" name="comment_id" value="{{$comment->id}}">
                    <button type="button" class="btn btn-default" onclick="confirmSubmit(this);" data-id="createBlogChildComment{{$comment->id}}">Send</button>
                  </form>
                </div>
              </div>
            @if(count( $comment->children) > 0)
            @include('blog.child_comments', ['comments' => $comment->children, 'parent' => (int) $comment->id, 'user' => $user])
            @endif
        </div>
      </div>
      </div>
    @endif
@endforeach