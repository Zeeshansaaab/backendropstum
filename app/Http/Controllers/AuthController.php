<?php

namespace App\Http\Controllers;

use App\Mail\PasswordGeneratorMail;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
class AuthController extends Controller
{
    use AuthenticatesUsers;
    public function register(Request $request){
        try{    
            DB::beginTransaction();
            $request->validate([
                'name'=>'required|string|min:5',
                'email'=>'required|string|unique:users',
            ]);

            $pass = \random_int(100000,1000000000);
            $user =  User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($pass),
            ]);

            Mail::to($request->input('email'))->send(new PasswordGeneratorMail($user,$pass));
            DB::commit();
            return response()->json([
                'status'=>200,
                'message'=>"Registration Successfull check your email",
            ]);
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'message'=>$e->getMessage(),
            ],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function login(Request $request)
    {
        try{    
            DB::beginTransaction();
            $request->validate([
                'password'=>'required|string|min:8',
                'email'=>'required|string',
            ]);
            $user = User::where('email',$request->input('email'))->first();
            
            if($user && Hash::check($request->password,$user->password)){
                $data['access_token'] = $user->createToken('access_token')->plainTextToken;       
                return response()->json([
                    'status'=>200,
                    'message'=>"Login Successfull",
                    'data'=>isset($data) ? $data : null
                ]);
            }
            return response()->json([
                'status'=>200,
                'message'=>"Login Successfull!!",
            ],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            DB::commit();
            

        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'message'=>$e->getMessage(),
            ],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function dashboard(){
        try{    
            DB::beginTransaction();
            $user = User::count();

            return response()->json([
                'status'=>200,
                'message'=>"Login Successfull!!",
                'data'=>$user
            ]);
            DB::commit();
            

        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'message'=>$e->getMessage(),
            ],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
