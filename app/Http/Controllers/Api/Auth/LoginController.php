<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Rules\IsActive;
use Illuminate\Http\Request;

class LoginController extends Controller
{

    public function register(Request $request){

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|max:255|email|unique:users,email',
            'phone' => 'required|min:10|integer|unique:users,phone',
            'password'  => 'required|string|min:4|required_with:confirmed_password|same:confirmed_password',
            'confirmed_password' => 'required|min:4'
        ]);

        DB::beginTransaction();
        try {
            $inputs             = $request->all();
            $inputs['password'] = bcrypt($inputs['password']);
            $user = User::create($inputs);
            $user->roles()->sync($request->input('roles', [config('app.roleid.admin')]));

            $accessToken = $user->createToken(config('auth.api_token_name'))->plainTextToken;
            DB::commit();
            //Success Response Send
            $responseData = [
                'status'            => true,
                'message'           => 'You have register successfully!',
                'userData'          => [
                    'id'            => $user->id,
                    'name'          => $user->name ?? '',
                    'username'      => $user->username ?? '',
                    'email'         => $user->email ?? '',
                    'phone'         => $user->phone ?? '',
                    'address'       => $user->address ?? '',
                    'profile_image' => $user->profile_image_url ?? '',
                    'pin'           =>  $user->device? $user->device->pin : '',
                ],
                'remember_me_token' => $user->remember_token,
                'access_token'      => $accessToken
            ];
            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 401);
        }
    }

    public function login(Request $request){
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            // 'username'    => ['required','string',new IsActive],
            'email'     => 'required|max:255|email',
            'password'  => 'required|min:4',
        ]);

        if($validator->fails()){
            //Error Response Send
            $responseData = [
                'status'        => false,
                'validation_errors' => $validator->errors(),
            ];
            return response()->json($responseData, 401);
        }

        DB::beginTransaction();
        try {
            $remember_me = !is_null($request->remember) ? true : false;
            $credentialsOnly = [
                'email'    => $request->email,
                'password' => $request->password,
            ];

            if(Auth::attempt($credentialsOnly, $remember_me)){
                $user = Auth::user();
                $accessToken = $user->createToken(config('auth.api_token_name'))->plainTextToken;
                DB::commit();
                //Success Response Send
                $responseData = [
                    'status'            => true,
                    'message'           => 'You have logged in successfully!',
                    'userData'          => [
                        'id'           => $user->id,
                        'name'   => $user->name ?? '',
                        'username'    => $user->username ?? '',
                        'email'    => $user->email ?? '',
                        'phone'    => $user->phone ?? '',
                        'address'    => $user->address ?? '',
                        'profile_image'=> $user->profile_image_url ?? '',
                        'pin'=>  $user->device? $user->device->pin : '',
                    ],
                    'remember_me_token' => $user->remember_token,
                    'access_token'      => $accessToken
                ];
                return response()->json($responseData, 200);

            } else{
                //Error Response Send
                $responseData = [
                    'status'        => false,
                    'error'         => trans('messages.wrong_credentials'),
                ];
                return response()->json($responseData, 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            //dd($e->getMessage().'->'.$e->getLine());
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 401);
        }
    }

    public function LoginWithPin(Request $request){

        $validator = Validator::make($request->all(), ['pin'    => ['required','numeric'/*,'exists:devices'*/,'digits:4']]);

        if($validator->fails()){
            $responseData = [
                'status'        => false,
                'validation_errors' => $validator->errors(),
            ];
            return response()->json($responseData, 401);
        }

        try{

            if (Auth::check()) {
                $user = auth()->user();

                if(!$user){
                    $responseData = [
                        'status'        => false,
                        'error'         => trans('messages.wrong_credentials'),
                    ];
                    return response()->json($responseData, 401);
                }
                elseif($request->pin !== $user->device->pin){
                    $responseData = [
                        'status'        => false,
                        'error'         => trans('messages.invalid_pin'),
                    ];
                    return response()->json($responseData, 401);
                }
                else{
                    $responseData = [
                        'status'            => true,
                        'message'           => 'You have logged in successfully!',
                        'userData'          => [
                            'id'           => $user->id,
                            'name'   => $user->name ?? '',
                            'username'    => $user->username ?? '',
                            'email'    => $user->email ?? '',
                            'phone'    => $user->phone ?? '',
                            'address'    => $user->address ?? '',
                            'profile_image'=> $user->profile_image_url ?? '',
                        ],
                    ];
                    return response()->json($responseData, 200);
                }
            }
        }catch (\Exception $e) {
            //dd($e->getMessage().'->'.$e->getLine());
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 401);
        }

    }

    public function forgotPassword(Request $request){
        $validator = Validator::make($request->all(), ['email' => ['required','email','exists:users',new IsActive]], getCommonValidationRuleMsgs());

        if($validator->fails()){
            //Error Response Send
            $responseData = [
                'status'        => false,
                'validation_errors' => $validator->errors(),
            ];
            return response()->json($responseData, 401);
        }

        DB::beginTransaction();
        try {
            //$token = generateRandomString(64);
            $token = rand(100000, 999999);
            $email_id = $request->email;
            $user = User::where('email', $email_id)->first();
            if(!$user){
                $responseData = [
                    'status'        => false,
                    'error'         => trans('messages.invalid_email'),
                ];
                return response()->json($responseData, 401);
            }

            DB::table('password_resets')->insert([
                'email'         => $email_id,
                'token'         => $token,
                'created_at'    => Carbon::now()
            ]);

            $user->otp = $token;
            $user->subject = "Reset Password OTP";
            $user->expiretime = '2 Minutes';
            //dd($user);
            $user->sendPasswordResetOtpNotification($request, $user);
            DB::commit();
            //Success Response Send
            $responseData = [
                'status'        => true,
                'otp_time_allow' => config('auth.passwords.users.expire').' Minutes',
                'otp' => $token,
                'message'         => trans('messages.otp_sent_email'),
            ];
            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage().'->'.$e->getLine());
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 401);
        }
    }

    public function verifyOtp(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email|exists:password_resets,email',
            'otp'   => 'required|numeric|min:6'
        ]);
        if ($validation->fails()) {
            $responseData = [
                'status'        => false,
                'validation_errors' => $validation->errors(),
            ];
            return response()->json($responseData, 401);
        }
        $email = $request->email;
        $otpToken = $request->otp;

        $passwordReset = DB::table('password_resets')->where('token', $otpToken)
                ->where('email', $email)
                ->orderBy('created_at','desc')
                ->first();

        if (!$passwordReset){
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.invalid_otp'),
            ];
            return response()->json($responseData, 401);
        }


        if (Carbon::parse($passwordReset->created_at)->addMinutes(config('auth.passwords.users.expire'))->isPast()) {
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.expire_otp'),
            ];
            return response()->json($responseData, 401);
        }

        $responseData = [
            'status'        => true,
            'token'         => encrypt($otpToken),
            'message'         => trans('messages.verified_otp'),
        ];
        return response()->json($responseData, 200);
    }

    public function resetPassword(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'token' => 'required',
            'email'     => 'required|email|exists:password_resets,email',
            'password'  => 'required|string|min:4',
            'confirmed_password' => 'required|string|min:4',
        ]);

        if ($validation->fails()) {
            $responseData = [
                'status'        => false,
                'validation_errors' => $validation->errors(),
            ];
            return response()->json($responseData, 401);
        }
        $token = decrypt($request->token);
        $passwordReset = DB::table('password_resets')->where('token',$token)
                ->where('email', $request->email)
                ->orderBy('created_at','desc')
                ->first();

        if (!$passwordReset)
        {
            $responseData = [
                'status'        => false,
                'validation_errors' => trans('messages.invalid_token_email'),
            ];
            return response()->json($responseData, 401);
        }

        $user = User::where('email', $passwordReset->email)->first();
        if (!$user){
            $responseData = [
                'status'        => false,
                'validation_errors' => trans('messages.invalid_email'),
            ];
            return response()->json($responseData, 401);
        }

        $user->password = bcrypt($request->password);
        $user->save();
        DB::table('password_resets')->where('email',$passwordReset->email)->delete();
        $responseData = [
            'status'        => true,
            'message'         => trans('passwords.reset'),
        ];
        return response()->json($responseData, 200);
    }

}
