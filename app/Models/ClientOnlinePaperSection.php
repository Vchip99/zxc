<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientInstituteCourse;

class ClientOnlinePaperSection extends Model
{
    protected $connection = 'mysql2';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'duration', 'category_id', 'sub_category_id', 'subject_id', 'paper_id','client_id', 'client_institute_course_id'];

    protected static function paperSectionsByInstituteCourseIdByPaperId($instituteCourse,$paperId){
        return static::where('client_institute_course_id', $instituteCourse)
                ->where('client_id', Auth::guard('client')->user()->id)
                ->where('paper_id', $paperId)->get();
    }

    protected static function paperSectionsByPaperId($paperId){
        return static::where('client_id', Auth::guard('clientuser')->user()->client_id)
                ->where('paper_id', $paperId)->get();
    }
}
