<?php

use App\Models\Order;
use App\Models\Setting;
use App\Models\Uploads;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str as Str;
use App\Events\InvoiceUpdated;

if (!function_exists('dispatchInvoiceUpdatedEvent')) {
    function dispatchInvoiceUpdatedEvent() {
        event(new InvoiceUpdated());
    }
}

if (!function_exists('getCommonValidationRuleMsgs')) {
	function getCommonValidationRuleMsgs()
	{
		return [
            'currentpassword.required'=>'The current Password is required.',
			'password.required' => 'The new Password is required.',
			'password.min' => 'The new Password must be at least 8 characters',
			'password.different' => 'The new Password and current password must be different.',
			'password.confirmed' => 'The Password confirmation does not match.',
			'password_confirmation.required' => 'The new password confirmation is required.',
			'password_confirmation.min' => 'The new password confirmation must be at least 8 characters',
			'email.required' => 'Please enter email address.',
			'email.email' => 'Email is not valid. Enter email address for example test@gmail.com',
            'email.exists' => "Please Enter Valid Registered Email!",
            'password_confirmation.same' => 'The confirm password and new password must match.'
		];
	}
}

if (!function_exists('generateRandomString')) {
	function generateRandomString($length = 20) {

		$randomString = Str::random($length);

		return $randomString;
	}
}

if (!function_exists('getWithDateTimezone')) {
	function getWithDateTimezone($date) {
        $newdate= Carbon::parse($date)->setTimezone(config('app.timezone'))->format('d-m-Y H:i:s');
		return $newdate;
	}
}

if (!function_exists('uploadImage')) {
	/**
	 * Upload Image.
	 *
	 * @param array $input
	 *
	 * @return array $input
	 */
	function uploadImage($directory, $file, $folder, $type="profile", $fileType="jpg",$actionType="save",$uploadId=null,$orientation=null)
	{
		$oldFile = null;

        if($actionType == "save"){

			$upload               		= new Uploads;
		}else{

			$upload               		= Uploads::find($uploadId);
			$oldFile = $upload->file_path;

		}
        $upload->file_path      	= $file->store($folder, 'public');
		$upload->extension      	= $file->getClientOriginalExtension();
		$upload->original_file_name = $file->getClientOriginalName();
		$upload->type 				= $type;
		$upload->file_type 			= $fileType;
		$upload->orientation 		= $orientation;
		$response             		= $directory->uploads()->save($upload);

        // delete old file
        if ($oldFile) {
            Storage::disk('public')->delete($oldFile);
        }

		return $upload;
	}
}

if (!function_exists('deleteFile')) {
	/**
	 * Destroy Old Image.	 *
	 * @param int $id
	 */
	function deleteFile($upload_id)
	{
		$upload = Uploads::find($upload_id);
		Storage::disk('public')->delete($upload->file_path);
		$upload->delete();
		return true;
	}
}


if (!function_exists('getSetting')) {
	function getSetting($key)
	{
		$result = null;
		$setting = Setting::where('key',$key)->where('status',1)->first();
		if($setting->type == 'image'){
			$result = $setting->image_url;
		}elseif($setting->type == 'video'){
			$result = $setting->video_url;
		}else{
			$result = $setting->value;
		}
		return $result;
	}
}

// if (!function_exists('generateInvoiceNumber')) {
//     function generateInvoiceNumber($orderId) {
//         dd($orderId);
//         $timeframe = now()->format('M-y'); // Get the current month abbreviation
//         $invoiceNumber = strtoupper($timeframe) . '-' . str_pad($orderId, 4, '0', STR_PAD_LEFT);
//         return $invoiceNumber;
//     }
// }

if (!function_exists('generateInvoiceNumber')) {
    function generateInvoiceNumber($orderId) {        
        try {
            $currentMonthYear = now()->format('M-y'); // Get the current month and year, e.g., Mar24
            $lastInvoiceNumber = Order::where('invoice_number', 'like', $currentMonthYear . '%')->orderBy('created_at', 'desc')->lockForUpdate()->first();
            if ($lastInvoiceNumber) {
                // Extract the numeric part of the last invoice number
                $lastNumber = (int)substr($lastInvoiceNumber->invoice_number, -4);
                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '0001';
            }
            // Create the new invoice number
            $invoiceNumber = strtoupper($currentMonthYear) . '-' . $nextNumber;            
            return $invoiceNumber;
        } catch (\Exception $e) {            
            throw $e;
        }
    }
}

if (!function_exists('generateInvoicePdf')) {
    function generateInvoicePdf($order,$type=null) {
        $order = Order::with('orderProduct.product')->findOrFail($order);
        $pdfFileName = 'invoice_' . $order->invoice_number . '.pdf';
        $pdf = PDF::loadView('admin.order.pdf.invoice-pdf', compact('order','type'));
        $pdfContent = $pdf->output();
        // Create a temporary file to save the PDF
        $customer_name = $order->customer->full_name;
        $pdfFileName = $order->invoice_number.'_'.$customer_name . '.pdf';

        //return $pdf->download($pdfFileName);
        return ['pdfContent' => $pdfContent,'pdfFileName' => $pdfFileName];
    }
}


if (!function_exists('str_limit_custom')) {
    /**
     * Limit the number of characters in a string.
     *
     * @param  string  $value
     * @param  int  $limit
     * @param  string  $end
     * @return string
     */
    function str_limit_custom($value, $limit = 100, $end = '...')
    {
        return \Illuminate\Support\Str::limit($value, $limit, $end);
    }
}


/// Function for handling Data Type of a number , if 50.00 then return 50 , if 50.64 then return 50.64
/// It will return 2 digit after point

if(!function_exists('handleDataTypeTwoDigit')){
    function handleDataTypeTwoDigit($number){
        $number = $number == intval($number) ? intval($number) : number_format($number, 2, '.', '');

        return $number;
    }
}

/// It will return 3 digit after point
if (!function_exists('handleDataTypeThreeDigit')) {
    function handleDataTypeThreeDigit($number) {
        $number = $number == intval($number) ? intval($number) : number_format($number, (fmod($number, 1) !== 0) ? 3 : 0, '.', '');

        return $number;
    }
}

/// Calculate Category Amount Ratio's percentage

if (!function_exists('CategoryAmountPercent')) {
    function CategoryAmountPercent($amount , $totalAmount) {
        if ($totalAmount == 0) {
            return '0.00%';
        }
        $percentShare = ($amount / $totalAmount) * 100;
        return number_format($percentShare, 2) . '%';
    }
}


?>
