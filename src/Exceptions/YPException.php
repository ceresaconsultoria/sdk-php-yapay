<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Yapay\Exceptions;

use Exception;

/**
 * Description of MSException
 *
 * @author weslley
 */
class YPException extends Exception{
    
    public function __construct(Exception $ex, $completeError = false) {
        $message = $ex->getMessage(); 
        
        if($completeError){
            $message .= PHP_EOL . $ex->getTraceAsString();
        }
        
        parent::__construct($message, $ex->getCode(), $ex->getPrevious());
    }
    
    public static function fromObjectMessage($message, $code, $previous = null){
        
        if(is_object($message)){

            return new YPException( new Exception(json_encode($message), $code, $previous) );   
            
        }
        
        if(is_string($message)){
            
            return new YPException( new Exception($message, $code, $previous) );     
            
        }
        
    }
    
}
