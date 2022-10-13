<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait GeneralTrait
{
    public function returnError(string $message,string $code='E0000'): JsonResponse
    {
        return response()->json([
            'status'=>false,
            'code'=>$code,
            'msg'=>$message,
        ]);
    }

    public function returnSuccessMessage(string $message,string $code='S0000'): JsonResponse
    {
        return response()->json([
            'status'=>true,
            'code'=>$code,
            'msg'=>$message,
        ]);
    }

    public function returnData(string $key,$data,string $message = '',string $code='S0000'): JsonResponse
    {
        return response()->json([
            'status'=>true,
            'code'=>$code,
            'msg'=>$message,
            $key=>$data,
        ]);
    }

    public function returnValidationError($validator): JsonResponse
    {
        $code = $this->returnCodeAccordingToInput($validator);
        return $this->returnError($validator->errors()->first(),$code);
    }

    protected function returnCodeAccordingToInput($validator):string
    {
        $inputs = array_keys($validator->errors()->toArray());
        return $this->getErrorCode($inputs[0]);
    }
    protected function getErrorCode(string $input):string
    {
        if($input === 'email') return 'E0051';
        if($input === 'password') return 'E0052';
        if($input === 'f_name') return 'E0053';
        if($input === 'l_name') return 'E0054';
        if($input === 'name') return 'E0055';
        if($input === 'username') return 'E0056';
        if($input === 'phone') return 'E0057';
        return 'E0050';
    }

}
