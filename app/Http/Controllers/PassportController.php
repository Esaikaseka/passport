<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\user;
use Excel;


class PassportController extends Controller
{
    public function register(Request $request){
        $this->validate($request,[
            'name'=>"required|min:3",
            'email'=>"required|email|unique:users",
            'password'=>"required|min:6",
        ]);
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->passowrd),
        ]);

        $token = $user->createToken('Welcome')->accessToken;
        
        return response()->json(['token'=>$token],200);
    }
    public function login(Request $request){
        $credentials = [
            'email'=>$request->email,
            'password'=>$request->passowrd,
        ];
        if(auth()->attempt($credentials)){
            $token=auth()->user()->createToken('Welcome')->accessToken;
            return response()->json(['token'=>$token],200);

        }else{
            response()->json(['error'=>'UnAuthorized'],401);
        } 
    }
    public function details(){
        return response()->json(['user'=>auth()->user()],200);
    }

   
    public function update(Request $request,$user){
          if(!$user){
            return response()->json([
                'success'=>false,
                'data'=>'user'.$user.'not found',
           ],500);
          }

          $updated=$user->fill($request->all()->save());

          if($updated){
            return response()->json([
                'success'=>true,
                'data'=>'user updated successfully',
           ],200);
          }else{
            return response()->json([
                'success'=>false,
                'message'=>'Product could not be updated',
           ],500);
          }
    }
    public function destroy($user){
        

        if(!$user){
            return response()->json([
                'success'=>false,
                'message'=>'User'.$user.'not found',
           ],400);
        }
        if($user->delete()){
            return response()->json([
                'success'=>false,
                'message'=>'user deleted successfully',
           ],);
        }
        else{
            return response()->json([
                'success'=>false,
                'message'=>'user could not be deleted',
           ],500);
        }
    }
    public function importForm()
    {
        return view('import-form');
    }

    public function import(Request $request){
                Excel::import(new user,$request->file);
                return "Record are imported successfully";
    }
}
