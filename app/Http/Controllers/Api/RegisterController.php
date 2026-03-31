<?php

namespace App\Http\Controllers\API;
require __DIR__.'/BaseController.php';
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\GeneralTrait;

class RegisterController extends BaseController
{
     use GeneralTrait;
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
       try{
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
      
        $input['uuid']= Str::uuid();
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;
        return $this->sendResponse($success, 'User register successfully.');

        }catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    } 
}
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        try{
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->plainTextToken; 
            $success['name'] =  $user->name;
            return $this->sendResponse($success, 'User login successfully.');
        }else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    } 

    }
     /** 
    * @param Request $request
    * @return JsonResponse
    */
    public function logout(Request $request): JsonResponse
    {
        try{
        $user = Auth::guard('sanctum')->user();
        if ($user) {
           
    
            $user->tokens()->delete();

            return $this->sendResponse([], 'User logged out successfully.');
        } else {
    
            return $this->sendError('Unauthorized', ['error' => 'You are not currently logged in.']);
            
        }}catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
        } 
    }
}