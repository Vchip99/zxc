<?php

namespace App\Http\Controllers\Blog;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\Notification;
use Validator, Session, Auth, DB, Input;
use App\Libraries\InputSanitise;
use App\Mail\MailToSubscribedUser;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class AdminBlogController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
     */
	public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin') || $adminUser->hasPermission('manageBlog')){
                    return $next($request);
                }
            }
            return Redirect::to('admin/home');
        });
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateBlog = [
        'title' => 'required|string',
        'author' => 'required|string',
        'category_id' => 'required|integer',
        'content' => 'required',
    ];

    /**
     *  show list of all blog
     */
    protected function show(){
    	$blogs = Blog::paginate();
    	return view('blog.list', compact('blogs'));
    }

    /**
     *  show create blog UI
     */
    protected function create(){
    	$blog = new blog;
        $blogCategories = BlogCategory::all();
        $tags = '';
        return view('blog.create', compact('blog', 'blogCategories', 'tags'));
    }

    /**
     *  store blog
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateBlog);
        if ($v->fails())
        {   $request->flash();
            return redirect()->back()->withErrors($v->errors());
        }
        $blogTitle = InputSanitise::inputString($request->get('title'));
        $blogCategoryId = InputSanitise::inputInt($request->get('category_id'));
        $duplicateBlogName = Blog::where('title', $blogTitle)->where('blog_category_id', $blogCategoryId)->first();
        if(is_object($duplicateBlogName)){
            $request->flash();
            return redirect()->back()->withErrors(['title' => 'blog title already exist for selected category. please use another blog title.']);
        }

        DB::beginTransaction();
        try
        {
            $blog = Blog::addOrUpdateBlog($request);
            if(is_object($blog)){
                $messageBody = '';
                $notificationMessage = 'A new blog: <a href="'.$request->root().'/blogComment/'.$blog->id.'">'.$blog->title.'</a> has been added.';
                Notification::addNotification($notificationMessage, Notification::ADMINBLOG, $blog->id);

                $tags = explode(',',$request->get('tags'));
                $tags = array_map('trim', $tags);
                BlogTag::addTags($tags, $blog->id);
                DB::commit();
                // $subscriedUsers = User::where('admin_approve', 1)->where('verified', 1)->select('email')->get()->toArray();
                // $allUsers = array_chunk($subscriedUsers, 100);
                // if(count($allUsers) > 0){
                //     foreach($allUsers as $selectedUsers){
                //         foreach($selectedUsers as $user){
                //             $user = User::where('email', $user)->first();
                //             $messageBody .= '<p> Hello '.$user->name.'</p>';
                //             $messageBody .= '<p>'.$notificationMessage.' please have a look once.</p>';
                //             $messageBody .= '<p><b> Thanks and Regard, </b></p>';
                //             $messageBody .= '<b><a href="https://vchiptech.com"> Vchip Technology Team </a></b><br/>';
                //             $messageBody .= '<b> More about us... </b><br/>';
                //             $messageBody .= '<b><a href="https://vchipedu.com"> Digital Education </a></b><br/>';
                //             $messageBody .= '<b><a href="mailto:info@vchiptech.com" target="_blank">E-mail</a></b><br/>';
                //             $mailSubject = 'Vchipedu added a new blog';
                //             Mail::to($user)->queue(new MailToSubscribedUser($messageBody, $mailSubject));
                //         }
                //     }
                // }
                return Redirect::to('admin/manageBlog')->with('message', 'Blog created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageBlog');
    }

    /**
     *  edit blog
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$blog = Blog::find($id);
            $tagNames = [];
            $tags = '';
    		if(is_object($blog)){
                $blogCategories = BlogCategory::all();
                $blogTags = BlogTag::where('blog_id', $id)->get();
                if(count($blogTags)>0){
                    foreach($blogTags as $blogTag){
                        $tagNames[] = $blogTag->name;
                    }
                    if(count($tagNames) > 0){
                        $tags = implode(',', $tagNames);
                    }
                }
    			return view('blog.create', compact('blog', 'blogCategories', 'tags'));
    		}
    	}
    	return Redirect::to('admin/manageBlog');
    }

    /**
     *  update blog
     */
    protected function update(Request $request){
    	$v = Validator::make($request->all(), $this->validateBlog);
        if ($v->fails())
        {   $request->flash();
            return redirect()->back()->withErrors($v->errors());
        }

        $blogId = InputSanitise::inputInt($request->get('blog_id'));
        $blogTitle = InputSanitise::inputString($request->get('title'));
        $blogCategoryId = InputSanitise::inputInt($request->get('category_id'));
        $duplicateBlogName = Blog::where('title', $blogTitle)->where('blog_category_id', $blogCategoryId)->where('id', '!=', $blogId)->first();
        if(is_object($duplicateBlogName)){
            $request->flash();
            return redirect()->back()->withErrors(['title' => 'blog title already exist for selected category. please use another blog title.']);
        }

        if(isset($blogId)){
            DB::beginTransaction();
            try
            {
         		$blog = Blog::addOrUpdateBlog($request, true);
    	        if(is_object($blog)){
                    $dbTags = [];
                    $inputTags = array_map('trim', explode(',',$request->get('tags')));

                    $blogTags  = BlogTag::where('blog_id', $blog->id)->select('name')->get();
                    if(is_object($blogTags) && false == $blogTags->isEmpty()){
                        foreach($blogTags as $blogTag){
                            $dbTags[] = $blogTag->name;
                        }
                    }
                    $addTags = array_diff($inputTags, $dbTags);
                    $deleteTags = array_diff($dbTags, $inputTags);
                    if(count($addTags) > 0){
                        foreach($addTags as $addTag){
                            $arrTabs[] = ['name' => $addTag, 'blog_id' => $blog->id];
                        }
                        DB::table('blog_tags')->insert($arrTabs);
                    }
                    if(count($deleteTags) > 0){
                        foreach($deleteTags as $deleteTag){
                            $blogTag = BlogTag::where('name', $deleteTag)->where('blog_id', $blog->id)->first();
                            if(is_object($blogTag)){
                                $blogTag->delete();
                            }
                        }
                    }
                    DB::commit();
    	            return Redirect::to('admin/manageBlog')->with('message', 'Blog updated successfully!');
    	        }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageBlog');

    }

    /**
     *  delete blog
     */
    protected function delete(Request $request){
    	$blogId = InputSanitise::inputInt($request->get('blog_id'));
    	if(isset($blogId)){
    		$blog = Blog::find($blogId);
    		if(is_object($blog)){
                DB::beginTransaction();
                try
                {
                    if(true == is_object($blog->comments) && false == $blog->comments->isEmpty()){
                        foreach($blog->comments as $comment){
                            $comment->delete();
                        }
                    }
                    if(true == is_object($blog->commentLikes) && false == $blog->commentLikes->isEmpty()){
                        foreach($blog->commentLikes as $commentLike){
                            $commentLike->delete();
                        }
                    }
                    $blog->deleteCommantsAndSubComments();
                    $blog->deleteBlogTags();
        			$blog->delete();
                    DB::commit();
        			return Redirect::to('admin/manageBlog')->with('message', 'Blog deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('admin/manageBlog');
    }

    protected function isBlogExist(Request $request){
        return Blog::isBlogExist($request);
    }
}