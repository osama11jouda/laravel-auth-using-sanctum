<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\Admin;
use App\Rules\MatchPassword;
use App\Traits\GeneralTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    use GeneralTrait;

    public function register(Request $request): JsonResponse
    {
        $rules = [
            'username'=>['required','min:6','string'],
            'email'=>['required','email','unique:admins,email'],
            'password'=>['required','confirmed',Password::min(6)->letters()->mixedCase()->symbols()->numbers()->uncompromised()],
        ];
        try {
            $validator = Validator::make($request->all(),$rules);
            if($validator->fails())
            {
                return $this->returnValidationError($validator);
            }
            $admin = Admin::create([
                'username'=>$request->username,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
            ]);
            $token = $admin->createToken('admin_token',['admin'])->plainTextToken;
            $admin['token'] = $token;
            return $this->returnData('admin',$admin,'new admin registered successfully. ');
        }catch (\Exception $e)
        {
            return $this->returnError($e->getMessage(),$e->getCode());
        }

    }

    public function login(Request $request): JsonResponse
    {
        $rules = [
            'email'=>['required','email','exists:admins,email'],
            'password'=>['required'],
        ];
        try {
            $validator = Validator::make($request->all(),$rules);
            if($validator->fails())
            {
                return $this->returnValidationError($validator);
            }
            $credentials=$request->only(['email','password']);
            if(!Auth::guard('admin')->attempt($credentials))
            {
                return $this->returnError('email or password is wrong');
            }
            $admin = Auth::guard('admin')->user();
            $token = $admin->createToken('admin_token',['admin'])->plainTextToken;
            return $this->returnData('token',$token,'admin logged in successfully. ');
        }catch (\Exception $e)
        {
            return $this->returnError($e->getMessage(),$e->getCode());
        }

    }

    public function info(): JsonResponse
    {
        try {

            $admin = Auth::user();
            return $this->returnData('admin',$admin);
        }catch (\Exception $e)
        {
            return $this->returnError($e->getMessage(),$e->getCode());
        }

    }

    public function update(Request $request): JsonResponse
    {
        $rules = [
            'username'=>['min:6','string'],
            'email'=>['email','unique:admins,email'],
        ];
        try {
            $validator = Validator::make($request->all(),$rules);
            if($validator->fails())
            {
                return $this->returnValidationError($validator);
            }
            $data = [];
            $admin = Auth::user();
            if(isset($request->username))
            {
                $data['username'] = $request->username;
            }
            if(isset($request->email))
            {
                $data['email'] = $request->email;
            }
            $admin->update($data);
            return $this->returnData('admin',$admin);

        }catch (\Exception $e)
        {
            return $this->returnError($e->getMessage(),$e->getCode());
        }

    }

    public function changePassword(Request $request): JsonResponse
    {
        $rules = [
            'old_password'=>['required',new MatchPassword],
            'new_password'=>['required','confirmed',Password::min(6)->letters()->mixedCase()->symbols()->numbers()->uncompromised()],
        ];
        try {
            $validator = Validator::make($request->all(),$rules);
            if($validator->fails())
            {
                return $this->returnValidationError($validator);
            }
            $admin = Auth::user();
            $admin->update([
                'password'=>Hash::make($request->new_password)
            ]);
            return $this->returnSuccessMessage('password changed successfully. ');

        }catch (\Exception $e)
        {
            return $this->returnError($e->getMessage(),$e->getCode());
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $admin = Auth::user();
            $admin->tokens()->delete();
            return $this->returnSuccessMessage('admin logged out successfully. ');
        }catch (\Exception $e)
        {
            return $this->returnError($e->getMessage(),$e->getCode());
        }

    }

    public function destroy(Request $request)
    {

    }

}
