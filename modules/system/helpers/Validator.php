<?php namespace System\Helpers;

use October\Rain\Exception\ValidationException;

class Validator
{
    public static function mobile($mobile)
    {
        if(!$mobile){
            throw new ValidationException(["message" => trans("backend::lang.validator.invalid_mobile")]);
        }

        $mobile = static::numbers($mobile);

        if(substr($mobile, 0, 2) == "00"){
            $mobile = "+" . substr($mobile, 2);
        }

        if($mobile[0] == "+"){

            if($mobile[1] == "9" && $mobile[2] == "8"){

                if (preg_match('/\+989\d{9}$/', $mobile) == 0){

                    throw new ValidationException(["message" => trans("backend::lang.validator.invalid_mobile")]);
                }

                return $mobile;
            }
            else{
                return $mobile;
            }

        }
        else if($mobile[0] == "0"){

            if (preg_match('/^\b09\d{9}$/', $mobile) == 0){

                throw new ValidationException(["message" => trans("backend::lang.validator.invalid_mobile")]);
            }

            return  "+98" . substr($mobile, 1);
        }
        else{

            if (preg_match('/^\b9\d{9}$/', $mobile) == 0){

                throw new ValidationException(["message" => trans("backend::lang.validator.invalid_mobile")]);
            }

            return  "+98" . $mobile;

        }

    }

    public static function numbers($string){

        $string = str_replace('۰', '0', $string);
        $string = str_replace('۱', '1', $string);
        $string = str_replace('۲', '2', $string);
        $string = str_replace('۳', '3', $string);
        $string = str_replace('۴', '4', $string);
        $string = str_replace('٤', '4', $string);
        $string = str_replace('۵', '5', $string);
        $string = str_replace('٥', '5', $string);
        $string = str_replace('۶', '6', $string);
        $string = str_replace('٦', '6', $string);
        $string = str_replace('۷', '7', $string);
        $string = str_replace('۸', '8', $string);
        $string = str_replace('۹', '9', $string);

        return $string;
    }
}
