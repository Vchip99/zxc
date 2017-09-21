<?php

namespace App\Http\Controllers\Client\InstituteCourse;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect,Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\ClientInstituteCourse;
use App\Models\ClientOnlineCategory;
use App\Models\ClientOnlineTestCategory;
use App\Models\Client;
use App\Models\ClientUserSolution;
use App\Models\ClientScore;

class ClientInstituteCourseController extends ClientBaseController
{
	/**
     *  check admin have permission or not, if not redirect to home
     */
	public function __construct(Request $request) {
        parent::__construct($request);
        $this->middleware('client');
    }

	/**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateCourse = [
        'course' => 'required|string',
    ];

    /**
     *  show list of course category
     */
   	public function show(Request $request){
   		$clientId = Auth::guard('client')->user()->id;
        $instituteCourses = ClientInstituteCourse::where('client_id', $clientId)->paginate();
        return view('client.instituteCourse.list', compact('instituteCourses'));
    }

    public function create(){
    	$course = new ClientInstituteCourse;
    	return view('client.instituteCourse.create', compact('course'));
    }

    public function store(Request $request){
    	$v = Validator::make($request->all(), $this->validateCourse);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
    	try{
	    	$course = ClientInstituteCourse::addOrUpdateInstituteCourse($request);
	    	if(is_object($course)){
	    		DB::connection('mysql2')->commit();
	    		return Redirect::to('manageInstituteCourses')->with('message', 'Institute course created successfully.');
	    	}
	    }
	    catch(\Exception $e){
	    	DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
	    }
    	return Redirect::to('manageInstituteCourses');
    }

    /**
     *  edit institute course
     */
    protected function edit($subdomain, $id){
        $id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$course = ClientInstituteCourse::find($id);

    		if(is_object($course)){
    			return view('client.instituteCourse.create', compact('course'));
    		}
    	}
    	return Redirect::to('manageInstituteCourses');
    }

        /**
     *  update institute category
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateCourse);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

    	$courseId = InputSanitise::inputInt($request->get('course_id'));
    	if(isset($courseId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $course = ClientInstituteCourse::addOrUpdateInstituteCourse($request, true);
                if(is_object($course)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageInstituteCourses')->with('message', 'Institute course updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('manageInstituteCourses');
    }

    /**
     *  delete institute course
     */
    protected function delete(Request $request){
    	$courseId = InputSanitise::inputInt($request->get('course_id'));
    	if(isset($courseId)){
    		$instituteCourse = ClientInstituteCourse::find($courseId);
    		if(is_object($instituteCourse)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $categories = ClientOnlineCategory::where('client_institute_course_id',$instituteCourse->id)->where('client_id', $instituteCourse->client_id)->get();
                    if(is_object($categories) && false == $categories->isEmpty()){
                        foreach($categories as $courseCategory){
                            if(is_object($courseCategory->subcategories) && false == $courseCategory->subcategories->isEmpty()){
                                foreach($courseCategory->subcategories as $courseSubcategory){
                                    if(true == is_object($courseSubcategory->courses) && false == $courseSubcategory->courses->isEmpty()){
                                        foreach($courseSubcategory->courses as $course){
                                            if(true == is_object($course->videos) && false == $course->videos->isEmpty()){
                                                foreach($course->videos as $video){
                                                    $video->deleteCommantsAndSubComments();
                                                    $video->delete();
                                                }
                                            }
                                            $course->deleteRegisteredOnlineCourses();
                                            $course->deleteCourseImageFolder($request);
                                            $course->delete();
                                        }
                                    }
                                    $courseSubcategory->delete();
                                }
                            }
                            $courseCategory->delete();
                        }
                    }

                    $testCategories = ClientOnlineTestCategory::where('client_institute_course_id',$instituteCourse->id)->where('client_id', $instituteCourse->client_id)->get();
                    if(is_object($testCategories) && false == $testCategories->isEmpty()){
                        foreach($testCategories as $category){
                            if(true == is_object($category->subcategories) && false == $category->subcategories->isEmpty()){
                                foreach($category->subcategories as $testSubcategory){
                                    if(true == is_object($testSubcategory->subjects) && false == $testSubcategory->subjects->isEmpty()){
                                        foreach($testSubcategory->subjects as $testSubject){
                                            if(true == is_object($testSubject->papers) && false == $testSubject->papers->isEmpty()){
                                                foreach($testSubject->papers as $paper){
                                                    if(true == is_object($paper->questions) && false == $paper->questions->isEmpty()){
                                                        foreach($paper->questions as $question){
                                                            ClientUserSolution::deleteClientUserSolutionsByQuestionId($question->id);
                                                            $question->delete();
                                                        }
                                                    }
                                                    ClientScore::deleteScoresByPaperId($paper->id);
                                                    $paper->deleteRegisteredPaper();
                                                    $paper->delete();
                                                }
                                            }
                                            $testSubject->delete();
                                        }
                                    }
                                    $testSubcategory->deleteSubCategoryImageFolder($request);
                                    $testSubcategory->delete();
                                }
                            }
                            $category->delete();
                        }
                    }

        			$instituteCourse->delete();
                    DB::connection('mysql2')->commit();
        			return Redirect::to('manageInstituteCourses')->with('message', 'Institute course deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('manageInstituteCourses');
    }
}