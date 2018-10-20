<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\User;
use App\Models\UserData;
use App\Models\TestCategory;
use App\Models\TestSubCategory;
use App\Models\TestSubject;
use App\Models\TestSubjectPaper;
use App\Models\Skill;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class UserDataController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
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
    protected $validateCreateUserData = [
        'category' => 'required|string',
        'subcategory' => 'required|string',
        'subject' => 'required|string',
        'paper' => 'required|string',
        'user' => 'required|string',
        'year' => 'required|string',
        'month' => 'required|string',
        'company' => 'required|string',
        'education' => 'required|string',
    ];

    /**
     * show all UserData
     */
    protected function show(){
    	$userDatas = UserData::paginate();
    	return view('userData.list', compact('userDatas'));
    }

    /**
     * show UI for create userData
     */
    protected function create(){
    	$userData = new UserData;
        $testCategories = TestCategory::getCompanyCategoriesAssociatedWithQuestion();
        $testSubCategories = [];
        $testSubjects = [];
        $papers = [];
        $years = range(0, 30);
        $months = range(0, 12);
        $skills = Skill::all();
        $expArr = [];
        $skillArr = [];
        $user = new User;
    	return view('userData.create', compact('userData','testCategories','testSubCategories','testSubjects','papers','years','months','skills','expArr','skillArr', 'user'));
    }

    /**
     *  store userData
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateUserData);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $userData = UserData::addOrUpdateUserData($request);
            if(is_object($userData)){
                DB::commit();
                return Redirect::to('admin/manageUserData')->with('message', 'UserData created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('admin/manageUserData');
    }

    /**
     * edit userData
     */
    protected function edit($id){
    	$userDataId = InputSanitise::inputInt(json_decode($id));
    	if(isset($userDataId)){
    		$userData = UserData::find($userDataId);
    		if(is_object($userData)){
    			$testCategories = TestCategory::getCompanyCategoriesAssociatedWithQuestion();
                $testSubCategories = TestSubCategory::getSubcategoriesByCategoryIdForAdmin($userData->category_id);
                $testSubjects = TestSubject::getSubjectsByCatIdBySubcatidForAdmin($userData->category_id,$userData->sub_category_id);
                $papers = TestSubjectPaper::getSubjectPapersByCategoryIdBySubCategoryIdBySubjectIdForAdmin($userData->category_id,$userData->sub_category_id,$userData->subject_id);
                $user = User::find($userData->user_id);
                $years = range(0, 30);
                $months = range(0, 12);
                $skills = Skill::all();
                $expArr = explode(',',$userData->experiance);
                $skillArr = explode(',',$userData->skill_ids);
                return view('userData.create', compact('userData','testCategories','testSubCategories','testSubjects','papers','user','years','months','skills','expArr','skillArr'));
    		}
    	}
		return Redirect::to('admin/manageUserData');
    }

    /**
     * update userData
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateUserData);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $userDataId = InputSanitise::inputInt($request->get('user_data_id'));
        if(isset($userDataId)){
            DB::beginTransaction();
            try
            {
                $userData = UserData::addOrUpdateUserData($request, true);
                if(is_object($userData)){
                    DB::commit();
                    return Redirect::to('admin/manageUserData')->with('message', 'UserData updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageUserData');
    }

    /**
     * delete userData
     */
    protected function delete(Request $request){
    	$userDataId = InputSanitise::inputInt($request->get('user_data_id'));
    	if(isset($userDataId)){
    		$userData = UserData::find($userDataId);
    		if(is_object($userData)){
                DB::beginTransaction();
                try
                {
                    if(!empty($userData->resume) && is_file($userData->resume)){
                        unlink($userData->resume);
                    }
        			$userData->delete();
                    DB::commit();
                    return Redirect::to('admin/manageUserData')->with('message', 'UserData deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
		return Redirect::to('admin/manageUserData');
    }

    protected function verifyUserByEmailIdByPaperId(Request $request){
        return User::verifyUserByEmailIdByPaperId($request->get('email'),$request->get('paper'));
    }
}
