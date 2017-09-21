<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\Client;
use App\Models\Clientuser;
use App\Models\ClientAssignmentSubject;
use App\Models\ClientAssignmentTopic;
use App\Models\ClientInstituteCourse;
use App\Models\ClientAssignmentQuestion;
use App\Models\ClientAssignmentAnswer;

class ClientAssignmentController extends ClientBaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('client');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateAssignment = [
        'institute_course' => 'required|integer',
        'subject' => 'required|integer',
        'topic' => 'required',
    ];

    protected function show(){
        $assignments = ClientAssignmentQuestion::where('client_id', Auth::guard('client')->user()->id)->paginate();
        return view('client.assignment.list', compact('assignments'));
    }

    /**
     *  create assignment
     */
    protected function create(){
        $clientId = Auth::guard('client')->user()->id;
        $instituteCourses = ClientInstituteCourse::where('client_id', $clientId)->get();
        $topics = [];
        $subjects = [];
        $assignment = new ClientAssignmentQuestion;
        return view('client.assignment.create', compact('instituteCourses', 'subjects', 'topics', 'assignment'));
    }

    /**
     *  store assignment
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateAssignment);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        if(empty($request->get('question')) && empty($request->get('attached_link'))){
            return Redirect::to('manageAssignment')->with('message', 'please enter questions or select attachment.');
        }

        DB::connection('mysql2')->beginTransaction();
        try
        {
            $assignment = ClientAssignmentQuestion::addOrUpdateAssignment($request);
            if(is_object($assignment)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageAssignment')->with('message', 'Assignment created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageAssignment');
    }

    /**
     * edit assignment
     */
    protected function edit($subdomain, $id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $assignment = ClientAssignmentQuestion::find($id);

            if(is_object($assignment)){
                $instituteCourses = ClientInstituteCourse::where('client_id', $assignment->client_id)->get();
                $topics = ClientAssignmentTopic::getAssignmentTopicsBySubject($assignment->client_assignment_subject_id);;
                $subjects = ClientAssignmentSubject::getAssignmentSubjectsByCourse($assignment->client_institute_course_id);
                return view('client.assignment.create', compact('instituteCourses', 'subjects', 'topics', 'assignment'));
            }
        }
        return Redirect::to('manageAssignment');
    }

    /**
     * update assignment
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateAssignment);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        if(empty($request->get('question')) && empty($request->get('attached_link'))){
            return Redirect::to('manageAssignment')->with('message', 'please enter questions or select attachment.');
        }
        $assignmentId = InputSanitise::inputInt($request->get('assignment_id'));
        if(isset($assignmentId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $assignment = ClientAssignmentQuestion::addOrUpdateAssignment($request, true);
                if(is_object($assignment)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageAssignment')->with('message', 'Assignment updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageAssignment');
    }

    protected function checkAssignmentExist(Request $request){
        return ClientAssignmentQuestion::checkAssignmentExist($request);
    }

    protected function getAssignmentSubjectsByCourse(Request $request){
        return ClientAssignmentSubject::getAssignmentSubjectsByCourse($request->institute_course_id);
    }

    protected function studentsAssignment(){
        $assignment = '';
        $assignmentSubjects = [];
        $assignmentTopics = [];
        $assignmentUsers = [];

        $selectedAssignmentCourse  = Session::get('client_selected_assignment_course');
        $selectedAssignmentSubject  = Session::get('client_selected_assignment_subject');
        $selectedAssignmentTopic = Session::get('client_selected_assignment_topic');
        $selectedAssignmentStudent = Session::get('client_selected_assignment_student');

        if(!empty($selectedAssignmentCourse)){
            $assignmentSubjects = ClientAssignmentSubject::getAssignmentSubjectsByCourse($selectedAssignmentCourse);
            $assignmentUsers = Clientuser::searchStudentForAssignment($selectedAssignmentCourse);
        }
        if(!empty($selectedAssignmentSubject)){
            $assignmentTopics = ClientAssignmentTopic::getAssignmentTopicsBySubject($selectedAssignmentSubject);
        }
        if(!empty($selectedAssignmentTopic)){
            $assignment = ClientAssignmentQuestion::where('client_id', Auth::guard('client')->user()->id)
                    ->where('client_assignment_topic_id', $selectedAssignmentTopic)->first();
        }
        $instituteCourses = ClientInstituteCourse::join('client_assignment_questions', 'client_assignment_questions.client_institute_course_id', '=', 'client_institute_courses.id')
                ->where('client_assignment_questions.client_id', Auth::guard('client')->user()->id)
                ->select('client_institute_courses.id','client_institute_courses.*')
                ->groupBy('client_institute_courses.id')->get();
        return view('client.studentAssignment.studentsAssignment', compact('instituteCourses', 'assignmentSubjects', 'assignmentTopics', 'assignmentUsers', 'selectedAssignmentCourse', 'selectedAssignmentSubject', 'selectedAssignmentTopic', 'selectedAssignmentStudent', 'assignment'));
    }

    protected function searchStudentForAssignment(Request $request){
        $courseId = InputSanitise::inputInt($request->get('institute_course_id'));
        return Clientuser::searchStudentForAssignment($courseId);
    }

    protected function getAssignmentByTopicForStudent(Request $request){
        $results = [];
        $assignment = ClientAssignmentQuestion::where('client_id', Auth::guard('client')->user()->id)
                    ->where('client_assignment_topic_id', $request->topic)->first();
        if(is_object($assignment)){
            $results['id'] = $assignment->id;
            $results['question'] = mb_strimwidth($assignment->question, 0, 400, "...");
            $results['subject'] = $assignment->subject->name;
            $results['topic'] = $assignment->topic->name;
            $results['instituteCourse'] = $assignment->instituteCourse->name;

            Session::set('client_selected_assignment_course', $assignment->client_institute_course_id);
            Session::set('client_selected_assignment_subject', $assignment->client_assignment_subject_id);
            Session::set('client_selected_assignment_topic', $assignment->client_assignment_topic_id);
            Session::set('client_selected_assignment_student', $request->student);
        }
        return $results;
    }

    protected function assignmentRemark($subdomain, $id, $studentId){
        $id = InputSanitise::inputInt(json_decode($id));
        $studentId = InputSanitise::inputInt(json_decode($studentId));
        $assignment = ClientAssignmentQuestion::find($id);
        $student = Clientuser::find($studentId);
        $answers = ClientAssignmentAnswer::where('client_id', $student->client_id)->where('student_id', $student->id)->where('client_assignment_question_id', $assignment->id)->get();
        return view('client.studentAssignment.assignmentRemark', compact('assignment', 'instituteCourses', 'answers','student'));
    }

    protected function createAssignmentRemark(Request $request){
        $questionId   = InputSanitise::inputInt($request->get('assignment_question_id'));
        $studentId   = InputSanitise::inputInt($request->get('student_id'));
        if(empty($request->get('teacher_comment')) && empty($request->get('attached_link'))){
            return Redirect::to('studentsAssignment')->with('message', 'please enter remark or select attachment.');
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            ClientAssignmentAnswer::addAssignmentAnswer($request);
            DB::connection('mysql2')->commit();
            return Redirect::to('assignmentRemark/'.$questionId.'/'.$studentId)->with('message', 'Assignment updated successfully!');
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('studentsAssignment');
    }

    protected function delete(Request $request){
        $assignmentId = InputSanitise::inputInt($request->get('assignment_id'));
        $assignment = ClientAssignmentQuestion::find($assignmentId);
        if(is_object($assignment)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $answers = ClientAssignmentAnswer::where('client_assignment_question_id', $assignment->id)->where('client_id',Auth::guard('client')->user()->id)->get();
                if(is_object($answers) && false == $answers->isEmpty()){
                    foreach($answers as $answer){
                        $dir = dirname($answer->attached_link);
                        InputSanitise::delFolder($dir);
                        $answer->delete();
                    }
                }
                $dir = dirname($assignment->attached_link);
                InputSanitise::delFolder($dir);
                $assignment->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('manageAssignment')->with('message', 'Assignment deleted successfully!');

            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageAssignment');
    }
}