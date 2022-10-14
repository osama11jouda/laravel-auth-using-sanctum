<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\Admin;
use App\Models\Auth\Delivery;
use App\Rules\MatchPassword;
use App\Traits\GeneralTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class DeliveryController extends Controller
{
    use GeneralTrait;

    public function register(Request $request): JsonResponse
    {
        $rules = [
            'f_name'=>['required','min:3','string'],
            'l_name'=>['required','min:3','string'],
            'email'=>['required','email','unique:deliveries,email'],
            'phone'=>['required','integer','digits:10'],
            'password'=>['required','confirmed',Password::min(6)->letters()->mixedCase()->symbols()->numbers()->uncompromised()],
        ];
        try {
            $validator = Validator::make($request->all(),$rules);
            if($validator->fails())
            {
                return $this->returnValidationError($validator);
            }
            $delivery = Delivery::create([
                'f_name'=>$request->f_name,
                'l_name'=>$request->l_name,
                'email'=>$request->email,
                'phone'=>$request->phone,
                'password'=>Hash::make($request->password),
            ]);
            $token = $delivery->createToken('delivery_token',['delivery'])->plainTextToken;
            $delivery['token'] = $token;
            return $this->returnData('delivery',$delivery,'new delivery registered successfully. ');
        }catch (\Exception $e)
        {
            return $this->returnError($e->getMessage(),$e->getCode());
        }

    }

    public function login(Request $request): JsonResponse
    {
        $rules = [
            'email'=>['required','email','exists:deliveries,email'],
            'password'=>['required'],
        ];
        try {
            $validator = Validator::make($request->all(),$rules);
            if($validator->fails())
            {
                return $this->returnValidationError($validator);
            }
            $credentials=$request->only(['email','password']);
            if(!Auth::guard('delivery')->attempt($credentials))
            {
                return $this->returnError('email or password is wrong');
            }
            $delivery = Auth::guard('delivery')->user();
            $token = $delivery->createToken('delivery_token',['delivery'])->plainTextToken;
            return $this->returnData('token',$token,'delivery logged in successfully. ');
        }catch (\Exception $e)
        {
            return $this->returnError($e->getMessage(),$e->getCode());
        }

    }

    public function info(): JsonResponse
    {
        try {

            $delivery = Auth::user();
            return $this->returnData('delivery',$delivery);
        }catch (\Exception $e)
        {
            return $this->returnError($e->getMessage(),$e->getCode());
        }

    }

    public function update(Request $request): JsonResponse
    {
        $rules = [
            'f_name'=>['min:3','string'],
            'l_name'=>['min:3','string'],
            'email'=>['email','unique:deliveries,email'],
            'phone'=>['numeric','min:10','max:10']
        ];
        try {
            $validator = Validator::make($request->all(),$rules);
            if($validator->fails())
            {
                return $this->returnValidationError($validator);
            }
            $data = [];
            $delivery = Auth::user();
            if(isset($request->f_name))
            {
                $data['f_name'] = $request->f_name;
            }
            if(isset($request->l_name))
            {
                $data['l_name'] = $request->l_name;
            }
            if(isset($request->email))
            {
                $data['email'] = $request->email;
            }
            if(isset($request->phone))
            {
                $data['phone'] = $request->phone;
            }
            $delivery->update($data);
            return $this->returnData('delivery',$delivery);

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
            $delivery = Auth::user();
            $delivery->update([
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
            $delivery = Auth::user();
            $delivery->tokens()->delete();
            return $this->returnSuccessMessage('delivery logged out successfully. ');
        }catch (\Exception $e)
        {
            return $this->returnError($e->getMessage(),$e->getCode());
        }

    }

    public function destroy(Request $request)
    {

    }
}
