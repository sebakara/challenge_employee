<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use App\Models\OTP;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use app\Mail\Sendemail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('login');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $validate = Validator::make($request->all(),
            [
                'first_name'=>'required',
                'last_name'=>'required',
                'email'=>'required|unique:users,email',
                'password'=>'required|confirmed|min:6',
                'phone_number'=>'required',
            ]);
            if($validate->fails()){
                return view('login')->with('error',$validate->error());
            }
            $user = User::create(
                [
                    'first_name'=>$request->first_name,
                    'last_name'=>$request->last_name,
                    'email'=>$request->email,
                    'password'=> Hash::make($request->password),
                ]
            );
            $empId = 'ABC'.$user->id;
            $employee = Employee::create(
                [
                    'user_id'=>$user->id,
                    'emp_id'=>$empId,
                    'phone_number'=>$request->phone_number
                ]
            );
            $data = ["messages"=>"Welcome ".$request->first_name." ".$request->last_name,"empl_nbr"=>$empId];
            $to_name = $request->first_name." ".$request->last_name;
            $to_email = $request->email;
            Mail::send("success", $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
            ->subject("Welcome");
            $message->from(env("MAIL_FROM_ADDRESS"));
            });

            return response()->json([
                'status'=>true,
                'message'=>'user created successfully',
                'token' => $user->createToken('-AuthToken')->plainTextToken
            ],200);

        }
        catch(\Throwable $th){
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage()
            ],500);
        }
    }
    // update
    public function update(Request $request){
        try{
            $user = $request->user();
            $request->validate([
                'first_name'=>'required',
                'last_name'=>'required',
                'email'=>'required|email|unique:users,email,'.$user->id,
                'phone_number'=>'required',
            ]);

            $user->first_name =$request->first_name;
            $user->last_name=$request->last_name;
            $user->email=$request->email;
            $user->save();

            $employee = Employee::where('user_id',$user->id)->first();
            $employee->phone_number = $request->phone_number;
            $employee->save();

            return response()->json([
                'status' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);

        }
            catch(\Throwable $th){
                return response()->json([
                    'status'=>false,
                    'message'=>$th->getMessage()
                ],500);
            }

        }
    // login function

    public function login(Request $request){
        try{
            $request->validate([
                'email'=>'required|email',
                'password'=>'required'
            ]);

            if(!Auth::attempt($request->only(['email','password']))){
                return view('login')->with('error','Invalid credentials');
            }
            $user = User::where('email',$request->email)->first();
            return response()->json([
                'status'=>true,
                'message'=>'success',
                'data'=>$user,
                'token' => $user->createToken('-AuthToken')->plainTextToken
            ],200);
        }
        catch(\Throwable $th){
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage()
            ],500);
        }
    }
// logout
public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();
    return response()->json([
        'status'=>true,
        'message' => 'Logged out successfully'
    ],200);
}
    public function me(Request $request)
    {

    $user = $request->user();
    $employee = $user->employee;
    return response()->json([
        'status' => true,
        'message' => 'success',
        'user' => $user,
    ], 200);
    }

    // return register page

    // forgot password
    public function forgotPassword(Request $request){
        try{
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            $user = User::where('email', $request->email)->first();

            $otp = rand(100000, 999999);
            OTP::create([
                'email'=>$request->email,
                'otp' => $otp
            ]);

            $data = ["messages"=>$user->first_name." ".$user->last_nam." your OTP ".$otp];
            $to_name = $user->first_name." ".$user->last_name;
            $to_email = $user->email;
            Mail::send("forget_password", $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
            ->subject("Forget Password");
            $message->from(env("MAIL_FROM_ADDRESS"));
            });
            return response()->json([
                'status'=>true,
                'message'=>'success, check your email for OTP',
            ],200);
        }
        catch(\Throwable $th){
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage()
            ],500);
        }

    }

    // reset password
    public function resetPassword(Request $request)
    {
        try{
            $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
            'password' => 'required|confirmed|min:6',
        ]);
        $reset = OTP::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('created_at', '>=', now()->subHours(24))
            ->first();

        if ($reset) {
            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();
            OTP::where('email', $request->email)->delete();

            return response()->json([
                'status'=>true,
                'message'=>'Password reset successful',
            ],200);
        } else {
            return response()->json([
                'status'=>false,
                'message' => 'Invalid or expired OTP',
            ], 422);
        }
    }
    catch(\Throwable $th){
        return response()->json([
            'status'=>false,
            'message'=>$th->getMessage()
        ],500);
    }

}


}
