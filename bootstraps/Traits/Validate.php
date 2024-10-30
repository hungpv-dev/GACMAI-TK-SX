<?php 
namespace AsfyCode\Traits;

use Illuminate\Database\Capsule\Manager as DB;

trait Validate
{
    protected function validateRequired($field)
    {
        if(!isset($this->data[$field]) || empty(trim($this->data[$field]))){
            $message = $this->messages['required'] ?? ':attribute là trường bắt buộc!'; 
            $this->addError($field,$message);
        }
    }

    protected function validateUnique($field,$parameters){
        $table = $parameters[0];
        $row = $parameters[1];
        $notId = isset($parameters[2]) ? $parameters[2] : false;
        if(isset($this->data[$field])){
            $col = DB::table($table)->where($row,$this->data[$field]);
            if($notId){
                $col->where('id','!=',$notId);
            }
            $col = $col->first();
            if($col){
                $message = $this->messages['unique'] ?? ':attribute đã tồn tại!'; 
                $this->addError($field,$message);
            }
        }
    }

    protected function validateEmail($field)
    {
        if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $message = $this->messages['email'] ?? ':attribute không hợp lệ!'; 
            $this->addError($field,$message);
        }
    }

    protected function validateMin($field, $parameters)
    {
        $min = (int)$parameters[0];
        if (isset($this->data[$field]) && (strlen($this->data[$field]) < $min)) {
            $message = $this->messages['min'] ?? ':attribute tối thiểu :min kí tự!'; 
            $this->addError($field,$message,['min' => $min]);
        }
    }
    protected function validateMax($field, $parameters)
    {
        $max = (int)$parameters[0];
        if (isset($this->data[$field]) && (strlen($this->data[$field]) > $max)) {
            $message = $this->messages['max'] ?? ':attribute tối thiểu :max kí tự!'; 
            $this->addError($field,$message,['max' => $max]);
        }
    }
    protected function validateLength($field, $parameters)
    {
        $length = (int)$parameters[0];
        if (isset($this->data[$field]) && (strlen($this->data[$field]) != $length) && !empty(trim($this->data[$field]))) {
            $message = $this->messages['length'] ?? ':attribute phải là :length kí tự!'; 
            $this->addError($field,$message,['length' => $length]);
        }
    }
    
    protected function validateNumber($field)
    {
        if (!is_numeric($this->data[$field]) && !empty(trim($this->data[$field]))) {
            $message = $this->messages['number'] ?? ':attribute không phải là số!'; 
            $this->addError($field,$message);
        }
    }
    protected function validateInteger($field)
    {
        if (!filter_var($this->data[$field], FILTER_VALIDATE_INT)) {
            $message = $this->messages['email'] ?? ':attribute không phải kiểu integer!'; 
            $this->addError($field,$message);
        }
    }
}