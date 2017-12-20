<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB;

class VirtualPlacementDrive extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'name', 'about','about_image','online_test','ti_basic','ti_machine_test','ti_puzzle','gd','pi','program_arrangement','program_arrangement_image'];

    /**
     *  create/update virtualPlacementDrive
     */
    protected static function addOrUpdateVirtualPlacementDrive(Request $request, $isUpdate = false){
        $placementId = InputSanitise::inputString($request->get('placement_id'));
        $name = $request->get('name');
        $about = $request->get('about');
        $onlineTest = $request->get('online_test');
        $tiBasic = $request->get('ti_basic');
        $tiMachineTest = $request->get('ti_machine_test');
        $tiPuzzle = $request->get('ti_puzzle');
        $gd = $request->get('gd');
        $pi = $request->get('pi');
        $programArrangement = $request->get('program_arrangement');

    	if( $isUpdate && !empty($placementId)){
    		$virtualPlacementDrive = static::find($placementId);
    		if(!is_object($virtualPlacementDrive)){
    			return Redirect::to('admin/manageVirtualPlacementDrive');
    		}
    	} else {
    		$virtualPlacementDrive = new static;
    	}
    	$virtualPlacementDrive->name = $name;
    	$virtualPlacementDrive->about = $about;
        $virtualPlacementDrive->online_test = $onlineTest;
    	$virtualPlacementDrive->ti_basic = $tiBasic;
        $virtualPlacementDrive->ti_machine_test = $tiMachineTest;
        $virtualPlacementDrive->ti_puzzle = $tiPuzzle;
        $virtualPlacementDrive->gd = $gd;
    	$virtualPlacementDrive->pi = $pi;
        $virtualPlacementDrive->program_arrangement = $programArrangement;
        if($request->exists('about_image')){
            $authorImage = $request->file('about_image')->getClientOriginalName();
            $virtualPlacementDriveFolderPath = "virtualPlacementDriveImages/";
            if(!is_dir($virtualPlacementDriveFolderPath)){
                mkdir($virtualPlacementDriveFolderPath, 0777, true);
            }
            $authorImagePath = $virtualPlacementDriveFolderPath ."/". $authorImage;
            if(file_exists($authorImagePath)){
                unlink($authorImagePath);
            } elseif(!empty($virtualPlacementDrive->id) && file_exists($virtualPlacementDrive->about_image)){
                unlink($virtualPlacementDrive->about_image);
            }
            $request->file('about_image')->move($virtualPlacementDriveFolderPath, $authorImage);
            $virtualPlacementDrive->about_image = $authorImagePath;
        }

        if($request->exists('program_arrangement_image')){
            $programImage = $request->file('program_arrangement_image')->getClientOriginalName();
            $virtualPlacementDriveFolderPath = "virtualPlacementDriveImages/";
            if(!is_dir($virtualPlacementDriveFolderPath)){
                mkdir($virtualPlacementDriveFolderPath, 0777, true);
            }
            $programImagePath = $virtualPlacementDriveFolderPath ."/". $programImage;
            if(file_exists($programImagePath)){
                unlink($programImagePath);
            } elseif(!empty($virtualPlacementDrive->id) && file_exists($virtualPlacementDrive->program_arrangement_image)){
                unlink($virtualPlacementDrive->program_arrangement_image);
            }
            $request->file('program_arrangement_image')->move($virtualPlacementDriveFolderPath, $programImage);
            $virtualPlacementDrive->program_arrangement_image = $programImagePath;
        }
    	$virtualPlacementDrive->save();
    	return $virtualPlacementDrive;
    }
}