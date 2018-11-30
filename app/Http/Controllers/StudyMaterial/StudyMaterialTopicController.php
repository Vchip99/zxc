<?php

namespace App\Http\Controllers\StudyMaterial;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CourseSubCategory;
use App\Models\CourseCategory;
use App\Models\StudyMaterialSubject;
use App\Models\StudyMaterialTopic;
use Redirect;
use Validator, Auth, DB;
use App\Libraries\InputSanitise;

class StudyMaterialTopicController extends Controller
{
	/**
     * check admin have permission or not, if not redirect to admin/home
     */
	public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin')){
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
    protected $validateCreateTopic = [
            'category' => 'required|integer',
            'subcategory' => 'required|integer',
            'subject' => 'required|string',
            'topic' => 'required|string',
    ];

    /**
     *	show all topics
     */
	public function show(){
		$topics = StudyMaterialTopic::paginate();
		return view('studyMaterialTopic.list', compact('topics'));
	}

	/**
	 *	show create UI for topic
	 */
	protected function create(){
		$courseCategories = CourseCategory::getCourseCategoriesForAdmin();
		$courseSubCategories = [];
		$subjects = [];
		$topic = new StudyMaterialTopic;
		return view('studyMaterialTopic.create', compact('courseCategories','courseSubCategories','subjects','topic'));
	}

	/**
	 *	store topic
	 */
	protected function store(Request $request){

		$v = Validator::make($request->all(), $this->validateCreateTopic);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
	        $topic = StudyMaterialTopic::addOrUpdateStudyMaterialTopic($request);
	        if(is_object($topic)){
	        	DB::commit();
	            return Redirect::to('admin/manageStudyMaterialTopic')->with('message', 'Topic created successfully!');
	        }
	    }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageStudyMaterialTopic');
	}

	/**
	 *	edit topic
	 */
	protected function edit($id){
		$id = InputSanitise::inputInt(json_decode($id));
		if(isset($id)){
			$topic = StudyMaterialTopic::find($id);
			if(is_object($topic)){
				$courseCategories = CourseCategory::getCourseCategoriesForAdmin();
				$courseSubCategories = CourseSubCategory::getCourseSubCategoriesByCategoryId($topic->course_category_id);
				$subjects = StudyMaterialSubject::getStudyMaterialSubjectsByCategoryIdBySubCategoryId($topic->course_category_id,$topic->course_sub_category_id);
				return view('studyMaterialTopic.create', compact('courseCategories','courseSubCategories','subjects','topic'));
			}
		}
		return Redirect::to('admin/manageStudyMaterialTopic');
	}

	/**
	 *	update topic
	 */
	protected function update(Request $request){
		$v = Validator::make($request->all(), $this->validateCreateTopic);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
		$topicId = InputSanitise::inputInt($request->get('topic_id'));
		if(isset($topicId)){
			DB::beginTransaction();
	        try
	        {
				$topic = StudyMaterialTopic::addOrUpdateStudyMaterialTopic($request, true);
		        if(is_object($topic)){
		        	DB::commit();
		            return Redirect::to('admin/manageStudyMaterialTopic')->with('message', 'Topic updated successfully!');
		        }
		    }
	        catch(\Exception $e)
	        {
	            DB::rollback();
	            return back()->withErrors('something went wrong.');
	        }
		}
		return Redirect::to('admin/manageStudyMaterialTopic');
	}

	/**
	 *	delete topic
	 */
	protected function delete(Request $request){
		$topicId = InputSanitise::inputInt($request->get('topic_id'));
		if(isset($topicId)){
			$topic = StudyMaterialTopic::find($topicId);
			if(is_object($topic)){
				DB::beginTransaction();
		        try
		        {
					$topic->delete();
					DB::commit();
					return Redirect::to('admin/manageStudyMaterialTopic')->with('message', 'Topic deleted successfully!');
				}
		        catch(\Exception $e)
		        {
		            DB::rollback();
		            return back()->withErrors('something went wrong.');
		        }
			}
		}
		return Redirect::to('admin/manageStudyMaterialTopic');
	}

	protected function isStudyMaterialTopicExist(Request $request){
		return StudyMaterialTopic::isStudyMaterialTopicExist($request);
	}


}
