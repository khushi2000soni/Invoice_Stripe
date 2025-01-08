<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    //
    public function showform(Request $request, $token,$email){

        return view('auth.reset-password')->with(['token' => $token, 'email' => $request->email]);
    }

    public function resetpass(Request $request){
        //dd($request->all());

        $email_id = decrypt($request->email);

        $validated = $request->validate([
            'token' => 'required',
            'email' => 'required|string',
            'password' => 'required|string|min:4|confirmed',
            'password_confirmation' => 'required|string|min:4',

        ], getCommonValidationRuleMsgs());

        $updatePassword = DB::table('password_resets')->where(['email' => $email_id,'token' => $request->token])->first();

        if(!$updatePassword){

            return redirect()->back()->with('error', trans('passwords.token'))->withInput($request->all());

        }else{
            $isActive = User::where('email',$email_id)->pluck('is_active');

            if($isActive){
                $user = User::where('email', $email_id)
                ->update(['password' => Hash::make($request->password)]);

                DB::table('password_resets')->where(['email'=> $email_id])->delete();

                // Set Flash Message

                return redirect()->route('login')->with('success',trans('passwords.reset'));
            }else{

                return redirect()->back()->withErrors(['error' => trans('passwords.suspened')])->withInput($request->all());
            }

        }

       // return redirect()->route('auth.login');
    }
}
