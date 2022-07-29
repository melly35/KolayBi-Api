<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;  
use Illuminate\Support\Facades\Auth; 
use Validator; 
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    /**
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|regex:/^[a-zA-Z-ığüşöçİĞÜŞÖÇ]+(?:\s[a-zA-Z-ığüşöçİĞÜŞÖÇ]+)+$/',
            'email' => 'required|email|unique:users|max:255', 
            'password' => 'required|max:255', 
        ]); 

        if ($validator->fails()) {
            return response([ 
                'success' => false,
                'msg' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
        else { 
            $User = new User;

            $data = $request->all();

            $User->name = $data['name'];
            $User->email = $data['email'];
            $User->password = bcrypt($data['password']);  
            
            $User->save();
 
            $response = [
                'success' => true, 
                'msg' => 'Register Successfully',
                'user' => $User, 
            ];
            
            return response($response, Response::HTTP_OK);
        } 
    }


    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user();  
            
            $response = [
                'success' => true, 
                'msg' => 'Login Successfully',
                'user' => [
                    'id' => $user->id, 
                    'name_surname' => $user->name,
                    'email' => $user->email, 
                    'accessToken' => $user->createToken($request->email)->plainTextToken,
                ],
            ];

            return response($response, Response::HTTP_OK);
        } 
        else{ 
            return response([ 
                'success' => false,
                'msg' => 'Email or Password Invalid',
            ], Response::HTTP_UNAUTHORIZED); 
        } 
    }

}
