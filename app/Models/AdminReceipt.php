<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;

class AdminReceipt extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['receipt_by', 'address', 'gstin','cin','pan','is_gst_test_applied','is_gst_course_applied','is_gst_vkit_applied','hsn_sac'];

    /**
     *  add/update
     */
    protected static function addOrUpdateAdminReceipt( Request $request, $isUpdate=false){
        $receiptBy = InputSanitise::inputString($request->get('receipt_by'));
        $address = InputSanitise::inputString($request->get('address'));
        $gstin = InputSanitise::inputString($request->get('gstin'));
        $cin = InputSanitise::inputString($request->get('cin'));
        $pan = InputSanitise::inputString($request->get('pan'));

        $isGstTestApplied   = InputSanitise::inputInt($request->get('is_gst_test_applied'));
        $isGstCourseApplied   = InputSanitise::inputInt($request->get('is_gst_course_applied'));
        $isGstVkitApplied   = InputSanitise::inputInt($request->get('is_gst_vkit_applied'));

        $hsnSac = InputSanitise::inputString($request->get('hsn_sac'));
        $receiptId   = InputSanitise::inputInt($request->get('receipt_id'));

        if( $isUpdate && isset($receiptId)){
            $receipt = static::find($receiptId);
            if(!is_object($receipt)){
            	return 'false';
            }
        } else{
            $receipt = new static;
        }
        $receipt->receipt_by = $receiptBy;
        $receipt->address = $address;
        $receipt->gstin = $gstin;
        $receipt->cin = $cin;
        $receipt->pan = $pan;
        $receipt->is_gst_test_applied = $isGstTestApplied;
        $receipt->is_gst_course_applied = $isGstCourseApplied;
        $receipt->is_gst_vkit_applied = $isGstVkitApplied;
        $receipt->hsn_sac = $hsnSac;
        $receipt->save();
        return $receipt;
    }
}
