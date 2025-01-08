<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use App\Rules\IsActive;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    //
    public function index(){
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request){

        $validated = $request->validate(['email' => ['required','email','exists:users',new IsActive]], getCommonValidationRuleMsgs());
        DB::beginTransaction();
        try{
            $user = User::where('email',$request->email)->first();
            if($user){
                $token = generateRandomString(64);
                $email_id = $request->email;

                $reset_password_url=route('resetPassword',[$token,encrypt($email_id)]);
                //dd($reset_password_url);
                DB::table('password_resets')->insert([
                    'email' => $email_id,
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);


                //Mail::to($email_id)->queue(new ResetPasswordMail($userDetails['name'],$userDetails['reset_password_url'], $subject));

                Mail::to($email_id)->send(new ResetPasswordMail($user,$reset_password_url));

                DB::commit();

                // Set Flash Message
                return redirect()->back()->with('success',trans('passwords.sent'))->withInput($request->only('email'));

            }else{
                // Set Flash Message
                return redirect()->back()->withErrors(['email' => trans('quickadmin.qa_invalid_email')])->withInput($request->only('email'));

            }

        }catch(\Exception $e){

            DB::rollBack();
         //dd($e->getMessage().'->'.$e->getLine());
            return redirect()->back()->with('error',trans('quickadmin.qa_reset_password_woops'))->withInput($request->only('email'));
        }
    }
}
