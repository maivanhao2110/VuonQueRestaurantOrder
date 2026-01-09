<?php
/**
 * Validator Helper
 */

class Validator {
    public static function required($value, $fieldName) {
        if (empty($value) && $value !== 0 && $value !== '0') {
            return "$fieldName không được để trống";
        }
        return null;
    }
    
    public static function minLength($value, $min, $fieldName) {
        if (strlen($value) < $min) {
            return "$fieldName phải có ít nhất $min ký tự";
        }
        return null;
    }
    
    public static function positive($value, $fieldName) {
        if ($value <= 0) {
            return "$fieldName phải lớn hơn 0";
        }
        return null;
    }
    
    public static function validateArray($array, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $array[$field] ?? null;
            
            foreach ($fieldRules as $rule) {
                $error = call_user_func($rule, $value, $field);
                if ($error) {
                    $errors[$field] = $error;
                    break;
                }
            }
        }
        
        return $errors;
    }
}
?>
