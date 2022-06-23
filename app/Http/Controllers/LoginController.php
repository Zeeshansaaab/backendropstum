<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\PasswordGeneratorMail;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
class LoginController extends Controller
{
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
            $data['access_token'] = $user->createToken('access_token')->plainTextToken;
            return response()->json([
                'status'=>200,
                'message'=>"Registration Successfull",
                'data'=>$data
            ]);
        }catch(Exception $e){
            DB::rollback();
            return response()->json([
                'message'=>$e->getMessage(),
            ],JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
