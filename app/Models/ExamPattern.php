<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;
use App\Models\PlacementArea;
use App\Models\PlacementCompany;

class ExamPattern extends Model
{
        public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['placement_area_id', 'placement_company_id', 'testing_area','no_of_question', 'duration'];
}
