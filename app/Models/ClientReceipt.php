<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;

class ClientReceipt extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['offline_receipt_by', 'offline_address', 'offline_gstin','offline_cin','offline_pan','is_offline_gst_applied','is_same_details','online_receipt_by', 'online_address', 'online_gstin','online_cin','online_pan','is_online_gst_applied','hsn_sac','client_id'];

    /**
     *  add/update
     */
    protected static function addOrUpdateClientReceipt( Request $request, $isUpdate=false){
        $offLineReceiptBy = InputSanitise::inputString($request->get('offline_receipt_by'));
        $offLineAddress = InputSanitise::inputString($request->get('offline_address'));
        $offLineGstin = InputSanitise::inputString($request->get('offline_gstin'));
        $offLineCin = InputSanitise::inputString($request->get('offline_cin'));
        $offLinePan = InputSanitise::inputString($request->get('offline_pan'));

        $onLineReceiptBy = InputSanitise::inputString($request->get('online_receipt_by'));
        $onLineAddress = InputSanitise::inputString($request->get('online_address'));
        $onLineGstin = InputSanitise::inputString($request->get('online_gstin'));
        $onLineCin = InputSanitise::inputString($request->get('online_cin'));
        $onLinePan = InputSanitise::inputString($request->get('online_pan'));

        $isOnlineGstApplied   = InputSanitise::inputInt($request->get('is_online_gst_applied'));
        $isOfflineGstApplied   = InputSanitise::inputInt($request->get('is_offline_gst_applied'));
        $isSameDetails   = InputSanitise::inputInt($request->get('is_same_details'));

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
        $receipt->offline_receipt_by = $offLineReceiptBy;
        $receipt->offline_address = $offLineAddress;
        $receipt->offline_gstin = $offLineGstin;
        $receipt->offline_cin = $offLineCin;
        $receipt->offline_pan = $offLinePan;
        $receipt->is_offline_gst_applied = $isOfflineGstApplied;
        $receipt->is_same_details = $isSameDetails;
        $receipt->online_receipt_by = $onLineReceiptBy;
        $receipt->online_address = $onLineAddress;
        $receipt->online_gstin = $onLineGstin;
        $receipt->online_cin = $onLineCin;
        $receipt->online_pan = $onLinePan;
        $receipt->is_online_gst_applied = $isOnlineGstApplied;
        $receipt->hsn_sac = $hsnSac;
        $receipt->client_id = Auth::guard('client')->user()->id;
        $receipt->save();
        return $receipt;
    }
}
