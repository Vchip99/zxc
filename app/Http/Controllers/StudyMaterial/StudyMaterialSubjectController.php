<?php

namespace App\Http\Controllers\StudyMaterial;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CourseSubCategory;
use App\Models\CourseCategory;
use App\Models\StudyMaterialSubject;
use Redirect;
use Validator, Auth, DB;
use App\Libraries\InputSanitise;

class StudyMaterialSubjectController extends Controller
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
    protected $validateCreateSubject = [
            'category' => 'required|integer',
            'subcategory' => 'required|integer',
            'subject' => 'required|string',
    ];

    /**
     *	show all subjects
     */
	public function show(){
		$subjects = StudyMaterialSubject::paginate();
		return view('studyMaterialSubject.list', compact('subjects'));
	}

	/**
	 *	show create UI for subject
	 */
	protected function create(){
		$courseCategories = CourseCategory::getCourseCategoriesForAdmin();
		$courseSubCategories = [];
		$subject = new StudyMaterialSubject;
		return view('studyMaterialSubject.create', compact('courseCategories','courseSubCategories','subject'));
	}

	/**
	 *	store subject
	 */
	protected function store(Request $request){
		$v = Validator::make($request->all(), $this->validateCreateSubject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
	        $subject = StudyMaterialSubject::addOrUpdateStudyMaterialSubject($request);
	        if(is_object($subject)){
	        	DB::commit();
	            return Redirect::to('admin/manageStudyMaterialSubject')->with('message', 'Subject created successfully!');
	        }
	    }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageStudyMaterialSubject');
	}

	/**
	 *	edit subject
	 */
	protected function edit($id){
		$id = InputSanitise::inputInt(json_decode($id));
		if(isset($id)){
			$subject = StudyMaterialSubject::find($id);
			if(is_object($subject)){
				$courseCategories = CourseCategory::getCourseCategoriesForAdmin();
				$courseSubCategories = CourseSubCategory::getCourseSubCategoriesByCategoryId($subject->course_category_id);;
				return view('studyMaterialSubject.create', compact('courseCategories','courseSubCategories','subject'));
			}
		}
		return Redirect::to('admin/manageStudyMaterialSubject');
	}

	/**
	 *	update subject
	 */
	protected function update(Request $request){
		$v = Validator::make($request->all(), $this->validateCreateSubject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
		$subjectId = InputSanitise::inputInt($request->get('subject_id'));
		if(isset($subjectId)){
			DB::beginTransaction();
	        try
	        {
				$subject = StudyMaterialSubject::addOrUpdateStudyMaterialSubject($request, true);
		        if(is_object($subject)){
		        	DB::commit();
		            return Redirect::to('admin/manageStudyMaterialSubject')->with('message', 'Subject updated successfully!');
		        }
		    }
	        catch(\Exception $e)
	        {
	            DB::rollback();
	            return back()->withErrors('something went wrong.');
	        }
		}
		return Redirect::to('admin/manageStudyMaterialSubject');
	}

	/**
	 *	delete subject
	 */
	protected function delete(Request $request){
		$subjectId = InputSanitise::inputInt($request->get('subject_id'));
		if(isset($subjectId)){
			$subject = StudyMaterialSubject::find($subjectId);
			if(is_object($subject)){
				DB::beginTransaction();
		        try
		        {
					$subject->delete();
					DB::commit();
					return Redirect::to('admin/manageStudyMaterialSubject')->with('message', 'Subject deleted successfully!');
				}
		        catch(\Exception $e)
		        {
		            DB::rollback();
		            return back()->withErrors('something went wrong.');
		        }
			}
		}
		return Redirect::to('admin/manageStudyMaterialSubject');
	}

	protected function isStudyMaterialSubjectExist(Request $request){
		return StudyMaterialSubject::isStudyMaterialSubjectExist($request);
	}

	protected function getStudyMaterialSubjectsByCategoryIdBySubCategoryId(Request $request){
		$categoryId = $request->get('category');
		$subcategoryId = $request->get('subcategory');
		return StudyMaterialSubject::getStudyMaterialSubjectsByCategoryIdBySubCategoryId($categoryId,$subcategoryId);
	}
}
